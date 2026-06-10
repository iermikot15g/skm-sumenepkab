@extends('layouts.app')

@section('title', 'Tambah OPD')
@section('header', 'Tambah OPD Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form action="{{ route('super-admin.opds.store') }}" method="POST">
            @csrf
            
            <div class="p-6 space-y-6">
                <!-- Kode OPD -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                        Kode OPD <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="code" 
                           id="code"
                           value="{{ old('code') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="DISPENDIK"
                           required>
                    <p class="text-xs text-gray-500 mt-1">Contoh: DISPENDIK, DINKES, DUKCAPIL</p>
                    @error('code')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Nama OPD -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama OPD <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name"
                           value="{{ old('name') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="Dinas Pendidikan"
                           required>
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Nama Singkat -->
                <div>
                    <label for="short_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Singkat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="short_name" 
                           id="short_name"
                           value="{{ old('short_name') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="Dispendik"
                           required>
                    @error('short_name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Kepala OPD -->
                <div>
                    <label for="head_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Kepala OPD <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="head_name" 
                           id="head_name"
                           value="{{ old('head_name') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="Dr. H. Moh. Ilyas, M.Pd"
                           required>
                    @error('head_name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- NIP Kepala OPD -->
                <div>
                    <label for="head_nip" class="block text-sm font-medium text-gray-700 mb-1">
                        NIP Kepala OPD
                    </label>
                    <input type="text" 
                           name="head_nip" 
                           id="head_nip"
                           value="{{ old('head_nip') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="197001011995031001">
                    @error('head_nip')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email OPD
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email"
                           value="{{ old('email') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="dispendik@sumenepkab.go.id">
                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Telepon -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Telepon OPD
                    </label>
                    <input type="text" 
                           name="phone" 
                           id="phone"
                           value="{{ old('phone') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="(0328) 123456">
                    @error('phone')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Status Aktif -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('super-admin.opds.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
                    Simpan OPD
                </button>
            </div>
        </form>
    </div>
</div>
@endsection