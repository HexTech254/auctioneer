<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:60,1'); // Rate limiting
    }

    public function showPaymentForm()
    {
        $recentTransactions = Transaction::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('payment.form', compact('recentTransactions'));
    }

    public function initiatePayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|regex:/^254[0-9]{9}$/',
                'amount' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Format phone number
            $phone = $this->formatPhoneNumber($request->phone);
            
            // Get access token
            $accessToken = $this->getAccessToken();

            // Generate timestamp
            $timestamp = date('YmdHis');
            
            // Create transaction reference
            $transactionRef = 'TRX' . $timestamp . rand(1000, 9999);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'phone' => $phone,
                'reference' => $transactionRef,
                'status' => 'pending'
            ]);

            // Initiate STK Push
            $response = $this->initiateSTKPush($accessToken, $transaction);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['ResponseCode']) && $result['ResponseCode'] === '0') {
                    // Update transaction with checkout request ID
                    $transaction->update([
                        'checkout_request_id' => $result['CheckoutRequestID'],
                        'merchant_request_id' => $result['MerchantRequestID']
                    ]);

                    return redirect()
                        ->route('payment.status', ['id' => $transaction->id])
                        ->with('success', 'Please complete the payment on your phone.');
                }
            }

            throw new \Exception($response['errorMessage'] ?? 'STK push failed');

        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to initiate payment: ' . $e->getMessage());
        }
    }

    private function getAccessToken()
    {
        try {
            $consumer_key = config('services.mpesa.consumer_key');
            $consumer_secret = config('services.mpesa.consumer_secret');

            if (empty($consumer_key) || empty($consumer_secret)) {
                throw new \Exception('M-Pesa credentials not configured');
            }

            $credentials = base64_encode($consumer_key . ':' . $consumer_secret);

            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 30,
            ])->withHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json',
            ])->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

            if ($response->failed()) {
                throw new \Exception('Failed to generate access token');
            }

            $result = $response->json();
            
            if (!isset($result['access_token'])) {
                throw new \Exception('Access token not found in response');
            }

            return $result['access_token'];

        } catch (\Exception $e) {
            Log::error('Access token generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function initiateSTKPush($accessToken, $transaction)
    {
        try {
            $timestamp = date('YmdHis');
            $shortcode = config('services.mpesa.shortcode');
            $passkey = config('services.mpesa.passkey');
            
            if (empty($shortcode) || empty($passkey)) {
                throw new \Exception('M-Pesa configuration not set');
            }

            $password = base64_encode($shortcode . $passkey . $timestamp);

            Log::info('Initiating STK Push', [
                'phone' => $transaction->phone,
                'amount' => $transaction->amount,
                'reference' => $transaction->reference
            ]);

            return Http::withOptions([
                'verify' => false,
                'timeout' => 30,
            ])->withToken($accessToken)
                ->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                    'BusinessShortCode' => $shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => (int) $transaction->amount,
                    'PartyA' => $transaction->phone,
                    'PartyB' => $shortcode,
                    'PhoneNumber' => $transaction->phone,
                    'CallBackURL' => route('payment.callback'),
                    'AccountReference' => $transaction->reference,
                    'TransactionDesc' => 'Payment'
                ]);

        } catch (\Exception $e) {
            Log::error('STK Push failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transaction->id
            ]);
            throw $e;
        }
    }

    public function callback(Request $request)
    {
        try {
            Log::info('M-Pesa Callback Received', [
                'data' => $request->all()
            ]);

            $callbackData = $request->Body['stkCallback'] ?? null;
            
            if (!$callbackData) {
                throw new \Exception('Invalid callback data');
            }

            $transaction = Transaction::where('checkout_request_id', $callbackData['CheckoutRequestID'])->first();
            
            if (!$transaction) {
                throw new \Exception('Transaction not found');
            }

            if ($callbackData['ResultCode'] == 0) {
                // Payment successful
                $transaction->update([
                    'status' => 'completed',
                    'mpesa_receipt_number' => $this->extractReceiptNumber($callbackData),
                    'payment_date' => now(),
                ]);
            } else {
                // Payment failed
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => $callbackData['ResultDesc']
                ]);
            }

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);

        } catch (\Exception $e) {
            Log::error('Callback Processing Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Failed']);
        }
    }

    public function showStatus($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.status', compact('transaction'));
    }

    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If the number starts with 0, replace it with 254
        if (substr($phone, 0, 1) == '0') {
            $phone = '254' . substr($phone, 1);
        }
        
        // If the number doesn't start with 254, add it
        if (substr($phone, 0, 3) != '254') {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }

    private function extractReceiptNumber($callbackData)
    {
        if (isset($callbackData['CallbackMetadata']['Item'])) {
            foreach ($callbackData['CallbackMetadata']['Item'] as $item) {
                if ($item['Name'] === 'MpesaReceiptNumber') {
                    return $item['Value'];
                }
            }
        }
        return null;
    }
}