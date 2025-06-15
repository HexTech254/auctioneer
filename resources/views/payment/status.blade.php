@extends('welcome')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-8">
            <div class="text-center">
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        {{ session('error') }}
                    </div>
                @endif

                @php
                    // Default to 'pending' if status is not set
                    $currentStatus = $status ?? session('status', 'pending');
                @endphp

                @switch($currentStatus)
                    @case('pending')
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                            <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Pending</h2>
                        <p class="text-gray-600 mb-6">Please complete the payment on your phone.</p>
                        
                        <!-- Add auto-refresh for pending status -->
                        <script>
                            setTimeout(function() {
                                window.location.reload();
                            }, 5000); // Refresh every 5 seconds
                        </script>
                        @break

                    @case('completed')
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful</h2>
                        <p class="text-gray-600 mb-6">Thank you for your payment!</p>
                        @break

                    @default
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Failed</h2>
                        <p class="text-gray-600 mb-6">Something went wrong with your payment.</p>
                @endswitch

                @if(isset($transaction))
                    <div class="bg-gray-50 rounded-lg p-6 mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction Details</h3>
                        <div class="space-y-3 text-left">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Amount:</span>
                                <span class="text-gray-900 font-medium">KES {{ number_format($transaction->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Reference:</span>
                                <span class="text-gray-900 font-medium">{{ $transaction->transaction_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Date:</span>
                                <span class="text-gray-900 font-medium">{{ $transaction->created_at->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-8">
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection