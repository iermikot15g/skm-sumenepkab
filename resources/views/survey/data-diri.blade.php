@extends('layouts.app')

@section('title', 'Data Diri - SKM Sumenep')
@section('header', 'Data Diri Responden')

@section('content')
<div class="max-w-2xl mx-auto">
    
    <!-- Info Ringkas -->
    <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Unit:</p>
                <p class="font-medium text-gray-900">{{ $unit->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Layanan:</p>
                <p class="font-medium text-gray-900">{{ $selectedService }}</p>
            </div>
            <a href="{{ route('survey.service') }}" class="text-sm text-green-600 hover:text-green-700">
                Ubah
            </a>
        </div>
    </div>
    
    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progress Survei</span>
            <span class="text-sm font-medium text-green-600">Step 3 dari 4</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-green-600 h-2 rounded-full" style="width: 60%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span class="text-green-600 font-medium">✓ Pilih Unit</span>
            <span class="text-green-600 font-medium">✓ Pilih Layanan</span>
            <span class="text-green-600 font-medium">Data Diri</span>
            <span>Pertanyaan</span>
        </div>
    </div>
    
    <!-- Form Data Diri -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                Informasi Pribadi
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Data Anda akan dirahasiakan sesuai dengan peraturan perundang-undangan
            </p>
        </div>
        
        <form action="{{ route('survey.post.data-diri') }}" method="POST" class="p-6 space-y-6" id="dataDiriForm">
            @csrf
            
            <!-- NIK -->
            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">
                    NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="nik" 
                       id="nik"
                       value="{{ old('nik', $oldInput['nik'] ?? '') }}"
                       maxlength="16"
                       pattern="[0-9]{16}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm @error('nik') border-red-500 @enderror"
                       placeholder="3529123412341234"
                       required>
                <p class="mt-1 text-xs text-gray-500">16 digit angka (contoh: 3529123412341234)</p>
                @error('nik')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Nama Lengkap -->
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="full_name" 
                       id="full_name"
                       value="{{ old('full_name', $oldInput['full_name'] ?? '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm @error('full_name') border-red-500 @enderror"
                       required>
                @error('full_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Nomor HP -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Nomor HP / WhatsApp <span class="text-red-500">*</span>
                </label>
                <input type="tel" 
                       name="phone" 
                       id="phone"
                       value="{{ old('phone', $oldInput['phone'] ?? '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm @error('phone') border-red-500 @enderror"
                       placeholder="081234567890"
                       required>
                <p class="mt-1 text-xs text-gray-500">Contoh: 081234567890 (tanpa tanda hubung)</p>
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Kelompok Usia -->
            <div>
                <label for="age_group" class="block text-sm font-medium text-gray-700 mb-1">
                    Kelompok Usia <span class="text-red-500">*</span>
                </label>
                <select name="age_group" 
                        id="age_group"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm @error('age_group') border-red-500 @enderror"
                        required>
                    <option value="">Pilih kelompok usia</option>
                    <option value="<20" {{ old('age_group', $oldInput['age_group'] ?? '') == '<20' ? 'selected' : '' }}>&lt; 20 tahun</option>
                    <option value="20-30" {{ old('age_group', $oldInput['age_group'] ?? '') == '20-30' ? 'selected' : '' }}>20 - 30 tahun</option>
                    <option value="31-40" {{ old('age_group', $oldInput['age_group'] ?? '') == '31-40' ? 'selected' : '' }}>31 - 40 tahun</option>
                    <option value="41-50" {{ old('age_group', $oldInput['age_group'] ?? '') == '41-50' ? 'selected' : '' }}>41 - 50 tahun</option>
                    <option value=">50" {{ old('age_group', $oldInput['age_group'] ?? '') == '>50' ? 'selected' : '' }}>&gt; 50 tahun</option>
                </select>
                @error('age_group')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Jenis Kelamin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Kelamin <span class="text-red-500">*</span>
                </label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" 
                               name="gender" 
                               value="male"
                               {{ old('gender', $oldInput['gender'] ?? '') == 'male' ? 'checked' : '' }}
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500"
                               required>
                        <span class="ml-2 text-gray-700">Laki-laki</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" 
                               name="gender" 
                               value="female"
                               {{ old('gender', $oldInput['gender'] ?? '') == 'female' ? 'checked' : '' }}
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <span class="ml-2 text-gray-700">Perempuan</span>
                    </label>
                </div>
                @error('gender')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Pendidikan Terakhir -->
            <div>
                <label for="education" class="block text-sm font-medium text-gray-700 mb-1">
                    Pendidikan Terakhir <span class="text-red-500">*</span>
                </label>
                <select name="education" 
                        id="education"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm @error('education') border-red-500 @enderror"
                        required>
                    <option value="">Pilih pendidikan terakhir</option>
                    <option value="sd" {{ old('education', $oldInput['education'] ?? '') == 'sd' ? 'selected' : '' }}>SD / Sederajat</option>
                    <option value="smp" {{ old('education', $oldInput['education'] ?? '') == 'smp' ? 'selected' : '' }}>SMP / Sederajat</option>
                    <option value="sma" {{ old('education', $oldInput['education'] ?? '') == 'sma' ? 'selected' : '' }}>SMA / Sederajat</option>
                    <option value="d1" {{ old('education', $oldInput['education'] ?? '') == 'd1' ? 'selected' : '' }}>D1</option>
                    <option value="d2" {{ old('education', $oldInput['education'] ?? '') == 'd2' ? 'selected' : '' }}>D2</option>
                    <option value="d3" {{ old('education', $oldInput['education'] ?? '') == 'd3' ? 'selected' : '' }}>D3</option>
                    <option value="s1" {{ old('education', $oldInput['education'] ?? '') == 's1' ? 'selected' : '' }}>S1</option>
                    <option value="s2" {{ old('education', $oldInput['education'] ?? '') == 's2' ? 'selected' : '' }}>S2</option>
                    <option value="s3" {{ old('education', $oldInput['education'] ?? '') == 's3' ? 'selected' : '' }}>S3</option>
                </select>
                @error('education')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Pekerjaan -->
            <div>
                <label for="occupation" class="block text-sm font-medium text-gray-700 mb-1">
                    Pekerjaan <span class="text-red-500">*</span>
                </label>
                <select name="occupation" 
                        id="occupation"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm @error('occupation') border-red-500 @enderror"
                        required>
                    <option value="">Pilih pekerjaan</option>
                    <option value="pns" {{ old('occupation', $oldInput['occupation'] ?? '') == 'pns' ? 'selected' : '' }}>PNS / TNI / POLRI</option>
                    <option value="swasta" {{ old('occupation', $oldInput['occupation'] ?? '') == 'swasta' ? 'selected' : '' }}>Karyawan Swasta</option>
                    <option value="wirausaha" {{ old('occupation', $oldInput['occupation'] ?? '') == 'wirausaha' ? 'selected' : '' }}>Wirausaha / Pengusaha</option>
                    <option value="petani" {{ old('occupation', $oldInput['occupation'] ?? '') == 'petani' ? 'selected' : '' }}>Petani / Nelayan</option>
                    <option value="nelayan" {{ old('occupation', $oldInput['occupation'] ?? '') == 'nelayan' ? 'selected' : '' }}>Nelayan</option>
                    <option value="ibu_rumah_tangga" {{ old('occupation', $oldInput['occupation'] ?? '') == 'ibu_rumah_tangga' ? 'selected' : '' }}>Ibu Rumah Tangga</option>
                    <option value="pelajar" {{ old('occupation', $oldInput['occupation'] ?? '') == 'pelajar' ? 'selected' : '' }}>Pelajar / Mahasiswa</option>
                    <option value="mahasiswa" {{ old('occupation', $oldInput['occupation'] ?? '') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="lainnya" {{ old('occupation', $oldInput['occupation'] ?? '') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('occupation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Pekerjaan Lainnya (conditional) -->
            <div id="other_occupation_container" 
                 class="{{ old('occupation', $oldInput['occupation'] ?? '') == 'lainnya' ? '' : 'hidden' }}">
                <label for="other_occupation" class="block text-sm font-medium text-gray-700 mb-1">
                    Pekerjaan Lainnya <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="other_occupation" 
                       id="other_occupation"
                       value="{{ old('other_occupation', $oldInput['other_occupation'] ?? '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm @error('other_occupation') border-red-500 @enderror"
                       placeholder="Tuliskan pekerjaan Anda"
                       {{ old('occupation', $oldInput['occupation'] ?? '') == 'lainnya' ? 'required' : '' }}>
                @error('other_occupation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Tombol Navigasi -->
            <div class="flex justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('survey.service') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 transition">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition">
                    Lanjut ke Pertanyaan
                    <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fungsi untuk toggle field pekerjaan lainnya
    function toggleOtherOccupation() {
        const occupationSelect = document.getElementById('occupation');
        const otherContainer = document.getElementById('other_occupation_container');
        const otherInput = document.getElementById('other_occupation');
        
        if (occupationSelect.value === 'lainnya') {
            otherContainer.classList.remove('hidden');
            otherInput.required = true;
        } else {
            otherContainer.classList.add('hidden');
            otherInput.required = false;
            otherInput.value = ''; // Kosongkan input jika tidak diperlukan
        }
    }
    
    // Event listener ketika halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const occupationSelect = document.getElementById('occupation');
        
        // Jalankan sekali saat load untuk mengatur status awal
        toggleOtherOccupation();
        
        // Tambahkan event listener untuk perubahan
        occupationSelect.addEventListener('change', toggleOtherOccupation);
    });
</script>
@endpush