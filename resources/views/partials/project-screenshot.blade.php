@php($large = asset('images/portfolio/'.$image.'-1600.webp'))
<figure class="screenshot-frame">
    <figcaption class="screenshot-chrome"><i aria-hidden="true"></i><i aria-hidden="true"></i><i aria-hidden="true"></i><span>{{ $caption }}</span></figcaption>
    <img src="{{ asset('images/portfolio/'.$image.'-800.webp') }}" srcset="{{ asset('images/portfolio/'.$image.'-800.webp') }} 800w, {{ $large }} 1600w" sizes="(max-width: 680px) 88vw, (max-width: 1400px) 44vw, 580px" alt="{{ $alt }}" width="1600" height="{{ $height ?? 870 }}" loading="{{ $loading ?? 'lazy' }}" decoding="async">
</figure>
