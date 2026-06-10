@extends('layouts.app')

@section('title', 'SKM Kabupaten Sumenep - Survei Kepuasan Masyarakat')
@section('header', 'Selamat Datang')

@section('content')
<div class="space-y-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl overflow-hidden">
        <div class="px-6 py-12 md:py-16 md:px-12 text-center text-white">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
                Survei Kepuasan Masyarakat
            </h1>
            <p class="text-lg md:text-xl mb-8 text-green-100 max-w-2xl mx-auto">
                Kabupaten Sumenep berkomitmen meningkatkan kualitas pelayanan publik. 
                Suara Anda sangat berharga untuk kemajuan bersama.
            </p>
            <x-buttons.primary-button href="{{ route('survey.unit') }}" class="text-lg px-8 py-3 bg-white text-green-700 hover:bg-gray-100">
                Mulai Survei
            </x-buttons.primary-button>
        </div>
    </div>
    
    <!-- Statistik Ringkas -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Statistik Survei</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-cards.stat-card 
                title="Total Responden" 
                value="0" 
                icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                color="blue" />
            
            <x-cards.stat-card 
                title="Unit Aktif" 
                value="0" 
                icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                color="green" />
            
            <x-cards.stat-card 
                title="Rata-rata IKM" 
                value="0%" 
                icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                color="yellow" />
        </div>
    </div>
    
    <!-- Daftar Unit Layanan -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Unit Layanan Terbaru</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                // Data sementara, nanti akan diganti dengan data dari database
                $dummyUnits = [
                    ['name' => 'Dinas Pendidikan', 'code' => 'DISPENDIK'],
                    ['name' => 'Dinas Kesehatan', 'code' => 'DINKES'],
                    ['name' => 'Dinas Kependudukan', 'code' => 'DUKCAPIL'],
                    ['name' => 'Dinas Sosial', 'code' => 'DINSOS'],
                    ['name' => 'Dinas Perindustrian', 'code' => 'DISPERIN'],
                ];
            @endphp
            
            @foreach($dummyUnits as $unit)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $unit['name'] }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Kode: {{ $unit['code'] }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-2">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <x-buttons.primary-button href="#" class="w-full justify-center text-sm">
                        Pilih Unit
                    </x-buttons.primary-button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection