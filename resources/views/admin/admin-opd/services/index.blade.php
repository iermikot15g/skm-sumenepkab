@extends('layouts.app')

@section('title', 'Manajemen Jenis Layanan')
@section('header', 'Manajemen Jenis Layanan')

@section('content')
<div class="space-y-6">
    <!-- Tombol Tambah -->
    <div class="flex justify-end">
        <a href="{{ route('admin-opd.services.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Jenis Layanan
        </a>
    </div>
    
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif
    
    <!-- Tabel Jenis Layanan -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($services as $index => $service)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @php
                                $unit = \App\Models\Unit::find($service['unit_id']);
                            @endphp
                            {{ $unit->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $service['service_name'] }}</td>
                        <td class="px-6 py-4">
                            @if($service['is_active'])
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin-opd.services.edit', $index) }}" 
                               class="text-blue-600 hover:text-blue-900">Edit</a>
                            
                            <form action="{{ route('admin-opd.services.toggle-active', $index) }}" 
                                  method="POST" 
                                  class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    {{ $service['is_active'] ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            
                            <form action="{{ route('admin-opd.services.destroy', $index) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Hapus jenis layanan {{ $service['service_name'] }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data jenis layanan. Silahkan tambah layanan baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Informasi -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Catatan:</strong>
                    <br>- Jenis layanan yang aktif akan ditampilkan di halaman survei
                    <br>- Anda dapat menambah, mengedit, atau menghapus jenis layanan sesuai kebutuhan
                </p>
            </div>
        </div>
    </div>
</div>
@endsection