@extends('layouts.app')

@section('title', 'Tambah User')
@section('header', 'Tambah User Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form action="{{ route('super-admin.users.store') }}" method="POST">
            @csrf
            
            <div class="p-6 space-y-6">
                <!-- Nama -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name"
                           value="{{ old('name') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           required>
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email"
                           value="{{ old('email') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           required>
                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role" 
                            id="role"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            required>
                        <option value="">Pilih Role</option>
                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin_opd" {{ old('role') == 'admin_opd' ? 'selected' : '' }}>Admin OPD</option>
                        <option value="pimpinan_opd" {{ old('role') == 'pimpinan_opd' ? 'selected' : '' }}>Pimpinan OPD</option>
                    </select>
                    @error('role')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- OPD (conditional) -->
                <div id="opd_container" style="display: {{ in_array(old('role'), ['admin_opd', 'pimpinan_opd']) ? 'block' : 'none' }};">
                    <label for="opd_id" class="block text-sm font-medium text-gray-700 mb-1">
                        OPD <span class="text-red-500">*</span>
                    </label>
                    <select name="opd_id" 
                            id="opd_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Pilih OPD</option>
                        @php
                            $opds = \App\Models\Opd::where('is_active', true)->get();
                        @endphp
                        @foreach($opds as $opd)
                            <option value="{{ $opd->id }}" {{ old('opd_id') == $opd->id ? 'selected' : '' }}>
                                {{ $opd->name }} ({{ $opd->short_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('opd_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="password" 
                           id="password"
                           value="{{ old('password') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="Minimal 8 karakter"
                           required>
                    <p class="text-xs text-gray-500 mt-1">Password akan ditampilkan setelah submit. User dapat mengubahnya nanti.</p>
                    @error('password')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('super-admin.users.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('role').addEventListener('change', function() {
        const opdContainer = document.getElementById('opd_container');
        const opdSelect = document.getElementById('opd_id');
        
        if (this.value === 'admin_opd' || this.value === 'pimpinan_opd') {
            opdContainer.style.display = 'block';
            opdSelect.required = true;
        } else {
            opdContainer.style.display = 'none';
            opdSelect.required = false;
            opdSelect.value = '';
        }
    });
</script>
@endpush
@endsection