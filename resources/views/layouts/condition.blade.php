@extends('welcome')

@section('content')
  <header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">Conditions of sale</h1>
    </div>
  </header>
<div class="min-h-screen bg-gray-100 py-8">
  <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-8">
      <h2 class="text-2xl font-bold text-gray-800">Add Auction Conditions</h2>
      <p class="text-gray-600 mt-2">Please provide the conditions for this auction.</p>
    </div>

    <form action="{{ route('auction.storeconditions', $auction->id) }}" method="post" class="px-6 pb-8 space-y-6">
      @csrf

      @for ($i = 1; $i <= 7; $i++)
      <div class="w-full">
        <label for="condition{{ $i }}" class="block text-sm font-medium text-gray-700">
          Condition {{ $i }}
        </label>
        <input 
          type="text" 
          name="condition{{ $i }}" 
          id="condition{{ $i }}" 
          required 
          placeholder="Enter condition {{ $i }}" 
          class="mt-2 w-full p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        @error("condition$i")
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>
      @endfor

      <div class="flex justify-end">
        <button 
          type="submit" 
          class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
          Save Conditions
        </button>
      </div>
    </form>
  </div>
</div>


@endsection