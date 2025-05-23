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
        'class' => 'h-8 w-full p-2 border-gray-300 focus:border-primary focus:ring-primary
                    rounded-sm'
    ]) }}
>

