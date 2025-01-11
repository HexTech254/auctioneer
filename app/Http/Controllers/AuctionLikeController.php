<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Mail\AuctionLiked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuctionLikeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    
    public function store(Auction $auction, Request $request)
    {
        if ($auction->likedBy($request->user())) {
            return response(null, 409);
        }

        $auction->likes()->create([
            'user_id' => $request->user()->id,
        ]);

        if (!$auction->likes()->onlyTrashed()->where('user_id', $request->user()->id)->count()) {
            Mail::to($auction->user)->send(new AuctionLiked(auth()->user(), $auction));
        }

        return back();
    }

    public function destroy(Auction $auction, Request $request)
    {
        $request->user()->likes()->where('auction_id', $auction->id)->delete();

        return back();
    }
}
