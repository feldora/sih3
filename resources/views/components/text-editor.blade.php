@props(['name', 'label' => '', 'value' => '', 'placeholder' => '', 'required' => false, 'disabled' => false])

<div class="mb-4">
    @if ($label)
        <label for="{{ $name }}" class="block text-gray-700 font-bold mb-2">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <!-- Textarea untuk TinyMCE Editor -->
    <textarea name="{{ $name }}" id="{{ $name }}" placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full h-60 p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500']) }}>{{ $value }}</textarea>

    <!-- Script untuk TinyMCE Editor -->
    {{-- <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> --}}


    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

@push('scripts')

    <script>
        window.onload = function() {
            tinymce.init({
                selector: '#content',
                height: 500,
                menubar: true,
                base_url: '/tinymce',
                branding: false,
                skin: 'oxide',
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | styleselect | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
            });
        }
    </script>
@endpush