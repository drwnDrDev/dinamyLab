<select id="{{ $id }}" name="{{ $name }}" {{ $required ? 'required' : '' }}
    class="text-sm h-8 w-full p-2 border-gray-300 focus:border-primary focus:ring-primary rounded-sm">
    @foreach ($options as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>