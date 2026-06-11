@props(['width' => 'auto', 'height' => '80'])

<a href="/">
    <img 
        src="{{ asset('images/lambang-sumenep.png') }}" 
        alt="Logo Pemerintah Kabupaten Sumenep" 
        width="{{ $width }}"
        height="{{ $height }}"
        class="block h-24 md:h-40 w-auto mx-auto"
    />
</a>