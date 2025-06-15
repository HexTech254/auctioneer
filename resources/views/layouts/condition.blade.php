@extends('welcome')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Auction Details Header -->
        <div class="p-6 bg-gray-50 border-b">
            <h1 class="text-2xl font-bold text-gray-900">{{ $auction->title }}</h1>
            <p class="mt-2 text-gray-600">
                Auction Date: {{ \Carbon\Carbon::parse($auction->auction_date)->format('F j, Y g:i A') }}
            </p>
        </div>

        <!-- Image Carousel -->
        <div class="relative" x-data="{ activeSlide: 0 }">
            <div class="relative h-96">
                <!-- Primary Image -->
                <div x-show="activeSlide === 0" class="absolute inset-0">
                    <img src="{{ asset('storage/' . $auction->image) }}" alt="{{ $auction->title }}" class="w-full h-full object-cover">
                </div>
                
                <!-- Second Image (if exists) -->
                @if($auction->image2)
                <div x-show="activeSlide === 1" class="absolute inset-0">
                    <img src="{{ asset('storage/' . $auction->image2) }}" alt="{{ $auction->title }}" class="w-full h-full object-cover">
                </div>
                @endif
                
                <!-- Third Image (if exists) -->
                @if($auction->image3)
                <div x-show="activeSlide === 2" class="absolute inset-0">
                    <img src="{{ asset('storage/' . $auction->image3) }}" alt="{{ $auction->title }}" class="w-full h-full object-cover">
                </div>
                @endif
            </div>
            
            <!-- Navigation Arrows -->
            @if($auction->image2 || $auction->image3)
            <div class="absolute inset-0 flex items-center justify-between p-4">
                <button @click="activeSlide = activeSlide === 0 ? {{ $auction->image3 ? 2 : ($auction->image2 ? 1 : 0) }} : activeSlide - 1" 
                        class="p-2 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-75">
                    ←
                </button>
                <button @click="activeSlide = activeSlide === {{ $auction->image3 ? 2 : ($auction->image2 ? 1 : 0) }} ? 0 : activeSlide + 1"
                        class="p-2 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-75">
                    →
                </button>
            </div>
            @endif
        </div>

        <!-- Auction Details -->
        <div class="p-6">
            <div class="prose max-w-none">
                <h2 class="text-xl font-semibold mb-4">Description</h2>
                <p class="text-gray-700">{{ $auction->description }}</p>
            </div>

            <!-- Conditions Section -->
            @if($auction->conditions)
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Conditions of Sale</h2>
                <ul class="list-disc pl-5 space-y-2">
                    @foreach(json_decode($auction->conditions) as $condition)
                        <li class="text-gray-700">{{ $condition }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-8 flex space-x-4">
                <form action="{{ route('auction.destroy', ['id' => $auction->id]) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" onclick="return confirm('Are you sure you want to delete this auction?')">
                        Delete Auction
                    </button>
                </form>
                
                <a href="{{ route('auction') }}" class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Back to Auctions
                </a>
            </div>

            <!-- Add username display -->

        </div>
    </div>
</div>
@endsection