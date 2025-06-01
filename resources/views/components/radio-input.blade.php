@props(['id' => '', 'name' => '', 'value' => '', 'checked' => false])

<input type="radio" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" {{ $checked ? 'checked' : '' }}
    class="appearance-none h-5 w-5 border border-borders rounded-full checked:bg-primary focus:ring-primary">