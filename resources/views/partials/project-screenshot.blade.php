@php
    $large = asset('images/portfolio/'.$image.'-1600.webp');
    $small = asset('images/portfolio/'.$image.'-800.webp');
    $sourceSet = $small.' 800w, '.$large.' 1600w';
    $source = $small;
    if (isset($nativeWidth)) {
        $source = asset('images/portfolio/'.$image.'-'.$nativeWidth.'.webp');
        $sourceSet .= ', '.$source.' '.$nativeWidth.'w';
    }
@endphp
<figure class="screenshot-frame"><figcaption class="screenshot-chrome"><span class="browser-dots" aria-hidden="true"><i></i><i></i><i></i></span><span>{{ $caption }}</span></figcaption><img src="{{ $source }}" srcset="{{ $sourceSet }}" sizes="{{ $sizes ?? '(max-width: 760px) 88vw, 75vw' }}" alt="{{ $alt }}" width="{{ $nativeWidth ?? 1600 }}" height="{{ $height ?? 870 }}" loading="{{ $loading ?? 'lazy' }}" decoding="async"></figure>
