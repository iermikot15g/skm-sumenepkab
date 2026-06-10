@extends('layouts.app')

@section('title', 'Pertanyaan Survei - Step ' . $step . ' dari 9')
@section('header', 'Penilaian Pelayanan')

@section('content')
<div class="max-w-2xl mx-auto">
    
    <!-- Info Ringkas -->
    <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <span class="text-gray-500">Unit:</span>
                <span class="font-medium">{{ \App\Models\Unit::find(session('survey.unit_id'))->name ?? '-' }}</span>
            </div>
            <div>
                <span class="text-gray-500">Layanan:</span>
                <span class="font-medium">{{ session('survey.selected_service') }}</span>
            </div>
            <a href="{{ route('survey.data-diri') }}" class="text-green-600 hover:text-green-700 text-sm">
                Ubah Data Diri
            </a>
        </div>
    </div>
    
    <!-- Progress Bar dengan Alpine.js -->
    <div class="mb-8" x-data="{ progress: {{ $progress }} }" x-init="progress = {{ $progress }}">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progress Survei</span>
            <span class="text-sm font-medium text-green-600">Pertanyaan {{ $step }} dari 9</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-green-600 h-2.5 rounded-full transition-all duration-300" 
                 :style="`width: ${progress}%`"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span class="text-green-600 font-medium">✓ Pilih Unit</span>
            <span class="text-green-600 font-medium">✓ Pilih Layanan</span>
            <span class="text-green-600 font-medium">✓ Data Diri</span>
            <span class="text-green-600 font-medium">Pertanyaan {{ $step }}/9</span>
        </div>
    </div>
    
    <!-- Form Pertanyaan -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-700 font-semibold text-sm">
                    {{ $step }}
                </span>
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $currentQuestion }}
                </h3>
            </div>
        </div>
        
        <form action="{{ route('survey.post.question', $step) }}" method="POST" class="p-6" id="questionForm">
            @csrf
            
            <!-- Pilihan Jawaban menggunakan component radio-card -->
            <div class="space-y-3">
                <x-forms.radio-card 
                    name="score"
                    value="1"
                    id="score_1"
                    checked="{{ $currentAnswer == 1 }}"
                    title="1 - Tidak Sesuai / Tidak Mudah / Tidak Baik"
                    description="Pelayanan tidak sesuai dengan standar yang dijanjikan" />
                
                <x-forms.radio-card 
                    name="score"
                    value="2"
                    id="score_2"
                    checked="{{ $currentAnswer == 2 }}"
                    title="2 - Kurang Sesuai / Kurang Mudah / Kurang Baik"
                    description="Pelayanan kurang sesuai dan masih perlu perbaikan" />
                
                <x-forms.radio-card 
                    name="score"
                    value="3"
                    id="score_3"
                    checked="{{ $currentAnswer == 3 }}"
                    title="3 - Sesuai / Mudah / Baik"
                    description="Pelayanan sudah sesuai dengan standar yang diharapkan" />
                
                <x-forms.radio-card 
                    name="score"
                    value="4"
                    id="score_4"
                    checked="{{ $currentAnswer == 4 }}"
                    title="4 - Sangat Sesuai / Sangat Mudah / Sangat Baik"
                    description="Pelayanan melebihi standar yang diharapkan" />
            </div>
            
            @error('score')
                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
            @enderror
            
            <!-- Tombol Navigasi -->
            <div class="mt-8 flex justify-between">
                @if($step > 1)
                    <a href="{{ route('survey.question', $step - 1) }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 transition">
                        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Sebelumnya
                    </a>
                @else
                    <a href="{{ route('survey.data-diri') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 transition">
                        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali ke Data Diri
                    </a>
                @endif
                
                <button type="submit" 
                        id="nextButton"
                        class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition">
                    @if($step < 9)
                        Selanjutnya
                        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    @else
                        Lihat Ringkasan
                        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    @endif
                </button>
            </div>
        </form>
    </div>
    
    <!-- Informasi -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Penilaian Anda sangat berharga untuk meningkatkan kualitas pelayanan publik di Kabupaten Sumenep.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk validasi client-side -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('questionForm');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const selected = document.querySelector('input[name="score"]:checked');
                if (!selected) {
                    e.preventDefault();
                    alert('Silahkan pilih jawaban terlebih dahulu.');
                }
            });
        }
    });
</script>
@endpush
@endsection