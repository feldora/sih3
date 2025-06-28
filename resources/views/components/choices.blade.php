@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select option...',
    'error' => null,
    'id' => 'choicee-' . uniqid(),
])

<div class="mb-4">
    @if($label)
        <label for="{{ $id }}" class="label">{{ $label }}</label>
    @endif
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        class="select select-bordered w-full"
    >
        <option value="" disabled {{ old($name, $selected) === null ? 'selected' : '' }}>{{ $placeholder }}</option>
        @foreach($options as $option)
            <option value="{{ $option['value'] }}"
                {{ old($name, $selected) == $option['value'] ? 'selected' : '' }}>
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
    @error($name)
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!window._choicesInitialized) window._choicesInitialized = {};
            if (!window._choicesInitialized['{{ $name }}']) {
                new Choices('#{{ $id }}', {
                    removeItemButton: false,
                    placeholder: true,
                    placeholderValue: @json($placeholder),
                    searchEnabled: true,
                    shouldSort: false,
                });
                window._choicesInitialized['{{ $name }}'] = true;
            }
        });
    </script>
@endpush