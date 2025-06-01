<select id="{{ $id }}" name="{{ $name }}" {{ $required ?? '' }}
    class="text-sm h-8 w-full p-2 border-borders focus:border-primary focus:ring-primary rounded-md">
    @foreach ($options as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>