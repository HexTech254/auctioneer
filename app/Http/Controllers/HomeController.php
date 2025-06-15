<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auction;

class HomeController extends Controller
{
    public function index()
    {
        // Get all auctions for the home page
        $auctions = Auction::latest()->get();
        
        return view('home', compact('auctions'));
    }
}
