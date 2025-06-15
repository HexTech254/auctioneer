@extends('welcome') 

@section('content')
@auth
<header class="bg-white shadow">
  <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Auctions</h1>
    <a 
      href="{{ route('auctioncreate') }}"
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
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                @foreach($auctions as $auction)
              <div class="relative flex flex-col bg-clip-border rounded-xl bg-white text-gray-700 shadow-md">
                  <div class="relative bg-clip-border mx-4 rounded-xl overflow-hidden bg-white text-gray-700 shadow-lg -mt-6">
                      <div class="relative" x-data="{ activeSlide: 0 }">
                          <!-- Carousel container -->
                          <div class="relative h-48 overflow-hidden rounded-t-xl">
                              <!-- Image 1 -->
                              <div x-show="activeSlide === 0" class="absolute inset-0 transition-opacity duration-500">
                                  <img src="{{ asset('storage/' . $auction->image) }}" alt="{{ $auction->title }}" class="object-cover object-center w-full h-full" />
                              </div>
                              <!-- Image 2 -->
                              @if($auction->image2)
                              <div x-show="activeSlide === 1" class="absolute inset-0 transition-opacity duration-500">
                                  <img src="{{ asset('storage/' . $auction->image2) }}" alt="{{ $auction->title }}" class="object-cover object-center w-full h-full" />
                              </div>
                              @endif
                              <!-- Image 3 -->
                              @if($auction->image3)
                              <div x-show="activeSlide === 2" class="absolute inset-0 transition-opacity duration-500">
                                  <img src="{{ asset('storage/' . $auction->image3) }}" alt="{{ $auction->title }}" class="object-cover object-center w-full h-full" />
                              </div>
                              @endif
                          </div>

                          <!-- Navigation buttons -->
                          <div class="absolute inset-0 flex items-center justify-between p-4">
                              <button 
                                  @click="activeSlide = activeSlide === 0 ? {{ $auction->image3 ? 2 : ($auction->image2 ? 1 : 0) }} : activeSlide - 1"
                                  class="p-1 rounded-full shadow bg-white/80 text-gray-800 hover:bg-white"
                              >
                                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                  </svg>
                              </button>
                              <button 
                                  @click="activeSlide = activeSlide === {{ $auction->image3 ? 2 : ($auction->image2 ? 1 : 0) }} ? 0 : activeSlide + 1"
                                  class="p-1 rounded-full shadow bg-white/80 text-gray-800 hover:bg-white"
                              >
                                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                  </svg>
                              </button>
                          </div>

                          <!-- Indicators -->
                          <div class="absolute bottom-4 left-0 right-0">
                              <div class="flex items-center justify-center gap-2">
                                  <button 
                                      @click="activeSlide = 0"
                                      :class="{'bg-white': activeSlide === 0, 'bg-white/50': activeSlide !== 0}"
                                      class="w-2 h-2 rounded-full transition-all duration-300"
                                  ></button>
                                  @if($auction->image2)
                                  <button 
                                      @click="activeSlide = 1"
                                      :class="{'bg-white': activeSlide === 1, 'bg-white/50': activeSlide !== 1}"
                                      class="w-2 h-2 rounded-full transition-all duration-300"
                                  ></button>
                                  @endif
                                  @if($auction->image3)
                                  <button 
                                      @click="activeSlide = 2"
                                      :class="{'bg-white': activeSlide === 2, 'bg-white/50': activeSlide !== 2}"
                                      class="w-2 h-2 rounded-full transition-all duration-300"
                                  ></button>
                                  @endif
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="p-6 flex flex-col items-center">  
    <p class="text-sm text-gray-500 text-center">
        Posted by: <span class="font-semibold text-gray-700">{{ $auction->user->username ?? 'Unknown' }}</span>
    </p>

    <h6 class="text-2xl font-bold text-center text-gray-900 break-words">
        {{ Str::limit($auction->title, 50) }}
    </h6>

    <p class="text-base text-gray-600 text-center leading-relaxed break-words">
        {{ Str::limit($auction->description, 100) }}
    </p>
                      @auth
                      <div class="mt-4 flex space-x-2" x-data="{ open: false }">
                          <button @click="open = !open" class="inline-flex items-center text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 p-1.5 dark:hover-bg-gray-800 text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                              <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                              </svg>
                          </button>
                          <div x-show="open" @click.away="open = false" class="z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                              <ul class="py-1 text-sm">
                                  <li>
                                      <a href="{{ route('layouts.condition', $auction->id) }}" class="flex w-full items-center py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-gray-700 dark:text-gray-200">
                                          <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                              <path fill-rule="evenodd" clip-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" />
                                          </svg>
                                   View Details
                                </a>
                                  </li>
                                  <li>
                                      <a href="{{ route('auction.edit', $auction->id) }}" class="flex w-full items-center py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-gray-700 dark:text-gray-200">
                                          <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                              <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                              <path fill-rule="evenodd" clip-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                          </svg>
                                          Edit
                                      </a>
                                  </li>
                                  <li>
                                <form action="{{ route('auction.attend', $auction->id) }}" method="POST">
                                    @csrf
                                          <button type="submit" class="flex w-full items-center py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 text-red-500 dark:hover:text-red-400">
                                              <svg class="w-4 h-4 mr-2" viewbox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                  <path fill-rule="evenodd" clip-rule="evenodd" fill="currentColor" d="M6.09922 0.300781C5.93212 0.30087 5.76835 0.347476 5.62625 0.435378C5.48414 0.523281 5.36931 0.649009 5.29462 0.798481L4.64302 2.10078H1.59922C1.36052 2.10078 1.13161 2.1956 0.962823 2.36439C0.79404 2.53317 0.699219 2.76209 0.699219 3.00078C0.699219 3.23948 0.79404 3.46839 0.962823 3.63718C1.13161 3.80596 1.36052 3.90078 1.59922 3.90078V12.9008C1.59922 13.3782 1.78886 13.836 2.12643 14.1736C2.46399 14.5111 2.92183 14.7008 3.39922 14.7008H10.5992C11.0766 14.7008 11.5344 14.5111 11.872 14.1736C12.2096 13.836 12.3992 13.3782 12.3992 12.9008V3.90078C12.6379 3.90078 12.8668 3.80596 13.0356 3.63718C13.2044 3.46839 13.2992 3.23948 13.2992 3.00078C13.2992 2.76209 13.2044 2.53317 13.0356 2.36439C12.8668 2.1956 12.6379 2.10078 12.3992 2.10078H9.35542L8.70382 0.798481C8.62913 0.649009 8.5143 0.523281 8.37219 0.435378C8.23009 0.347476 8.06631 0.30087 7.89922 0.300781H6.09922ZM4.29922 5.70078C4.29922 5.46209 4.39404 5.23317 4.56282 5.06439C4.73161 4.8956 4.96052 4.80078 5.19922 4.80078C5.43791 4.80078 5.66683 4.8956 5.83561 5.06439C6.0044 5.23317 6.09922 5.46209 6.09922 5.70078V11.1008C6.09922 11.3395 6.0044 11.5684 5.83561 11.7372C5.66683 11.906 5.43791 12.0008 5.19922 12.0008C4.96052 12.0008 4.73161 11.906 4.56282 11.7372C4.39404 11.5684 4.29922 11.3395 4.29922 11.1008V5.70078ZM8.79922 4.80078C8.56052 4.80078 8.33161 4.8956 8.16282 5.06439C7.99404 5.23317 7.89922 5.46209 7.89922 5.70078V11.1008C7.89922 11.3395 7.99404 11.5684 8.16282 11.7372C8.33161 11.906 8.56052 12.0008 8.79922 12.0008C9.03791 12.0008 9.26683 11.906 9.43561 11.7372C9.6044 11.5684 9.69922 11.3395 9.69922 11.1008V5.70078C9.69922 5.46209 9.6044 5.23317 9.43561 5.06439C9.26683 4.8956 9.03791 4.80078 8.79922 4.80078Z" />
                                              </svg>
                                        Attend
                                    </button>
                                </form>
                                  </li>
                              </ul>
                          </div>
                            </div>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    </div>
  </main>

  @endsection

<head>
    <!-- Other head content -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>