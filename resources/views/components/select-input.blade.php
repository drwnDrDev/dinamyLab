<select id="{{ $id }}" name="{{ $name }}" {{ $required ?? '' }}
    class="text-sm h-9 w-full p-1 border-borders focus:border-primary focus:ring-primary rounded-md">
    @foreach ($options as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>
