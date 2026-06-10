@extends('layouts.app')

@section('title', 'Laporan - Pimpinan OPD')
@section('header', 'Laporan Survei - ' . $opd->short_name)

@section('content')
<div class="space-y-6">
    <!-- Info Read Only -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm text-blue-700">
                    <strong>Mode Baca Saja (Read Only)</strong> - Anda dapat melihat, menganalisis, dan mengekspor data, tetapi tidak dapat mengubah data survei.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Laporan</h3>
        
        <form method="GET" action="{{ route('pimpinan.reports.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="period_id" class="block text-sm font-medium text-gray-700 mb-1">Periode Survei</label>
                    <select name="period_id" id="period_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Semua Periode</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}" {{ $selectedPeriod == $period->id ? 'selected' : '' }}>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-1">Unit Layanan</label>
                    <select name="unit_id" id="unit_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Semua Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ $selectedUnit == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>
            
            <div class="flex justify-between items-center pt-4">
                <div class="space-x-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Tampilkan
                    </button>
                    
                    <a href="{{ route('pimpinan.reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300">
                        Reset
                    </a>
                </div>
                
                <div class="space-x-2">
                    <a href="{{ route('pimpinan.reports.export-excel', request()->query()) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Excel
                    </a>
                    
                    <a href="{{ route('pimpinan.reports.export-pdf', request()->query()) }}" 
                       class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Executive Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Eksekutif</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                    <span class="text-gray-600">Total Responden</span>
                    <span class="text-2xl font-bold text-gray-900">{{ number_format($totalRespondents) }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                    <span class="text-gray-600">Rata-rata IKM</span>
                    <span class="text-2xl font-bold text-{{ $gradeColor }}-600">{{ number_format($averageIkm, 2) }}%</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                    <span class="text-gray-600">Mutu Pelayanan</span>
                    <span class="text-lg font-bold text-{{ $gradeColor }}-600">{{ $grade }}</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Jawaban</h3>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Tidak Baik (1)</span>
                        <span>{{ $scoreDistribution[1] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ $totalRespondents > 0 ? ($scoreDistribution[1] / ($scoreDistribution[1]+$scoreDistribution[2]+$scoreDistribution[3]+$scoreDistribution[4]) * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Kurang Baik (2)</span>
                        <span>{{ $scoreDistribution[2] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $totalRespondents > 0 ? ($scoreDistribution[2] / ($scoreDistribution[1]+$scoreDistribution[2]+$scoreDistribution[3]+$scoreDistribution[4]) * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Baik (3)</span>
                        <span>{{ $scoreDistribution[3] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $totalRespondents > 0 ? ($scoreDistribution[3] / ($scoreDistribution[1]+$scoreDistribution[2]+$scoreDistribution[3]+$scoreDistribution[4]) * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Sangat Baik (4)</span>
                        <span>{{ $scoreDistribution[4] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalRespondents > 0 ? ($scoreDistribution[4] / ($scoreDistribution[1]+$scoreDistribution[2]+$scoreDistribution[3]+$scoreDistribution[4]) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">IKM per Unit</h3>
            <div class="h-64">
                <canvas id="unitChart"></canvas>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">IKM per Periode</h3>
            <div class="h-64">
                <canvas id="periodChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Question Analysis -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Analisis per Unsur SKM</h3>
        <div class="space-y-3">
            @foreach($questionAnalysis as $num => $analysis)
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm text-gray-700">{{ $num }}. {{ Str::limit($analysis['question'], 60) }}</span>
                    <span class="text-sm font-medium {{ $analysis['ikm'] >= 75 ? 'text-green-600' : ($analysis['ikm'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $analysis['ikm'] }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $analysis['ikm'] >= 75 ? 'bg-green-500' : ($analysis['ikm'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                         style="width: {{ $analysis['ikm'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Data Preview Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Data Survei</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IKM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mutu</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($previews as $survey)
                    @php
                        $avgScore = $survey->answers->avg('score') ?? 0;
                        $ikm = ($avgScore / 4) * 100;
                        $gradeClass = $ikm >= 88.31 ? 'bg-green-100 text-green-800' : ($ikm >= 76.61 ? 'bg-blue-100 text-blue-800' : ($ikm >= 65 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'));
                        $gradeLabel = $ikm >= 88.31 ? 'A' : ($ikm >= 76.61 ? 'B' : ($ikm >= 65 ? 'C' : 'D'));
                    @endphp
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $survey->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $survey->full_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $survey->nik }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $survey->unit->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $gradeClass }}">
                                {{ number_format($ikm, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold {{ $gradeClass }}">
                            {{ $gradeLabel }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data survei dengan filter yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($previews->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $previews->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Unit Chart
    const unitCtx = document.getElementById('unitChart').getContext('2d');
    new Chart(unitCtx, {
        type: 'bar',
        data: {
            labels: @json($unitChartLabels),
            datasets: [{
                label: 'Nilai IKM (%)',
                data: @json($unitChartData),
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Nilai IKM (%)' }
                }
            }
        }
    });
    
    // Period Chart
    const periodCtx = document.getElementById('periodChart').getContext('2d');
    new Chart(periodCtx, {
        type: 'line',
        data: {
            labels: @json($periodChartLabels),
            datasets: [{
                label: 'Nilai IKM (%)',
                data: @json($periodChartData),
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: 'rgb(34, 197, 94)',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Nilai IKM (%)' }
                }
            }
        }
    });
</script>
@endpush
@endsection