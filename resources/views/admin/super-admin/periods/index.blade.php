@extends('layouts.app')

@section('title', 'Manajemen Periode Survei')
@section('header', 'Manajemen Periode Survei')

@section('content')
<div class="space-y-6">
    <!-- Tombol Tambah -->
    <div class="flex justify-end">
        <a href="{{ route('super-admin.periods.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Periode
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
    
    <!-- Tabel Periode -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi (Hari)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($periods as $period)
                    @php
                        $startDate = \Carbon\Carbon::parse($period->start_date);
                        $endDate = \Carbon\Carbon::parse($period->end_date);
                        $duration = $startDate->diffInDays($endDate) + 1;
                        $isOngoing = now()->between($startDate, $endDate);
                    @endphp
                    <tr class="{{ $period->is_active ? 'bg-green-50' : '' }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $period->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $startDate->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $endDate->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $duration }} hari</td>
                        <td class="px-6 py-4">
                            @if($period->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                    Tidak Aktif
                                </span>
                            @endif
                            @if($isOngoing && !$period->is_active)
                                <span class="inline-flex ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Sedang Berlangsung
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('super-admin.periods.edit', $period) }}" 
                               class="text-blue-600 hover:text-blue-900">Edit</a>
                            
                            @if(!$period->is_active)
                                <form action="{{ route('super-admin.periods.destroy', $period) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Hapus periode {{ $period->name }}? Semua data survei dalam periode ini juga akan terpengaruh.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data periode survei. Silahkan tambah periode baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $periods->links() }}
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
                    <br>- Hanya satu periode yang bisa aktif dalam satu waktu
                    <br>- Periode yang sudah memiliki data survei tidak dapat dihapus
                    <br>- Periode aktif akan ditampilkan di halaman survei publik
                </p>
            </div>
        </div>
    </div>
</div>
@endsection