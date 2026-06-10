@extends('layouts.app')

@section('title', 'Pilih Layanan - SKM Sumenep')
@section('header', 'Pilih Jenis Layanan')

@section('content')
<div class="max-w-2xl mx-auto">
    
    <!-- Info Unit yang Dipilih -->
    <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Unit yang dipilih:</p>
                <p class="font-medium text-gray-900">{{ $unit->name }}</p>
                <p class="text-xs text-gray-400">Kode: {{ $unit->code }}</p>
            </div>
            <a href="{{ route('survey.unit') }}" class="text-sm text-green-600 hover:text-green-700">
                Ubah Unit
            </a>
        </div>
    </div>
    
    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progress Survei</span>
            <span class="text-sm font-medium text-green-600">Step 2 dari 4</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-green-600 h-2 rounded-full" style="width: 50%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span class="text-green-600 font-medium">✓ Pilih Unit</span>
            <span class="text-green-600 font-medium">Pilih Layanan</span>
            <span>Data Diri</span>
            <span>Pertanyaan</span>
        </div>
    </div>
    
    <!-- Form Pilih Layanan -->
    <form action="{{ route('survey.post.service') }}" method="POST">
        @csrf
        
        <div class="space-y-3">
            @foreach($services as $service)
                <x-forms.radio-card 
                    name="selected_service"
                    value="{{ $service }}"
                    id="service_{{ Str::slug($service) }}"
                    checked="{{ old('selected_service') == $service }}"
                    title="{{ $service }}"
                    description="Layanan yang tersedia di {{ $unit->name }}" />
            @endforeach
        </div>
        
        @error('selected_service')
            <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
        @enderror
        
        <!-- Tombol Navigasi -->
        <div class="mt-8 flex justify-between">
            <a href="{{ route('survey.unit') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 transition">
                <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
            
            <button type="submit" 
                    class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition">
                Lanjut ke Data Diri
                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection