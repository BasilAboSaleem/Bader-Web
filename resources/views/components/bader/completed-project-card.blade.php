@props(['project'])

@php
    $projectImage = $project->image ?: ($project->galleryPhotos()[0] ?? 'images/programs/water.jpg');
@endphp

<article {{ $attributes->class(['surface-card surface-card-hover group relative flex flex-col overflow-hidden']) }}>
    <div class="relative aspect-[16/10] overflow-hidden bg-paper-3">
        <img src="{{ asset($projectImage) }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-700 ease-bader group-hover:scale-105">
        <span class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/45 to-transparent"></span>
        <span class="absolute start-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-forest-700 px-3 py-1 text-xs font-extrabold text-white shadow-card-xs">
            <x-bader.icon name="badge-check" class="h-3.5 w-3.5" />
            {{ __('completed_project_page.badge') }}
        </span>
        @if ($project->region)
            <span class="absolute bottom-3 start-3 inline-flex items-center gap-1 text-xs font-semibold text-white">
                <x-bader.icon name="map-pin" class="h-3.5 w-3.5" />
                {{ $project->region->name }}
            </span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        @if ($project->program)
            <p class="text-xs font-bold text-forest-700">{{ $project->program->title }}</p>
        @endif
        <h3 class="mt-1 text-lg font-extrabold leading-snug text-ink-900 transition group-hover:text-forest-700">
            <a href="{{ route('completed-projects.show', $project->key) }}" class="after:absolute after:inset-0">{{ $project->title }}</a>
        </h3>
        @if ($project->description)
            <p class="mt-2 line-clamp-2 text-sm leading-7 text-muted">{{ $project->description }}</p>
        @endif
        <div class="mt-auto flex flex-wrap gap-x-4 gap-y-1 pt-4 text-xs font-semibold text-subtle">
            @if ($project->completed_at)
                <span class="inline-flex items-center gap-1.5">
                    <x-bader.icon name="calendar" class="h-3.5 w-3.5" />
                    {{ $project->completed_at->translatedFormat('F Y') }}
                </span>
            @endif
            @if ($project->beneficiaries)
                <span class="inline-flex items-center gap-1.5">
                    <x-bader.icon name="users" class="h-3.5 w-3.5" />
                    {{ trans_choice('completed_project_page.beneficiaries_count', $project->beneficiaries, ['count' => number_format($project->beneficiaries)]) }}
                </span>
            @endif
        </div>
    </div>
</article>
