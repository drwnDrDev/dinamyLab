@props([
    'id' => '',
    'name' => '',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'value' => ''
])

<input
    id="{{ $id }}"
    name="{{ $name }}"
    type="{{ $type }}"
    placeholder="{{ $placeholder }}"
    value="{{ old($name, $value) }}"
    {{ $required ? 'required' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' => 'h-10 w-full px-3 py-2 bg-white dark:bg-slate-800/60 border border-borders dark:border-slate-600/50 text-text dark:text-slate-100 placeholder:text-titles/60 dark:placeholder:text-slate-500 focus:border-primary dark:focus:border-cyan-500 focus:ring-2 focus:ring-primary/20 dark:focus:ring-cyan-500/20 rounded-xl transition-colors duration-150'
    ]) }}
>

