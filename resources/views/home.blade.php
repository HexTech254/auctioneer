@extends('welcome')

@section('content')
<div class="bg-white">
    <div class="relative isolate px-6 pt-14 lg:px-8">
        <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                    Welcome to Online Auction Platform
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    Discover unique items and participate in exciting auctions from the comfort of your home.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    @auth
                        <a href="{{ route('auction') }}" class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            View Auctions
                        </a>
                        <a href="{{ route('auctioncreate') }}" class="text-sm font-semibold leading-6 text-gray-900">
                            Create Auction <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold leading-6 text-gray-900">
                            Register <span aria-hidden="true">→</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Auctions Section -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900 mb-6">Featured Auctions</h2>
        
        @if($auctions->isEmpty())
            <p class="text-center text-gray-500">No auctions available at the moment.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($auctions->take(4) as $auction)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="{{ asset('storage/' . $auction->image) }}" alt="{{ $auction->title }}" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $auction->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($auction->description, 100) }}</p>
                            <div class="mt-4">
                                <a href="{{ route('layouts.condition', $auction->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($auctions->count() > 4)
                <div class="text-center mt-8">
                    <a href="{{ route('auction') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                        View All Auctions
                    </a>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection 