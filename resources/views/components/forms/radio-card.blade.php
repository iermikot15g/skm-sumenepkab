@props([
    'name',           // Nama field (untuk radio button)
    'value',          // Value radio button
    'id',             // ID unik untuk radio
    'checked' => false, // Apakah terpilih?
    'title',          // Judul utama (wajib)
    'subtitle' => null, // Subtitle/kode/secondary info
    'description' => null, // Deskripsi panjang
    'badge' => null,  // Badge text (misal: "Populer", "Baru")
    'icon' => null,   // Icon path (opsional)
])

@php
    $radioId = $id ?? 'radio_' . Str::slug($name) . '_' . $value;
    $borderColorClass = match(true) {
        $checked && $value == 1 => 'border-green-500',
        $checked && $value == 2 => 'border-yellow-500',
        $checked && $value == 3 => 'border-blue-500',
        $checked && $value == 4 => 'border-green-600',
        $checked => 'border-green-500',
        default => 'border-gray-200'
    };
    
    $bgColorClass = match(true) {
        $checked && $value == 1 => 'bg-green-50',
        $checked && $value == 2 => 'bg-yellow-50',
        $checked && $value == 3 => 'bg-blue-50',
        $checked && $value == 4 => 'bg-green-100',
        $checked => 'bg-green-50',
        default => 'bg-white'
    };
@endphp

<div class="border rounded-lg overflow-hidden transition-all duration-200 {{ $borderColorClass }} {{ $bgColorClass }} hover:shadow-md hover:-translate-y-0.5">
    <label for="{{ $radioId }}" class="flex items-start p-4 cursor-pointer w-full">
        <!-- Radio Button -->
        <input type="radio" 
               name="{{ $name }}" 
               id="{{ $radioId }}" 
               value="{{ $value }}"
               {{ $checked ? 'checked' : '' }}
               class="mt-0.5 h-5 w-5 flex-shrink-0 border-gray-300 focus:ring-green-500"
               style="accent-color: #16a34a;">
        
        <div class="ml-3 flex-1">
            <!-- Header with title and badge -->
            <div class="flex items-center flex-wrap gap-2">
                @if($icon)
                    <div class="flex-shrink-0">
                        <img src="{{ $icon }}" alt="" class="h-6 w-6">
                    </div>
                @endif
                <span class="font-semibold text-gray-900">{{ $title }}</span>
                
                @if($badge)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $badge }}
                    </span>
                @endif
            </div>
            
            <!-- Subtitle -->
            @if($subtitle)
                <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
            @endif
            
            <!-- Description -->
            @if($description)
                <p class="text-sm text-gray-600 mt-2">{{ $description }}</p>
            @endif
        </div>
    </label>
</div>