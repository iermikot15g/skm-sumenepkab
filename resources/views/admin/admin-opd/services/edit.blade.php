@extends('layouts.app')

@section('title', 'Edit Jenis Layanan')
@section('header', 'Edit Jenis Layanan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form action="{{ route('admin-opd.services.update', $id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <!-- Pilih Unit -->
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Unit Layanan <span class="text-red-500">*</span>
                    </label>
                    <select name="unit_id" 
                            id="unit_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            required>
                        <option value="">Pilih Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $service['unit_id']) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Nama Layanan -->
                <div>
                    <label for="service_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Jenis Layanan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="service_name" 
                           id="service_name"
                           value="{{ old('service_name', $service['service_name']) }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           required>
                    @error('service_name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('admin-opd.services.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
                    Update Layanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection