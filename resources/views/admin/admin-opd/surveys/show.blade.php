@extends('layouts.app')

@section('title', 'Detail Survei')
@section('header', 'Detail Survei')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Tombol Kembali -->
    <div>
        <a href="{{ route('admin-opd.surveys.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke daftar
        </a>
    </div>
    
    <!-- Info Survei -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
            <h3 class="text-lg font-semibold text-white">Informasi Survei</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Tanggal Survei</p>
                    <p class="font-medium text-gray-900">{{ $respondent->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Unit Layanan</p>
                    <p class="font-medium text-gray-900">{{ $respondent->unit->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jenis Layanan</p>
                    <p class="font-medium text-gray-900">{{ $respondent->selected_service }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Periode Survei</p>
                    <p class="font-medium text-gray-900">{{ $respondent->period->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">IP Address</p>
                    <p class="font-medium text-gray-900">{{ $respondent->ip_address ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Responden -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Data Responden</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">NIK</p>
                    <p class="font-medium text-gray-900">{{ $respondent->nik }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nama Lengkap</p>
                    <p class="font-medium text-gray-900">{{ $respondent->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nomor HP</p>
                    <p class="font-medium text-gray-900">{{ $respondent->phone }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kelompok Usia</p>
                    <p class="font-medium text-gray-900">{{ $respondent->age_group }} tahun</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jenis Kelamin</p>
                    <p class="font-medium text-gray-900">{{ $respondent->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pendidikan</p>
                    <p class="font-medium text-gray-900">{{ strtoupper($respondent->education) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pekerjaan</p>
                    <p class="font-medium text-gray-900">
                        @if($respondent->occupation == 'lainnya')
                            {{ $respondent->other_occupation }}
                        @else
                            {{ ucfirst(str_replace('_', ' ', $respondent->occupation)) }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hasil Penilaian -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Hasil Penilaian</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($answers as $num => $answer)
                <div class="flex justify-between items-center py-2 {{ $num % 2 == 0 ? 'bg-gray-50' : '' }} px-3 rounded">
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700">{{ $num }}.</span>
                        <span class="text-sm text-gray-600 ml-1">{{ $answer['question'] }}</span>
                    </div>
                    <div class="ml-4">
                        @php
                            $score = $answer['score'];
                            $scoreColor = match($score) {
                                1 => 'red',
                                2 => 'yellow',
                                3 => 'blue',
                                4 => 'green',
                                default => 'gray'
                            };
                        @endphp
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-{{ $scoreColor }}-100 text-{{ $scoreColor }}-800">
                            {{ $score ? $score . ' - ' . $answer['label'] : $answer['label'] }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Ringkasan IKM -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Ringkasan Indeks Kepuasan Masyarakat</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Rata-rata Skor</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($respondent->answers->avg('score') ?? 0, 2) }}</p>
                    <p class="text-xs text-gray-500">dari skala 1-4</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Nilai IKM</p>
                    <p class="text-2xl font-bold text-{{ $gradeColor }}-600">{{ number_format($ikm, 2) }}%</p>
                    <p class="text-xs text-gray-500">dari 0-100</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Mutu Pelayanan</p>
                    <p class="text-2xl font-bold text-{{ $gradeColor }}-600">{{ $grade }}</p>
                    <p class="text-xs text-gray-500">Berdasarkan Permen PANRB</p>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-6">
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-{{ $gradeColor }}-500 h-3 rounded-full" style="width: {{ $ikm }}%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tombol Aksi -->
    <div class="flex justify-end">
        <a href="{{ route('admin-opd.surveys.export-pdf', $respondent) }}" 
           class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Export PDF
        </a>
    </div>
</div>
@endsection