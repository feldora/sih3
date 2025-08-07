<select name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'form-select input input-bordered']) }}>
    <option value="">-- Pilih Jenis Pos --</option>
    @foreach($options as $value => $label)
        <option value="{{ $value }}" @selected(old($name, $selected) === $value)>
            {{ $label }}
        </option>
    @endforeach
</select>
