@extends('layouts.app')

@section('title', 'Analisis SKM')
@section('header', 'Analisis SKM - ' . $opd->short_name)

@section('content')
<div class="space-y-6">
    <!-- Filter -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Analisis</h3>
        
        <form method="GET" action="{{ route('pimpinan.analysis') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Terapkan
                </button>
                
                <a href="{{ route('pimpinan.analysis') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>
    </div>
    
    <!-- Executive Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow p-4 text-white">
            <p class="text-sm opacity-90">Prioritas Tinggi</p>
            <p class="text-2xl font-bold">{{ collect($priorityInterventions)->where('priority', 'High')->count() }}</p>
            <p class="text-xs opacity-75">Unsur dengan nilai < 50%</p>
        </div>
        
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg shadow p-4 text-white">
            <p class="text-sm opacity-90">Prioritas Sedang</p>
            <p class="text-2xl font-bold">{{ collect($priorityInterventions)->where('priority', 'Medium')->count() }}</p>
            <p class="text-xs opacity-75">Unsur dengan nilai 50-75%</p>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-4 text-white">
            <p class="text-sm opacity-90">Prioritas Rendah</p>
            <p class="text-2xl font-bold">{{ collect($priorityInterventions)->where('priority', 'Low')->count() }}</p>
            <p class="text-xs opacity-75">Unsur dengan nilai > 75%</p>
        </div>
        
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-4 text-white">
            <p class="text-sm opacity-90">Total Unsur</p>
            <p class="text-2xl font-bold">{{ count($questionAnalysis) }}</p>
            <p class="text-xs opacity-75">Unsur SKM yang dianalisis</p>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Bar Chart - All Questions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafik Nilai per Unsur SKM</h3>
            <div class="h-96">
                <canvas id="barChart"></canvas>
            </div>
        </div>
        
        <!-- Radar Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Profil Kualitas Pelayanan</h3>
            <div class="h-96">
                <canvas id="radarChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Period Comparison -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Perbandingan Antar Periode</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Periode</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Jumlah Responden</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Nilai IKM</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Mutu</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Tren</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($periodComparison as $index => $period)
                    @php
                        $trend = '';
                        $trendIcon = '';
                        if ($index > 0) {
                            $prevIkm = $periodComparison[$index - 1]['ikm'];
                            $diff = $period['ikm'] - $prevIkm;
                            if ($diff > 0) {
                                $trend = 'Meningkat';
                                $trendIcon = '<svg class="h-4 w-4 text-green-500 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>';
                            } elseif ($diff < 0) {
                                $trend = 'Menurun';
                                $trendIcon = '<svg class="h-4 w-4 text-red-500 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
                            } else {
                                $trend = 'Stabil';
                                $trendIcon = '<svg class="h-4 w-4 text-gray-500 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>';
                            }
                        }
                    @endphp
                    <tr class="{{ $index == 0 ? 'bg-green-50' : '' }}">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $period['period_name'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ number_format($period['respondents']) }}</td>
                        <td class="px-4 py-3 text-sm font-semibold" style="color: {{ $period['gradeColor'] }}">{{ $period['ikm'] }}%</td>
                        <td class="px-4 py-3 text-sm">{{ $period['grade'] }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($index > 0)
                                {!! $trendIcon !!} <span class="ml-1">{{ $trend }} ({{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 2) }}%)</span>
                            @else
                                <span class="text-gray-400">Periode terbaru</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Priority Interventions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Prioritas Intervensi</h3>
        <div class="space-y-4">
            @foreach($priorityInterventions as $intervention)
            <div class="border rounded-lg p-4 {{ $intervention['priority'] == 'High' ? 'border-red-200 bg-red-50' : ($intervention['priority'] == 'Medium' ? 'border-yellow-200 bg-yellow-50' : 'border-green-200 bg-green-50') }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $intervention['priority'] == 'High' ? 'bg-red-100 text-red-800' : ($intervention['priority'] == 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                Prioritas {{ $intervention['priority'] }}
                            </span>
                            <span class="text-xs text-gray-500">Target: +{{ $intervention['target_ikm'] - $intervention['current_ikm'] }}%</span>
                            <span class="text-xs text-gray-500">Estimasi: {{ $intervention['timeframe'] }}</span>
                        </div>
                        <h4 class="font-semibold text-gray-900 mt-2">{{ $intervention['question_number'] }}. {{ $intervention['question'] }}</h4>
                        <div class="mt-2 flex items-center space-x-4">
                            <div>
                                <span class="text-xs text-gray-500">Nilai Saat Ini</span>
                                <p class="text-sm font-semibold {{ $intervention['priority'] == 'High' ? 'text-red-600' : ($intervention['priority'] == 'Medium' ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ $intervention['current_ikm'] }}%
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Target</span>
                                <p class="text-sm font-semibold text-gray-900">{{ $intervention['target_ikm'] }}%</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Dampak</span>
                                <p class="text-sm text-gray-700">{{ $intervention['estimated_impact'] }}</p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-sm text-gray-700">{!! nl2br(e($intervention['recommendation'])) !!}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Unsur Analysis Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Analisis Lengkap per Unsur SKM</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unsur SKM</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Skor Rata-rata</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nilai IKM</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Mutu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rekomendasi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($questionAnalysis as $num => $analysis)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $num }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $analysis['question'] }}</td>
                        <td class="px-4 py-3 text-sm text-center">{{ $analysis['avg_score'] }} / 4</td>
                        <td class="px-4 py-3 text-sm text-center font-semibold" style="color: {{ $analysis['gradeColor'] }}">{{ $analysis['ikm'] }}%</td>
                        <td class="px-4 py-3 text-sm text-center">{{ $analysis['grade'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{!! nl2br(e($analysis['recommendation'])) !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Nilai IKM (%)',
                data: @json($chartData),
                backgroundColor: @json($chartColors),
                borderColor: @json($chartColors),
                borderWidth: 1,
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
                },
                x: {
                    ticks: { font: { size: 10 } }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `IKM: ${context.raw}%`;
                        }
                    }
                }
            }
        }
    });
    
    // Radar Chart
    const radarCtx = document.getElementById('radarChart').getContext('2d');
    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: @json($radarLabels),
            datasets: [{
                label: 'Nilai IKM (%)',
                data: @json($radarData),
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 2,
                pointBackgroundColor: 'rgb(34, 197, 94)',
                pointBorderColor: '#fff',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { stepSize: 20 }
                }
            }
        }
    });
</script>
@endpush
@endsection