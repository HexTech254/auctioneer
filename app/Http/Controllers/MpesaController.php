<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Transaction;
use Illuminate\Http\Request;

class MpesaController
{
    public function stkPush(Request $request)
    {
        $auction = Auction::findOrFail($request->auction_id);
        
        // Initialize Daraja API
        $mpesa = new \Safaricom\Mpesa\Mpesa();
        
        try {
            $response = $mpesa->STKPush([
                'amount' => $request->amount,
                'phone' => auth()->user()->phone, // Ensure you have phone number in users table
                'reference' => 'Auction-'.$auction->id,
                'description' => 'Auction Registration Fee'
            ]);
            
            // Store transaction details
            Transaction::create([
                'user_id' => auth()->id(),
                'auction_id' => $auction->id,
                'amount' => $request->amount,
                'transaction_id' => $response->MerchantRequestID,
                'status' => 'pending'
            ]);
            
            return back()->with('success', 'Payment initiated. Please complete the payment on your phone.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Payment failed. Please try again.');
        }
    }
} 