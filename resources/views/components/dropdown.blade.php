@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white/95 dark:bg-slate-800/95 backdrop-blur-md'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative">
    <div>
        {{ $trigger }}
    </div>

    <div>
        <div class="rounded-xl ring-1 ring-black/5 dark:ring-white/10 shadow-lg {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
