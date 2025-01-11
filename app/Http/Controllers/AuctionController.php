<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuctionController extends Controller
{

public function index()
{
    $auctions = Auction::all(); // Fetch all auctions
    return view('layouts.auction', compact('auctions'));
}

    public function auctioncreate() 
    {
        return view('layouts.auctioncreate');
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'image' => 'required|image|max:2048',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'auction_date' => 'required|date',
    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('images', 'public');
        $validated['image'] = $path;
    }

    // Create the auction and get the instance
    $auction = Auction::create($validated);

    // Redirect to conditions form with the auction ID
    return redirect()->route('layouts.condition', ['id' => $auction->id])
        ->with('success', 'Auction saved successfully! Please add conditions.');
}

public function conditions($id)
{
    $auction = Auction::findOrFail($id);

    return view('layouts.condition', compact('auction'));
}

public function storeconditions(Request $request, $id)
{
    $validated = $request->validate([
        'condition1' => 'required|string|max:255',
        'condition2' => 'nullable|string|max:255',
        'condition3' => 'nullable|string|max:255',
        'condition4' => 'nullable|string|max:255',
        'condition5' => 'nullable|string|max:255',
        'condition6' => 'nullable|string|max:255',
        'condition7' => 'nullable|string|max:255',
    ]);

    $auction = Auction::findOrFail($id);

    // Store non-empty conditions as a JSON array
    $conditions = array_filter($validated); // Remove null values
    $auction->conditions = json_encode($conditions);
    $auction->save();

    return redirect()->route('auction')
        ->with('success', 'Auction updated successfully!');
}

    public function simulate(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^254[0-9]{9}$/',
            'amount' => 'required|numeric|min:1',
        ]);

        $transaction = Transaction::create([
            'phone' => $request->phone,
            'amount' => $request->amount,
        ]);

        $shortcode = 'YOUR_SHORTCODE';
        $passkey = 'YOUR_PASSKEY';
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);
        $access_token = $this->getAccessToken();

        $response = Http::withToken($access_token)->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $request->amount,
            'PartyA' => $request->phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $request->phone,
            'CallBackURL' => route('pay.callback'),
            'AccountReference' => 'Test',
            'TransactionDesc' => 'Payment',
        ]);

        return redirect()->back()->with('success', 'Payment initiated. Please complete on your phone.');
    }

    public function callback(Request $request)
    {
        $data = $request->get('Body')['stkCallback'];

        if ($data['ResultCode'] === 0) {
            $transaction = Transaction::where('transaction_id', $data['CheckoutRequestID'])->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'success',
                    'transaction_id' => $data['MpesaReceiptNumber'],
                ]);
            }
        } else {
            $transaction = Transaction::where('transaction_id', $data['CheckoutRequestID'])->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'failed',
                ]);
            }
        }

        return response()->json(['message' => 'Callback processed successfully']);
    }

    private function getAccessToken()
    {
        $consumer_key = 'YOUR_CONSUMER_KEY';
        $consumer_secret = 'YOUR_CONSUMER_SECRET';

        $response = Http::withBasicAuth($consumer_key, $consumer_secret)
            ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

        return $response->json()['access_token'];
    }

public function attend($id)
{
    // Logic for attending the auction
    return redirect()->back()->with('success', 'You have successfully attended the auction!');
}

}