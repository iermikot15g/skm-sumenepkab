@extends('layouts.app')

@section('title', 'Manajemen OPD')
@section('header', 'Manajemen OPD')

@section('content')
<div class="space-y-6">
    <!-- Tombol Tambah -->
    <div class="flex justify-end">
        <a href="{{ route('super-admin.opds.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah OPD
        </a>
    </div>
    
    <!-- Tabel OPD -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama OPD</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Singkat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kepala OPD</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jml Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($opds as $opd)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $opd->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $opd->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $opd->short_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $opd->head_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $opd->units_count }}</td>
                        <td class="px-6 py-4">
                            @if($opd->is_active)
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
                            <a href="{{ route('super-admin.opds.edit', $opd) }}" 
                               class="text-blue-600 hover:text-blue-900">Edit</a>
                            <form action="{{ route('super-admin.opds.destroy', $opd) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Hapus OPD {{ $opd->name }}? Semua unit dalam OPD ini juga akan terpengaruh.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data OPD. Silahkan tambah OPD baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $opds->links() }}
        </div>
    </div>
</div>
@endsection