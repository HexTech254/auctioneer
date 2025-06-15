@extends('welcome')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Set Auction Conditions</h1>
    <p class="mb-6 text-gray-600">Please specify the conditions that must be met for this auction.</p>

    <form action="{{ route('auction.storeconditions', ['id' => $auction->id]) }}" method="POST">
        @csrf
        
        <!-- Condition 1 -->
        <div class="mb-4">
            <label for="condition1" class="block text-gray-700 font-bold mb-2">Condition 1</label>
            <input type="text" name="condition1" id="condition1" class="w-full px-3 py-2 border rounded" required>
        </div>

        <!-- Condition 2 -->
        <div class="mb-4">
            <label for="condition2" class="block text-gray-700 font-bold mb-2">Condition 2</label>
            <input type="text" name="condition2" id="condition2" class="w-full px-3 py-2 border rounded" required>
        </div>

        <!-- Condition 3 -->
        <div class="mb-4">
            <label for="condition3" class="block text-gray-700 font-bold mb-2">Condition 3</label>
            <input type="text" name="condition3" id="condition3" class="w-full px-3 py-2 border rounded" required>
        </div>

        <!-- Condition 4 -->
        <div class="mb-4">
            <label for="condition4" class="block text-gray-700 font-bold mb-2">Condition 4</label>
            <input type="text" name="condition4" id="condition4" class="w-full px-3 py-2 border rounded" required>
        </div>

        <!-- Condition 5 -->
        <div class="mb-4">
            <label for="condition5" class="block text-gray-700 font-bold mb-2">Condition 5</label>
            <input type="text" name="condition5" id="condition5" class="w-full px-3 py-2 border rounded" required>
        </div>

        <!-- Condition 6 -->
        <div class="mb-4">
            <label for="condition6" class="block text-gray-700 font-bold mb-2">Condition 6</label>
            <input type="text" name="condition6" id="condition6" class="w-full px-3 py-2 border rounded" required>
        </div>

        <!-- Condition 7 -->
        <div class="mb-4">
            <label for="condition7" class="block text-gray-700 font-bold mb-2">Condition 7</label>
            <input type="text" name="condition7" id="condition7" class="w-full px-3 py-2 border rounded" required>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Save Conditions
        </button>
    </form>
</div>
@endsection