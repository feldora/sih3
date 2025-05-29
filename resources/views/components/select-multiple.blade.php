@props([
    'label' => '',
    'name',
    'options' => [],
    'value' => [],
    'required' => false,
    'class' => '',
])

@php
    $selected = is_array($value) ? $value : (array) $value;
@endphp

<div class="mb-4 {{ $class }}">
    @if($label)
        <label class="block text-sm font-medium text-gray-700" for="{{ $name }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        multiple
        @if($required) required @endif
        class="form-select mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}"
                @if(in_array($optionValue, $selected)) selected @endif
            >{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error(str_replace('[]', '', $name))
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>