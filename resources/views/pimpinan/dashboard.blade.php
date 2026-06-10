@extends('layouts.app')

@section('title', 'Dashboard Pimpinan OPD')
@section('header', 'Dashboard Pimpinan - ' . $opd->short_name)

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Unit</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalUnits }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Responden</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalRespondents }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">IKM Saat Ini</p>
                    <p class="text-2xl font-semibold {{ $averageIkm >= 76.61 ? 'text-green-600' : ($averageIkm >= 65 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $averageIkm }}%
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Tren IKM</p>
                    <div class="flex items-center">
                        <svg class="h-5 w-5 {{ $trendColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $trendDirection == 'up' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                        </svg>
                        <span class="text-xl font-semibold {{ $trendColor }} ml-1">{{ abs($ikmChange) }}%</span>
                    </div>
                    <p class="text-xs text-gray-500">dari periode sebelumnya</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Periode Aktif</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $activePeriod->name ?? 'Tidak Ada' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- IKM per Unit Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Nilai IKM per Unit</h3>
            <div class="h-80">
                <canvas id="ikmChart"></canvas>
            </div>
        </div>
        
        <!-- Trend Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tren IKM 12 Bulan Terakhir</h3>
            <div class="h-80">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Best & Worst Performing Units -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Unit dengan IKM Tertinggi -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-green-600 mb-4">🏆 Unit dengan Kinerja Terbaik</h3>
            <div class="space-y-3">
                @php
                    $sortedUnits = collect($chartLabels)->sortByDesc(function($label, $key) use ($chartData) {
                        return $chartData[$key];
                    })->take(3);
                @endphp
                @foreach($sortedUnits as $index => $unitName)
                    @php
                        $unitKey = array_search($unitName, $chartLabels);
                        $ikm = $chartData[$unitKey] ?? 0;
                        $medal = $index == 0 ? '🥇' : ($index == 1 ? '🥈' : '🥉');
                    @endphp
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-xl mr-3">{{ $medal }}</span>
                            <span class="font-medium text-gray-900">{{ $unitName }}</span>
                        </div>
                        <span class="text-lg font-bold text-green-600">{{ $ikm }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Unit dengan IKM Terendah -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-red-600 mb-4">⚠️ Unit yang Perlu Perhatian</h3>
            <div class="space-y-3">
                @php
                    $worstUnits = collect($chartLabels)->sortBy(function($label, $key) use ($chartData) {
                        return $chartData[$key];
                    })->take(3);
                @endphp
                @foreach($worstUnits as $unitName)
                    @php
                        $unitKey = array_search($unitName, $chartLabels);
                        $ikm = $chartData[$unitKey] ?? 0;
                    @endphp
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-900">{{ $unitName }}</span>
                        <span class="text-lg font-bold text-red-600">{{ $ikm }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Question Analysis & Recommendations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Unsur dengan Nilai Tertinggi -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-green-600 mb-4">✓ Unsur dengan Nilai Tertinggi</h3>
            <div class="space-y-4">
                @foreach($highestAspects as $num => $aspect)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-700">{{ $num }}. {{ Str::limit($aspect['question'], 60) }}</span>
                        <span class="text-sm font-bold text-green-600">{{ $aspect['ikm'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $aspect['ikm'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Unsur dengan Nilai Terendah & Rekomendasi -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-red-600 mb-4">⚠️ Unsur yang Perlu Ditingkatkan</h3>
            <div class="space-y-4">
                @foreach($lowestAspects as $num => $aspect)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-700">{{ $num }}. {{ Str::limit($aspect['question'], 60) }}</span>
                        <span class="text-sm font-bold text-red-600">{{ $aspect['ikm'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ $aspect['ikm'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Recommendations Panel -->
    @if(count($recommendations) > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-4">📋 Rekomendasi Perbaikan</h3>
        <div class="space-y-4">
            @foreach($recommendations as $rec)
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-800">{{ $rec['aspect'] }}</p>
                    <p class="text-sm text-yellow-700 mt-1">{{ $rec['recommendation'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Aktivitas Survei Terbaru</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentSurveys as $survey)
            @php
                $avgScore = $survey->answers->avg('score') ?? 0;
                $ikm = ($avgScore / 4) * 100;
            @endphp
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $survey->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $survey->unit->name ?? '-' }} • {{ $survey->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $ikm >= 88.31 ? 'bg-green-100 text-green-800' : ($ikm >= 76.61 ? 'bg-blue-100 text-blue-800' : ($ikm >= 65 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                        IKM: {{ number_format($ikm, 1) }}%
                    </span>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-500">
                Belum ada aktivitas survei
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // IKM per Unit Chart
    const ikmCtx = document.getElementById('ikmChart').getContext('2d');
    new Chart(ikmCtx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Nilai IKM (%)',
                data: @json($chartData),
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
                    title: { display: true, text: 'Nilai IKM' }
                }
            }
        }
    });
    
    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const monthlyLabels = @json(array_keys($monthlyData));
    const monthlyIkm = @json(array_column($monthlyData, 'ikm'));
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Nilai IKM (%)',
                data: monthlyIkm,
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: 'rgb(34, 197, 94)',
                pointBorderColor: '#fff',
                pointRadius: 5,
                pointHoverRadius: 7
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
</script>
@endpush
@endsection