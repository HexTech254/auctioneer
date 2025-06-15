<?php

namespace App\Http\Controllers; 

use App\Models\Auction;
use App\Models\AuctionCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AuctionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

public function index()
{
        $auctions = Auction::latest()->get();
    return view('layouts.auction', compact('auctions'));
}

    public function create()
    {
        return view('layouts.auctioncreate');
    }

public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'auction_date' => 'required|date',
            'auction_time' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        \Log::info('Validation passed', $validated);

        $auction = new Auction();
        $auction->title = $validated['title'];
        $auction->description = $validated['description'];

        // Combine date and time
        $auction->auction_date = $validated['auction_date'] . ' ' . $validated['auction_time'];

        // Set user_id from authenticated user
        $auction->user_id = auth()->id(); // or Auth::id()

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('auctions', 'public');
            $auction->image = $imagePath;
        }

        if ($request->hasFile('image2')) {
            $imagePath2 = $request->file('image2')->store('auctions', 'public');
            $auction->image2 = $imagePath2;
        }

        if ($request->hasFile('image3')) {
            $imagePath3 = $request->file('image3')->store('auctions', 'public');
            $auction->image3 = $imagePath3;
        }

        \Log::info('Before saving auction', $auction->toArray());

        $auction->save();

        \Log::info('Auction saved successfully', ['id' => $auction->id]);

        return redirect()->route('auction.conditions', ['id' => $auction->id])
            ->with('success', 'Auction created successfully. Please add conditions.');

    } catch (\Exception $e) {
        \Log::error('Failed to create auction', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()
            ->withInput()
            ->with('error', 'Failed to create auction: ' . $e->getMessage());
    }
}

    public function show($id)
    {
        $auction = Auction::findOrFail($id);
        return view('layouts.condition', compact('auction')); 
    }

    public function edit($id)
    {
        $auction = Auction::findOrFail($id);
        return view('auction.edit', compact('auction'));
    }

    public function update(Request $request, $id)
    {
        $auction = Auction::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'auction_date' => 'required|date',
            'auction_time' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Update auction details
        $auction->title = $validated['title'];
        $auction->description = $validated['description'];
    }

    public function attend(Auction $auction)
    {
        // Add your auction attendance logic here
        
        return redirect()->route('auctions.index')
            ->with('success', 'Successfully registered for the auction.');
}

public function conditions($id)
{
    $auction = Auction::findOrFail($id);
    return view('layouts.auctionconditions', compact('auction'));
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
    public function destroy($id)
{
    try {
        $auction = Auction::findOrFail($id);
        
        // Delete associated images
        if ($auction->image && Storage::exists('public/' . $auction->image)) {
            Storage::delete('public/' . $auction->image);
        }
        if ($auction->image2 && Storage::exists('public/' . $auction->image2)) {
            Storage::delete('public/' . $auction->image2);
        }
        if ($auction->image3 && Storage::exists('public/' . $auction->image3)) {
            Storage::delete('public/' . $auction->image3);
        }
        
        // Delete the auction
        $auction->delete();
        
        return redirect()->route('auction.index')
                        ->with('success', 'Auction deleted successfully');
    } catch (\Exception $e) {
        return redirect()->back()
                        ->with('error', 'Error deleting auction: ' . $e->getMessage());
    }
}
}