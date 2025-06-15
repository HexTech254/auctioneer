@extends('welcome')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Edit Auction</h1>

    <form action="{{ route('auction.update', ['id' => $auction->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
            <input type="text" name="title" id="title" class="w-full px-3 py-2 border rounded" 
                   value="{{ old('title', $auction->title) }}" required>
        </div>
        
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border rounded" required>{{ old('description', $auction->description) }}</textarea>
        </div>
        
        <div class="mb-4">
            <label for="auction_date" class="block text-gray-700 font-bold mb-2">Auction Date</label>
            <input type="date" name="auction_date" id="auction_date" class="w-full px-3 py-2 border rounded" 
                   value="{{ old('auction_date', date('Y-m-d', strtotime($auction->auction_date))) }}" required>
        </div>
        
        <div class="mb-4">
            <label for="auction_time" class="block text-gray-700 font-bold mb-2">Auction Time</label>
            <input type="time" name="auction_time" id="auction_time" class="w-full px-3 py-2 border rounded" 
                   value="{{ old('auction_time', date('H:i', strtotime($auction->auction_date))) }}" required>
        </div>
        
        <!-- Image 1 -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Current Primary Image</label>
            @if($auction->image)
                <img src="{{ asset('storage/' . $auction->image) }}" alt="Primary Image" class="w-32 h-32 object-cover">
            @else
                <p>No primary image</p>
            @endif
            
            <label for="image" class="block text-gray-700 font-bold mt-2">Change Primary Image</label>
            <input type="file" name="image" id="image" class="w-full px-3 py-2 border rounded">
        </div>

        <!-- Image 2 -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Current Second Image</label>
            @if($auction->image2)
                <img src="{{ asset('storage/' . $auction->image2) }}" alt="Second Image" class="w-32 h-32 object-cover">
            @else
                <p>No second image</p>
            @endif
            
            <label for="image2" class="block text-gray-700 font-bold mt-2">Change Second Image</label>
            <input type="file" name="image2" id="image2" class="w-full px-3 py-2 border rounded">
        </div>

        <!-- Image 3 -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Current Third Image</label>
            @if($auction->image3)
                <img src="{{ asset('storage/' . $auction->image3) }}" alt="Third Image" class="w-32 h-32 object-cover">
            @else
                <p>No third image</p>
            @endif
            
            <label for="image3" class="block text-gray-700 font-bold mt-2">Change Third Image</label>
            <input type="file" name="image3" id="image3" class="w-full px-3 py-2 border rounded">
        </div>
        
        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Update Auction
        </button>
    </form>
</div>
@endsection