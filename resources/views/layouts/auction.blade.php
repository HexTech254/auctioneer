@extends('welcome') 

@section('content')
@auth
<header class="bg-white shadow">
  <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Auctions</h1>
    <a 
      href="/auctioncreate"
      class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:text-sm lg:text-base"
    >
      Add Auction
    </a>
  </div>
</header>
@endauth
@guest
<header class="bg-white shadow">
  <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Auctions</h1>
  </div>
</header>
@endguest
  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <!-- Your content -->
          <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-8">Available Auctions</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($auctions->isEmpty())
            <p class="text-center text-gray-700">No auctions available.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($auctions as $auction)
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <img src="{{ asset('storage/' . $auction->image) }}" alt="{{ $auction->title }}" class="h-40 w-full object-cover">
                        <div class="p-4">
                            <h2 class="text-xl font-bold text-gray-800">{{ $auction->title }}</h2>
                            <p class="text-gray-600 mt-2">{{ Str::limit($auction->description, 100) }}</p>
                            <p class="text-gray-700 mt-4 font-semibold">Auction Date: {{ $auction->auction_date }}</p>
                            <div class="mt-4 flex space-x-2">
                                <a href="{{ route('layouts.condition', $auction->id) }}" 
                                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                   View Details
                                </a>
                                <form action="{{ route('auction.attend', $auction->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                        Attend
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    </div>
  </main>

  @endsection