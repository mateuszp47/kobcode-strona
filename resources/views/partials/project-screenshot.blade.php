@php($large = asset('images/portfolio/'.$image.'-1600.webp'))
<a class="screenshot-frame" href="{{ $large }}" target="_blank" rel="noopener" aria-label="{{ $alt }} — otwórz większy obraz w nowej karcie">
    <span class="screenshot-chrome" aria-hidden="true"><i></i><i></i><i></i><span>{{ $caption }}</span><b>↗</b></span>
    <img src="{{ asset('images/portfolio/'.$image.'-800.webp') }}" srcset="{{ asset('images/portfolio/'.$image.'-800.webp') }} 800w, {{ $large }} 1600w" sizes="(max-width: 680px) 88vw, (max-width: 1400px) 44vw, 580px" alt="{{ $alt }}" width="1600" height="{{ $height ?? 870 }}" loading="lazy" decoding="async">
    <span class="screenshot-open">Powiększ zrzut <span aria-hidden="true">↗</span></span>
</a>
