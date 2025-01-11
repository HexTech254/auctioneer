@extends('welcome')

@section('content')
@auth
<header class="bg-white shadow">
  <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Auctions</h1>
    <a 
      href="/auction"
      class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:text-sm lg:text-base"
    >
      Back
    </a>
  </div>
</header>
<form action="{{ route('auction.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="shadow sm:rounded-md sm:overflow-hidden">
        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">

            <!-- Image Upload -->
            <div class="sm:col-span-4">
                <label for="image" class="block text-sm font-medium text-gray-900">Upload an Image</label>
                <input type="file" id="image" name="image" required
                    accept="image/*"
                    class="w-full mt-2 rounded-md border border-gray-300 p-2 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('image')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Title -->
            <div class="col-span-full">
                <label for="title" class="block text-sm font-medium text-gray-900">Title</label>
                <input type="text" id="title" name="title" required
                    class="block w-full mt-2 rounded-md border border-gray-300 p-2 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="e.g., Sale on XX/XX/2024 at ...">
                @error('title')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="col-span-full">
                <label for="description" class="block text-sm font-medium text-gray-900">Description</label>
                <textarea id="description" name="description" rows="4" required
                    class="block w-full mt-2 rounded-md border border-gray-300 p-2 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="All that property known as XXX, measuring ..."></textarea>
                @error('description')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Auction Date -->
            <div>
                <label for="auction_date" class="block text-sm font-medium text-gray-900">Date of Auction</label>
                <input type="date" name="auction_date" id="auction_date" required
                    class="block w-full mt-2 rounded-md border border-gray-300 p-2 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('auction_date')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <!-- Submit Button -->
        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            <button type="submit"
                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Save
            </button>
        </div>
    </div>
</form>
@endauth
@endsection