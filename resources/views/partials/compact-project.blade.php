<section class="compact-case" id="project-{{ $project->slug }}">
    <span class="eyebrow">{{ $project->category }} · {{ ['own' => 'Projekt własny', 'client' => 'Projekt dla klienta', 'related' => 'Firma powiązana'][$project->relationship] }}</span>
    <div>
        <h2>{{ $project->title }}</h2>
        <p class="preserve-lines">{{ $project->summary }}</p>
        @if($project->result)
            <p class="compact-case-result preserve-lines">{{ $project->result }}</p>
        @endif
        <a class="text-link" href="{{ route('project', $project->slug) }}">Poznaj realizację {{ $project->title }} <span aria-hidden="true">↗</span></a>
    </div>
    @if($project->cover)
        <div class="compact-case-preview">
            <figure class="screenshot-frame">
                <figcaption class="screenshot-chrome"><span class="browser-dots" aria-hidden="true"><i></i><i></i><i></i></span><span>{{ $project->title }}</span></figcaption>
                <img src="{{ Storage::disk('public')->url($project->cover) }}" alt="{{ $project->cover_alt ?: $project->title }}" loading="lazy" decoding="async">
            </figure>
        </div>
    @endif
</section>
