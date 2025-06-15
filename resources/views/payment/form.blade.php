@extends('welcome') 

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-8">
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">
                Payment Details
            </h2>

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('mpesa.stkpush') }}" class="space-y-6">
                        @csrf
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        Phone Number
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               name="phone" 
                               id="phone" 
                               placeholder="254XXXXXXXXX"
                               value="{{ old('phone') }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               required>
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Format: 254XXXXXXXXX</p>
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">
                        Amount (KES)
                    </label>
                    <div class="mt-1">
                        <input type="number" 
                               name="amount" 
                               id="amount" 
                               min="1"
                               value="{{ old('amount') }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               required>
                        </div>
                    @error('amount')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                        </div>

                <input type="hidden" name="auction_id" value="{{ $auction_id ?? request('auction_id', 1) }}">

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Pay with M-Pesa
                            </button>
                        </div>
                    </form>

            @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Transactions</h3>
                    <div class="space-y-4">
                        @foreach($recentTransactions as $transaction)
                            <div class="border rounded-lg p-4 {{ 
                                $transaction->status === 'completed' ? 'border-green-200 bg-green-50' :
                                ($transaction->status === 'failed' ? 'border-red-200 bg-red-50' : 'border-yellow-200 bg-yellow-50')
                            }}">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            Amount: KES {{ number_format($transaction->amount, 2) }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $transaction->created_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ 
                                        $transaction->status === 'completed' ? 'bg-green-100 text-green-800' :
                                        ($transaction->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')
                                    }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </div>
                                @if($transaction->status === 'failed' && isset($transaction->transaction_details['failure_reason']))
                                    <p class="text-xs text-red-600 mt-2">
                                        {{ $transaction->transaction_details['failure_reason'] }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
  @endsection
