<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'SKM Kabupaten Sumenep') - Survei Kepuasan Masyarakat</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen">
        
        <!-- Top Navigation Bar -->
        <nav class="bg-white border-b border-gray-200 fixed top-0 w-full z-50">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    
                    <!-- Left side: Logo & Hamburger (mobile) -->
                    <div class="flex items-center">
                        <!-- Mobile hamburger button -->
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center ml-4 lg:ml-0">
                            <img class="h-8 w-auto" src="{{ asset('images/logo-sumenep.png') }}" alt="Logo Sumenep">
                            <span class="ml-2 text-lg font-semibold text-gray-900 hidden sm:inline">
                                SKM Sumenep
                            </span>
                        </div>
                    </div>
                    
                    <!-- Right side: User menu -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900">
                                    <div class="h-8 w-8 rounded-full bg-green-600 flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">
                                            {{ substr(Auth::user()->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <!-- Dropdown menu -->
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('super-admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Dashboard Admin
                                        </a>
                                    @elseif(auth()->user()->isAdminOpd())
                                        <a href="{{ route('admin-opd.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Dashboard Admin OPD
                                        </a>
                                    @elseif(auth()->user()->isPimpinanOpd())
                                        <a href="{{ route('pimpinan.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Dashboard Pimpinan
                                        </a>
                                    @endif
                                    
                                    <div class="border-t border-gray-100 my-1"></div>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="inline mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Login</a>
                            <a href="{{ route('register') }}" class="text-sm bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                Register
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar (Mobile overlay) -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 flex z-40 lg:hidden" @click.away="sidebarOpen = false">
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
            
            <div x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-300" 
                 x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300" 
                 x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                 class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Sidebar content mobile -->
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    @include('layouts.sidebar')
                </div>
            </div>
        </div>

        <!-- Desktop sidebar -->
        <div class="hidden lg:flex lg:fixed lg:inset-y-0 lg:mt-16" :class="sidebarCollapsed ? 'lg:w-20' : 'lg:w-64'">
            <div class="flex flex-col flex-grow bg-white border-r border-gray-200 pt-5 pb-4 overflow-y-auto">
                <div class="flex-grow flex flex-col">
                    <!-- Toggle collapse button -->
                    <div class="px-3 mb-4">
                        <button @click="sidebarCollapsed = !sidebarCollapsed" class="w-full flex items-center justify-center px-2 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="ml-2">Collapse</span>
                            <span x-show="sidebarCollapsed" class="ml-2">Expand</span>
                        </button>
                    </div>
                    
                    @include('layouts.sidebar')
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64 mt-16" :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'">
            <main class="py-6 px-4 sm:px-6 lg:px-8">
                <!-- Page header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">@yield('header')</h1>
                </div>
                
                <!-- Flash messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Content -->
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 mt-8">
                <div class="py-4 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500">
                        &copy; {{ date('Y') }} Pemerintah Kabupaten Sumenep - Survei Kepuasan Masyarakat
                        <br>
                        <span class="text-xs">Berlandaskan Permen PANRB No. 14 Tahun 2017</span>
                    </p>
                </div>
            </footer>
        </div>
    </div>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('scripts')
</body>
</html>