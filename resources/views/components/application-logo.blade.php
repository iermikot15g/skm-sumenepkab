@props(['width' => 'auto', 'height' => '50'])

<a href="/">
    <img 
        src="{{ asset('images/logo-sumenep-192x192.png') }}" 
        alt="Logo Pemerintah Kabupaten Sumenep" 
        width="{{ $width }}"
        height="{{ $height }}"
        class="block h-12 w-auto"
    />
</a>