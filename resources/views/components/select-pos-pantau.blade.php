<div>
    <label for="{{ $id }}" class="label">
        <span class="label-text">Pos Pantau</span>
    </label>

    <select 
        id="{{ $id }}" 
        name="pos_pantau_id" 
        {{ $attributes->merge(['class' => 'select select-bordered w-full']) }}
        required
    >
        <option value="">-- Pilih Pos Pantau --</option>
        @foreach($posPantau as $pos)
            <option value="{{ $pos->id }}" {{ $selected == $pos->id ? 'selected' : '' }}>
                {{ $pos->nama_pos }} ({{ $pos->jenis_pos }})
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>
