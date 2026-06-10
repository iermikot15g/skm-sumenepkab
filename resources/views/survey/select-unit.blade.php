@extends('layouts.app')

@section('title', 'Pilih Unit Layanan - SKM Sumenep')
@section('header', 'Pilih Unit Layanan')

@section('content')
<div class="max-w-6xl mx-auto">
    
    <!-- Informasi Periode -->
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm text-blue-700">
                    <strong>Periode Survei Aktif:</strong> {{ $activePeriod->name }}
                    <br>
                    Periode: {{ $activePeriod->start_date->format('d/m/Y') }} - {{ $activePeriod->end_date->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progress Survei</span>
            <span class="text-sm font-medium text-green-600">Step 1 dari 4</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-green-600 h-2 rounded-full" style="width: 25%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span class="text-green-600 font-medium">Pilih Unit</span>
            <span>Pilih Layanan</span>
            <span>Data Diri</span>
            <span>Pertanyaan</span>
        </div>
    </div>
    
    <!-- Form Pilih Unit dengan Grid 3 Kolom -->
    <form action="{{ route('survey.post.unit') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($units as $unit)
                <x-forms.radio-card 
                    name="unit_id"
                    value="{{ $unit->id }}"
                    id="unit_{{ $unit->id }}"
                    checked="{{ old('unit_id') == $unit->id }}"
                    title="{{ $unit->name }}"
                    subtitle="Kode: {{ $unit->code }}"
                    description="{{ $unit->description }}"
                    badge="{{ $unit->respondents_count ?? 0 }} survei"
                    icon="{{ asset('images/logo-sumenep.png') }}" />
            @endforeach
        </div>
        
        @error('unit_id')
            <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
        @enderror
        
        <div class="mt-8 flex justify-end">
            <button type="submit" 
                    class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                Lanjut ke Pilih Layanan
                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </form>
    
    <!-- Tombol Kembali ke Home -->
    <div class="mt-6">
        <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Kembali ke Beranda
        </a>
    </div>
</div>
@endsection