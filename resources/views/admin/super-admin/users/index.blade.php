@extends('layouts.app')

@section('title', 'Manajemen User')
@section('header', 'Manajemen User')

@section('content')
<div class="space-y-6">
    <!-- Tombol Tambah -->
    <div class="flex justify-end">
        <a href="{{ route('super-admin.users.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah User
        </a>
    </div>
    
    <!-- Tabel User -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">OPD</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="ml-2 inline-flex px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">Anda</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @php
                                $roleLabels = [
                                    'super_admin' => 'Super Admin',
                                    'admin_opd' => 'Admin OPD',
                                    'pimpinan_opd' => 'Pimpinan OPD',
                                ];
                                $roleColors = [
                                    'super_admin' => 'purple',
                                    'admin_opd' => 'blue',
                                    'pimpinan_opd' => 'green',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $roleColors[$user->role] }}-100 text-{{ $roleColors[$user->role] }}-800">
                                {{ $roleLabels[$user->role] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->opd->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($user->trashed())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Nonaktif
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            @if(!$user->trashed())
                                <a href="{{ route('super-admin.users.edit', $user) }}" 
                                   class="text-blue-600 hover:text-blue-900">Edit</a>
                                
                                <form action="{{ route('super-admin.users.reset-password', $user) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Reset password untuk user {{ $user->name }}?')">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900 ml-2">Reset PW</button>
                                </form>
                            @endif
                            
                            <form action="{{ route('super-admin.users.toggle-active', $user) }}" 
                                  method="POST" 
                                  class="inline">
                                @csrf
                                <button type="submit" 
                                        class="{{ $user->trashed() ? 'text-green-600 hover:text-green-900' : 'text-red-600 hover:text-red-900' }} ml-2">
                                    {{ $user->trashed() ? 'Aktifkan' : 'Nonaktifkan' }}
                                </button>
                            </form>
                            
                            @if(!$user->trashed() && $user->id !== auth()->id())
                                <form action="{{ route('super-admin.users.destroy', $user) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Hapus user {{ $user->name }} secara permanen?')">
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
                            Belum ada data user. Silahkan tambah user baru.
                         </td>
                    </tr>
                    @endforelse
                </tbody>
             </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection