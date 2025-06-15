<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Auctioneer</title>
        @vite('resources/css/app.css')
        @livewireStyles
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->

    </head>
<body>
  <nav x-data="{ open: false }" class="p-6 bg-white">
    <div class="container mx-auto flex justify-between items-center">
      <!-- Logo -->
      <a href="/" class="text-lg font-bold">Payment</a>

      <!-- Hamburger Menu for Small Screens -->
      <button 
        @click="open = !open"
        class="block md:hidden text-gray-700 focus:outline-none"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>

      <!-- Navigation Links -->
      <div 
        :class="open ? 'block' : 'hidden'"
        class="md:flex md:items-center md:space-x-6"
      >
        <!-- Left Links -->
        <ul class="flex flex-col md:flex-row md:space-x-6 md:items-center">
          <li>
            <a href="{{ route('home') }}" class="block md:inline p-3 text-gray-700 hover:text-blue-600">Home</a>
          </li>
          <li>
            <a href="{{ route('auction') }}" class="block md:inline p-3 text-gray-700 hover:text-blue-600">Auctions</a>
          </li>
        </ul>

        <!-- Right Links -->
        <ul class="flex flex-col md:flex-row md:space-x-6 md:items-center mt-4 md:mt-0">
          <!-- Authenticated User -->
          @auth
            <li>
              <a href="#" class="block md:inline p-3 text-gray-700 hover:text-blue-600">{{ auth()->user()->name }}</a>
            </li>
            <li>
              <form action="{{ route('logout') }}" method="post" class="block md:inline p-3">
                @csrf
                <button type="submit" class="text-gray-700 hover:text-blue-600">Logout</button>
              </form>
            </li>
          @endauth

          <!-- Guest User -->
          @guest
            <li>
              <a href="{{ route('login') }}" class="block md:inline p-3 text-gray-700 hover:text-blue-600">Login</a>
            </li>
            <li>
              <a href="{{ route('register') }}" class="block md:inline p-3 text-gray-700 hover:text-blue-600">Register</a>
            </li>
          @endguest
        </ul>
      </div>
    </div>
  </nav>
@yield('content')
</body>
@livewireScripts
<!-- ====== Mega Menu End -->

</html>
