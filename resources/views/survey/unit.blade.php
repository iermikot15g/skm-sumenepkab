@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Pilih Unit Layanan</h1>
        <p class="text-gray-600">Periode Survei Aktif: <strong>{{ $activePeriod->name }}</strong></p>
        <p class="text-sm text-gray-500">{{ $activePeriod->start_date->format('d/m/Y') }} - {{ $activePeriod->end_date->format('d/m/Y') }}</p>
    </div>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($units as $unit)
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $unit->name }}</h3>
            <p class="text-gray-600 text-sm mb-4">{{ $unit->description }}</p>
            <form method="POST" action="{{ route('survey.store.unit') }}">
                @csrf
                <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition">
                    Pilih Unit Ini
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection