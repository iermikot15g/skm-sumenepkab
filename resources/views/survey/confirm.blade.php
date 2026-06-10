@extends('layouts.app')

@section('title', 'Konfirmasi Survei - SKM Sumenep')
@section('header', 'Konfirmasi Data Survei')

@section('content')
<div class="max-w-3xl mx-auto">
    
    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progress Survei</span>
            <span class="text-sm font-medium text-green-600">Step 4 dari 4</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-green-600 h-2.5 rounded-full" style="width: 100%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span class="text-green-600 font-medium">✓ Pilih Unit</span>
            <span class="text-green-600 font-medium">✓ Pilih Layanan</span>
            <span class="text-green-600 font-medium">✓ Data Diri</span>
            <span class="text-green-600 font-medium">✓ Pertanyaan</span>
        </div>
    </div>
    
    <!-- Ringkasan Survei -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
            <h3 class="text-lg font-semibold text-white">Ringkasan Survei</h3>
            <p class="text-green-100 text-sm mt-1">Silahkan periksa kembali data Anda sebelum mengirim</p>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Informasi Unit & Layanan -->
            <div class="border-b border-gray-200 pb-4">
                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Informasi Pelayanan</h4>
                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-gray-500">Unit Layanan</span>
                        <p class="font-medium text-gray-900">{{ $unit->name }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Jenis Layanan</span>
                        <p class="font-medium text-gray-900">{{ $selectedService }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Data Diri -->
            <div class="border-b border-gray-200 pb-4">
                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Data Responden</h4>
                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-gray-500">NIK</span>
                        <p class="font-medium text-gray-900">{{ $respondentData['nik'] }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Nama Lengkap</span>
                        <p class="font-medium text-gray-900">{{ $respondentData['full_name'] }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Nomor HP</span>
                        <p class="font-medium text-gray-900">{{ $respondentData['phone'] }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Kelompok Usia</span>
                        <p class="font-medium text-gray-900">{{ $respondentData['age_group'] }} tahun</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Jenis Kelamin</span>
                        <p class="font-medium text-gray-900">{{ $respondentData['gender'] == 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Pendidikan</span>
                        <p class="font-medium text-gray-900">{{ strtoupper($respondentData['education']) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Pekerjaan</span>
                        <p class="font-medium text-gray-900">
                            @if($respondentData['occupation'] == 'lainnya')
                                {{ $respondentData['other_occupation'] }}
                            @else
                                {{ ucfirst(str_replace('_', ' ', $respondentData['occupation'])) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Jawaban Pertanyaan -->
            <div class="border-b border-gray-200 pb-4">
                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Penilaian Pelayanan</h4>
                <div class="mt-3 space-y-3">
                    @foreach($questions as $num => $question)
                    <div class="flex justify-between items-start py-3 {{ $num % 2 == 0 ? 'bg-gray-50' : '' }} px-3 rounded">
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-700">{{ $num }}.</span>
                            <span class="text-sm text-gray-600 ml-1">{{ $question }}</span>
                        </div>
                        <div class="ml-4">
                            @php
                                // Ambil score dari array answers
                                $score = $answers[$num] ?? null;
                                
                                // Konversi score ke label dan warna
                                if ($score == 1) {
                                    $scoreLabel = 'Tidak Baik';
                                    $scoreColor = 'red';
                                } elseif ($score == 2) {
                                    $scoreLabel = 'Kurang Baik';
                                    $scoreColor = 'yellow';
                                } elseif ($score == 3) {
                                    $scoreLabel = 'Baik';
                                    $scoreColor = 'blue';
                                } elseif ($score == 4) {
                                    $scoreLabel = 'Sangat Baik';
                                    $scoreColor = 'green';
                                } else {
                                    $scoreLabel = 'Belum diisi';
                                    $scoreColor = 'gray';
                                }
                            @endphp
                            <div class="text-right">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-semibold bg-{{ $scoreColor }}-100 text-{{ $scoreColor }}-800 min-w-[100px] text-center">
                                    @if($score)
                                        {{ $score }} - {{ $scoreLabel }}
                                    @else
                                        ❌ {{ $scoreLabel }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- IKM Sementara -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Indeks Kepuasan Masyarakat (IKM)</h4>
                <div class="mt-2 flex items-baseline space-x-2">
                    <span class="text-3xl font-bold text-gray-900">{{ number_format($ikm, 2) }}</span>
                    <span class="text-gray-500">/ 100</span>
                    <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-{{ $mutu['color'] }}-100 text-{{ $mutu['color'] }}-800">
                        {{ $mutu['grade'] }} - {{ $mutu['label'] }}
                    </span>
                </div>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-{{ $mutu['color'] }}-500 h-2 rounded-full" style="width: {{ $ikm }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    *IKM dihitung berdasarkan rumus Permen PANRB No. 14 Tahun 2017
                </p>
            </div>
        </div>
        
        <!-- Tombol Aksi -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
            <a href="{{ route('survey.question', 9) }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 transition">
                <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali Edit
            </a>
            
            <form action="{{ route('survey.submit') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition">
                    Kirim Survei
                    <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

