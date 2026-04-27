@props(['url', 'size' => 40, 'alt' => 'Avatar'])

<img src="{{ $url }}" alt="{{ $alt }}" class="rounded-circle shadow-sm" width="{{ $size }}" height="{{ $size }}" style="object-fit: cover; border: 2px solid white;">
