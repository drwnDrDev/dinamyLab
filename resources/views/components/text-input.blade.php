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
        'class' => 'h-9 w-full p-2 border-borders focus:border-primary focus:ring-primary
                    rounded-md'
    ]) }}
>

