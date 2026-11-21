@props(['name', 'label', 'width' => 'flex-[0_1_180px]'])

<label class="flex min-w-0 {{ $width }}">
    <span class="sr-only">{{ $label }}</span>
    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'input pr-9']) }}>
        {{ $slot }}
    </select>
</label>
