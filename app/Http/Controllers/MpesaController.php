<?php

namespace App\Http\Controllers;

use App\Services\MpesaService;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Auction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Queue;

class MpesaController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
        $this->middleware('auth');
        $this->middleware('throttle:60,1'); // Rate limiting
    }

    public function showPaymentForm()
    {
        // Get recent transactions
        $recentTransactions = Transaction::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Set default auction_id for testing (you can modify this as needed)
        $auction_id = 1;  // or get from request: request('auction_id')
        
        return view('payment.form', [
            'recentTransactions' => $recentTransactions,
            'auction_id' => $auction_id
        ]);
    }

    public function stkPush(Request $request)
    {
        try {
            // Validate the request
        $validator = Validator::make($request->all(), [
                'phone' => 'required|regex:/^254[0-9]{9}$/',
                'amount' => 'required|numeric|min:1',
                'auction_id' => 'required|exists:auctions,id'  // Add this validation
        ]);

        if ($validator->fails()) {
                return redirect()
                    ->back()
                ->withErrors($validator)
                ->withInput();
        }

            $amount = $request->amount;
            $phone = $this->formatPhoneNumber($request->phone);
            $auctionId = $request->auction_id;  // Get the auction_id from request
            
            try {
            $response = $this->mpesaService->initiateSTKPush(
                $amount,
                $phone,
                    'TEST-'.time()
            );
            
                // Save successful transaction attempt
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                    'auction_id' => $auctionId,  // Use the validated auction_id
                'amount' => $amount,
                'transaction_id' => $response['MerchantRequestID'],
                    'status' => 'pending',
                    'transaction_details' => [
                        'phone' => $phone,
                        'response' => $response
                    ]
                ]);
            
            return redirect()
                ->route('mpesa.form')
                    ->with('warning', 'Payment initiated. Check your phone.');
            
        } catch (\Exception $e) {
                // Save failed transaction with auction_id
            Transaction::create([
                'user_id' => auth()->id(),
                    'auction_id' => $auctionId,  // Use the validated auction_id
                'amount' => $amount,
                'transaction_id' => 'FAILED-'.time(),
                'status' => 'failed',
                    'transaction_details' => [
                    'error' => $e->getMessage(),
                        'phone' => $phone
                    ]
            ]);

                return redirect()
                    ->route('mpesa.form')
                    ->with('error', 'Payment failed: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('mpesa.form')
                ->with('error', 'Invalid request data');
        }
    }

    public function callback(Request $request)
    {
        try {
            \Log::info('Mpesa Callback Received', [
                'ip' => $request->ip(),
                'data' => $request->all()
            ]);

            $response = $request->all();
            
            if(isset($response['Body']['stkCallback'])) {
                $callbackData = $response['Body']['stkCallback'];
                $merchantRequestID = $callbackData['MerchantRequestID'];
                
                $transaction = Transaction::where('transaction_id', $merchantRequestID)->first();
                    
                    if($transaction) {
                    // Check if payment was successful
                    if($callbackData['ResultCode'] == 0) {
                        $transaction->update([
                            'status' => 'completed',
                            'transaction_details' => array_merge(
                                $transaction->transaction_details ?? [],
                                [
                                    'callback_response' => $response,
                                    'completed_at' => now(),
                                    'mpesa_receipt' => $callbackData['CallbackMetadata']['Item'][1]['Value'] ?? null
                                ]
                            )
                        ]);
                    } else {
                        $transaction->update([
                            'status' => 'failed',
                            'transaction_details' => array_merge(
                                $transaction->transaction_details ?? [],
                                [
                                    'callback_response' => $response,
                                    'failure_reason' => $callbackData['ResultDesc']
                                ]
                            )
                        ]);
                    }
                }
            }

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Success'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Callback Processing Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Failed'
            ], 500);
        }
    }

    public function confirmation(Request $request)
    {
        Log::info('Confirmation Request', ['data' => $request->all()]);
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Success'
        ]);
    }

    public function validation(Request $request)
    {
        Log::info('Validation Request', ['data' => $request->all()]);
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted'
        ]);
    }

    public function showStatus(Request $request)
    {
        // Get the transaction ID from the request or session
        $transactionId = $request->transaction_id ?? session('transaction_id');
        
        if (!$transactionId) {
            return redirect()->route('home')->with('error', 'Transaction not found');
        }

        // Find the transaction
        $transaction = Transaction::where('transaction_id', $transactionId)->first();
        
        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Transaction not found');
        }

        // Determine the status
        $status = $transaction->status ?? 'failed';

        return view('payment.status', compact('status', 'transaction'));
    }

    protected function formatPhoneNumber($phone)
    {
        // Remove any spaces, dashes, or plus signs
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
} 