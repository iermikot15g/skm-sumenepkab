@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    {{-- Progress Bar --}}
    <div class="mb-8">
        <div class="flex justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progress Survei</span>
            <span class="text-sm font-medium text-gray-700">Step 1 dari 4</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-green-600 h-2 rounded-full" style="width: 25%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span class="font-medium text-green-600">Pilih Unit</span>
            <span class="text-green-600">✓ Pilih Layanan</span>
            <span>Data Diri</span>
            <span>Pertanyaan</span>
            <span>Selesai</span>
        </div>
    </div>

    {{-- Form Pilih Layanan --}}
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Pilih Jenis Layanan</h2>
        <p class="text-gray-600 mb-6">Unit: <strong>{{ session('survey.unit_name') }}</strong></p>
        
        <form method="POST" action="{{ route('survey.layanan') }}">
            @csrf
            
            <div class="mb-6">
                <label for="selected_service" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Layanan <span class="text-red-500">*</span>
                </label>
                <select id="selected_service" 
                        name="selected_service" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        required>
                    <option value="">Pilih Jenis Layanan</option>
                    <option value="Administrasi">Administrasi</option>
                    <option value="Perizinan">Perizinan</option>
                    <option value="Pengaduan">Pengaduan</option>
                    <option value="Informasi">Informasi Publik</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                @error('selected_service')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-between">
                <a href="{{ route('survey.unit') }}" 
                   class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    Kembali
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    Lanjut ke Data Diri
                </button>
            </div>
        </form>
    </div>
</div>
@endsection