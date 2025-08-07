<select name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'form-select input input-bordered']) }}>
    <option value="">-- Pilih Instansi --</option>
    @foreach($instansis as $instansi)
        <option value="{{ $instansi->id }}" @selected(old($name, $selected) == $instansi->id)>
            {{ $instansi->singkatan ?: $instansi->nama }}
        </option>
    @endforeach
</select>
