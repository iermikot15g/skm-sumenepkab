@extends('layouts.app')

@section('title', 'Terima Kasih - SKM Sumenep')
@section('header', 'Terima Kasih')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    
    <!-- Icon Sukses -->
    <div class="mb-6">
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100">
            <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
    </div>
    
    <!-- Pesan Terima Kasih -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                Survei Telah Terkirim!
            </h2>
            <p class="text-gray-600 mb-6">
                Terima kasih atas partisipasi Anda dalam Survei Kepuasan Masyarakat Kabupaten Sumenep.
                Penilaian Anda sangat berharga untuk meningkatkan kualitas pelayanan publik.
            </p>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-800">
                    <strong>Informasi Penting:</strong><br>
                    Data Anda akan dirahasiakan dan hanya digunakan untuk keperluan peningkatan mutu pelayanan sesuai dengan Permen PANRB No. 14 Tahun 2017.
                </p>
            </div>
            
            <div class="space-y-3">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center justify-center w-full px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 transition">
                    Kembali ke Beranda
                </a>
                
                <a href="{{ route('survey.unit') }}" 
                   class="inline-flex items-center justify-center w-full px-6 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 transition">
                    Isi Survei Lainnya
                </a>
            </div>
        </div>
    </div>    
</div>
@endsection