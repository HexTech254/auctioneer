@extends('welcome')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Create New Auction</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
  </div>
    @endif

<form action="{{ route('auction.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
            <input type="text" 
                   name="title" 
                   id="title" 
                   value="{{ old('title') }}"
                   class="w-full px-3 py-2 border rounded @error('title') border-red-500 @enderror break-words" 
                   maxlength="255"
                   required>
            <p class="text-sm text-gray-500 mt-1">Maximum 255 characters</p>
            @error('title')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea 
                name="description" 
                id="description" 
                rows="4" 
                class="w-full px-3 py-2 border rounded @error('description') border-red-500 @enderror break-words" 
                style="white-space: pre-wrap; word-wrap: break-word;"
                required>{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="auction_date" class="block text-gray-700 font-bold mb-2">Auction Date</label>
                <input type="date" 
                       name="auction_date" 
                       id="auction_date" 
                       value="{{ old('auction_date') }}"
                       class="w-full px-3 py-2 border rounded @error('auction_date') border-red-500 @enderror" 
                       required>
                @error('auction_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="auction_time" class="block text-gray-700 font-bold mb-2">Auction Time</label>
                <input type="time" 
                       name="auction_time" 
                       id="auction_time" 
                       value="{{ old('auction_time') }}"
                       class="w-full px-3 py-2 border rounded @error('auction_time') border-red-500 @enderror" 
                       required>
                @error('auction_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Images</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="image" class="block text-sm text-gray-600">Primary Image (Required)</label>
                    <input type="file" 
                           name="image" 
                           id="image" 
                           class="w-full px-3 py-2 border rounded @error('image') border-red-500 @enderror"
                           accept="image/*"
                           required>
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="image2" class="block text-sm text-gray-600">Second Image (Optional)</label>
                    <input type="file" 
                           name="image2" 
                           id="image2" 
                           class="w-full px-3 py-2 border rounded @error('image2') border-red-500 @enderror"
                           accept="image/*">
                    @error('image2')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="image3" class="block text-sm text-gray-600">Third Image (Optional)</label>
                    <input type="file" 
                           name="image3" 
                           id="image3" 
                           class="w-full px-3 py-2 border rounded @error('image3') border-red-500 @enderror"
                           accept="image/*">
                    @error('image3')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Create Auction
            </button>
        </div>
    </form>
    </div>
@endsection