@extends('welcome') 

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-4">Payment Status</h2>
                    <div class="mb-4">
                        <p><strong>Reference:</strong> {{ $transaction->reference }}</p>
                        <p><strong>Amount:</strong> {{ number_format($transaction->amount, 2) }} KES</p>
                        <p><strong>Status:</strong> {{ ucfirst($transaction->status) }}</p>
                        @if($transaction->mpesa_receipt_number)
                            <p><strong>M-Pesa Receipt:</strong> {{ $transaction->mpesa_receipt_number }}</p>
                        @endif
                    </div>
                    @if($transaction->status === 'pending')
                        <p class="text-yellow-600">Your payment is being processed. Please complete the transaction on your phone.</p>
                    @elseif($transaction->status === 'completed')
                        <p class="text-green-600">Your payment has been successfully processed. Thank you!</p>
                    @else
                        <p class="text-red-600">There was an issue with your payment. Please try again or contact support.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

  @endsection