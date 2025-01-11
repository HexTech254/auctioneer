<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        return view('payment.form');
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^254[0-9]{9}$/',
            'amount' => 'required|numeric|min:1',
        ]);

        $transaction = Transaction::create([
            'phone' => $request->phone,
            'amount' => $request->amount,
            'reference' => 'TRX' . time(),
        ]);

        try {
            $accessToken = $this->getAccessToken();
            $response = $this->sendStkPush($accessToken, $transaction);

            if ($response->successful()) {
                return redirect()->route('payment.status', $transaction->id)->with('success', 'Payment initiated. Please complete the transaction on your phone.');
            } else {
                Log::error('STK Push failed', ['response' => $response->json()]);
                return back()->withErrors(['message' => 'Failed to initiate payment. Please try again.']);
            }
        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['message' => 'An error occurred while processing your request. Please try again later.']);
        }
    }

    private function getAccessToken()
    {
        $consumerKey = config('services.mpesa.consumer_key');
        $consumerSecret = config('services.mpesa.consumer_secret');

        $response = Http::withBasicAuth($consumerKey, $consumerSecret)
            ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

        if ($response->successful()) {
            return $response->json()['access_token'];
        } else {
            Log::error('Failed to get access token', ['response' => $response->json()]);
            throw new \Exception('Failed to get access token');
        }
    }

    private function sendStkPush($accessToken, $transaction)
    {
        $timestamp = now()->format('YmdHis');
        $shortcode = config('services.mpesa.shortcode');
        $passkey = config('services.mpesa.passkey');
        $password = base64_encode($shortcode . $passkey . $timestamp);

        return Http::withToken($accessToken)
            ->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $transaction->amount,
                'PartyA' => $transaction->phone,
                'PartyB' => $shortcode,
                'PhoneNumber' => $transaction->phone,
                'CallBackURL' => route('payment.callback'),
                'AccountReference' => $transaction->reference,
                'TransactionDesc' => 'Payment for goods/services',
            ]);
    }

    public function handleCallback(Request $request)
    {
        Log::info('M-Pesa callback received', ['data' => $request->all()]);

        $callbackData = $request->json()->all();

        if (isset($callbackData['Body']['stkCallback']['CallbackMetadata'])) {
            $metadata = collect($callbackData['Body']['stkCallback']['CallbackMetadata']['Item']);

            $mpesaReceiptNumber = $metadata->firstWhere('Name', 'MpesaReceiptNumber')['Value'];
            $amount = $metadata->firstWhere('Name', 'Amount')['Value'];
            $phoneNumber = $metadata->firstWhere('Name', 'PhoneNumber')['Value'];

            $transaction = Transaction::where('phone', $phoneNumber)
                ->where('amount', $amount)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'completed',
                    'mpesa_receipt_number' => $mpesaReceiptNumber,
                ]);
            } else {
                Log::warning('Transaction not found for callback', ['data' => $callbackData]);
            }
        } else {
            Log::error('Invalid callback data structure', ['data' => $callbackData]);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    public function showPaymentStatus($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('payment.status', compact('transaction'));
    }
}

