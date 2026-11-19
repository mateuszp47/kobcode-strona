@php($caseStudy = $projects->first(fn ($item) => in_array($item->slug, $slugs, true)))
@if($caseStudy)
<p><a class="text-link" href="{{ route('project', $caseStudy->slug) }}">Poznaj realizację: {{ $caseStudy->title }} <span aria-hidden="true">↗</span></a></p>
@endif
