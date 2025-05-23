<select id="{{ $id }}" name="{{ $name }}" {{ $required ? 'required' : '' }}
    class="h-8 w-full p-2 border-gray-300 focus:border-secondary focus:ring-secondary rounded-sm">
    @foreach ($options as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>