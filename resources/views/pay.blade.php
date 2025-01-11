<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-lg mx-auto mt-10 bg-white p-6 shadow-md rounded">
        <h1 class="text-xl font-bold mb-4">Make Payment</h1>
        
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('pay.simulate') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="phone" class="block text-gray-700">Phone Number (254xxxxxxxxx)</label>
                <input type="text" name="phone" id="phone" class="w-full p-2 border rounded" required>
                @error('phone')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="amount" class="block text-gray-700">Amount</label>
                <input type="number" name="amount" id="amount" class="w-full p-2 border rounded" required>
                @error('amount')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Pay Now</button>
        </form>
    </div>
</body>
</html>
