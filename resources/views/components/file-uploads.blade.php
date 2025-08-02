<!-- resources/views/components/file-upload.blade.php -->
@props([
    'id',
    'name',
    'label',
    'multiple' => true,
    'value' => null,
    'maxSize' => '5MB',
    'description' => null,
    'required' => false,
    'accept' => 'image/*',
    'acceptLabel' => 'Any file type',
])

<div class="file-upload-container">
    <!-- Label -->
    <label for="{{ $id }}" class="block text-sm font-semibold text-gray-800 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    @if ($description)
        <p class="text-sm text-gray-600 mb-3">{{ $description }}</p>
    @endif

    <!-- Hidden File Input -->
    <input type="file" id="{{ $id }}" name="{{ $name }}{{ $multiple ? '[]' : '' }}" class="hidden"
        accept="{{ $accept }}" {{ $attributes }} @if ($multiple) multiple @endif
        @if ($required) required @endif onchange="handleFileSelect(event, '{{ $id }}')" />

    <!-- Custom Upload Area -->
    <div class="upload-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-300 hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer"
        id="upload-area-{{ $id }}" onclick="document.getElementById('{{ $id }}').click()"
        ondrop="handleDrop(event, '{{ $id }}')" ondragover="handleDragOver(event, '{{ $id }}')"
        ondragleave="handleDragLeave(event, '{{ $id }}')">
        <!-- Upload Icon -->
        <div class="upload-icon mb-4">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path
                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>

        <!-- Upload Text -->
        <div class="upload-text">
            <p class="text-lg font-medium text-gray-900 mb-1">
                {{ $multiple ? 'Choose files or drag here' : 'Choose file or drag here' }}
            </p>
            <p class="text-sm text-gray-500">
                {{ $acceptLabel }} up to {{ $maxSize }}
            </p>
        </div>

        <!-- Upload Button -->
        <button type="button"
            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            Browse Files
        </button>
    </div>

    <!-- Error Message -->
    @error($name)
        <div class="mt-2 flex items-center text-red-600">
            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-sm">{{ $message }}</span>
        </div>
    @enderror

    <!-- Preview Container -->
    <div class="mt-4 {{ $value ? '' : 'hidden' }}" id="preview-container-{{ $id }}">
        <div class="preview-header flex items-center justify-between mb-3">
            <h4 class="text-sm font-semibold text-gray-800">Preview</h4>
            <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium"
                onclick="clearFiles('{{ $id }}')">
                Remove All
            </button>
        </div>
        <div class="preview-grid grid gap-4" id="preview-grid-{{ $id }}">
            <!-- Preview images will be inserted here -->
            @if ($value)
                @php
                    $files = is_array($value) ? $value : [$value];
                @endphp
                @foreach ($files as $file)
                    <div class="preview-item">
                        <img src="{{ is_string($file) ? asset($file) : $file->temporaryUrl() }}" alt="Preview">
                        <div class="file-info">
                            <div class="font-medium truncate">
                                {{ is_string($file) ? basename($file) : $file->getClientOriginalName() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<style>
    .upload-area.drag-over {
        @apply border-indigo-500 bg-indigo-100;
    }

    .preview-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .preview-item:hover {
        transform: translateY(-2px);
    }

    .preview-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .preview-overlay {
        position: absolute;
        top: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 8px;
        border-radius: 0 0 0 12px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .preview-overlay:hover {
        background: rgba(220, 38, 38, 0.8);
    }

    .file-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
        color: white;
        padding: 12px;
        font-size: 12px;
    }

    .preview-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }

    .file-icon {
        width: 100%;
        border-radius: 12px;
        font-size: 3rem;
    }
</style>

<script>
    const fileLists = {};

function handleFileSelect(event, inputId) {
    const files = Array.from(event.target.files);
    const input = document.getElementById(inputId);
    const isMultiple = input.hasAttribute('multiple');

    if (!fileLists[inputId]) fileLists[inputId] = [];

    if (isMultiple) {
        // tambahkan file yang belum ada di fileLists
        files.forEach(f => {
            if (!fileLists[inputId].some(existing => existing.name === f.name && existing.size === f.size && existing.lastModified === f.lastModified)) {
                fileLists[inputId].push(f);
            }
        });
    } else {
        fileLists[inputId] = [files[0]];
    }

    updatePreviewAndInput(inputId);
}

function handleDrop(event, inputId) {
    event.preventDefault();
    const uploadArea = document.getElementById(`upload-area-${inputId}`);
    uploadArea.classList.remove('drag-over');

    const droppedFiles = Array.from(event.dataTransfer.files);
    const input = document.getElementById(inputId);
    const isMultiple = input.hasAttribute('multiple');

    if (!fileLists[inputId]) fileLists[inputId] = [];

    if (isMultiple) {
        droppedFiles.forEach(f => {
            if (!fileLists[inputId].some(existing => existing.name === f.name && existing.size === f.size && existing.lastModified === f.lastModified)) {
                fileLists[inputId].push(f);
            }
        });
    } else {
        fileLists[inputId] = [droppedFiles[0]];
    }

    updatePreviewAndInput(inputId);
}

    function handleDragOver(event, inputId) {
        event.preventDefault();
        const uploadArea = document.getElementById(`upload-area-${inputId}`);
        uploadArea.classList.add('drag-over');
    }

    function handleDragLeave(event, inputId) {
        event.preventDefault();
        const uploadArea = document.getElementById(`upload-area-${inputId}`);
        uploadArea.classList.remove('drag-over');
    }

    function displayPreviews(files, inputId) {
        const previewContainer = document.getElementById(`preview-container-${inputId}`);
        const previewGrid = document.getElementById(`preview-grid-${inputId}`);

        // Kosongkan isi lama
        // previewGrid.innerHTML = '';

        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function(e) {
                const previewItem = createPreviewItem(e.target.result, file, index, inputId);
                previewGrid.appendChild(previewItem);
            };

            reader.readAsDataURL(file); // tetap pakai ini walau file bukan gambar (dataURL masih valid)
        });

        previewContainer.classList.remove('hidden');
    }

    function createPreviewItem(src, file, index, inputId) {
        const div = document.createElement('div');
        div.className = 'preview-item';

        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();
        const isImage = file.type.startsWith('image/');

        // Define icons by type
        const fileIcons = {
            pdf: '📄',
            doc: '📝',
            docx: '📝',
            xls: '📊',
            xlsx: '📊',
            zip: '🗜️',
            rar: '🗜️',
            default: '📁'
        };

        const icon = fileIcons[fileExt] || fileIcons.default;

        let thumbnail = `
            ${isImage ? 
                `<img src="${src}" alt="Preview ${index + 1}">` : 
                `<div class="file-icon flex items-center justify-center bg-gray-100 text-4xl text-gray-500" style="height: 200px;">
                    ${icon}
                </div>`
            }
            <div class="preview-overlay" onclick="removePreview(${index}, '${inputId}')">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="file-info">
                <div class="font-medium truncate">${fileName}</div>
                <div class="text-gray-300">${fileSize} MB</div>
            </div>
        `;

        div.innerHTML = thumbnail;
        return div;
    }

function removePreview(index, inputId) {
    if (!fileLists[inputId]) return;

    fileLists[inputId].splice(index, 1);
    updatePreviewAndInput(inputId);
}

function updatePreviewAndInput(inputId) {
    const previewContainer = document.getElementById(`preview-container-${inputId}`);
    const previewGrid = document.getElementById(`preview-grid-${inputId}`);
    const input = document.getElementById(inputId);

    previewGrid.innerHTML = '';

    const files = fileLists[inputId] || [];

    const isMultiple = input.hasAttribute('multiple');

    // Jika multiple false, batasi hanya satu file
    let filesToShow = files;
    if (!isMultiple && files.length > 1) {
        filesToShow = [files[files.length - 1]]; // hanya ambil file terakhir
        fileLists[inputId] = filesToShow; // sinkronkan juga array fileLists
    }

    console.log('Files to preview:', filesToShow);

    // Update input.files dengan DataTransfer
    const dt = new DataTransfer();
    filesToShow.forEach(file => dt.items.add(file));
    input.files = dt.files;

    filesToShow.forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function(e) {
            const previewItem = createPreviewItem(e.target.result, file, index, inputId);
            previewGrid.appendChild(previewItem);
        };

        reader.readAsDataURL(file);
    });

    if (filesToShow.length > 0) {
        previewContainer.classList.remove('hidden');
    } else {
        previewContainer.classList.add('hidden');
    }
}

function clearFiles(inputId) {
    const input = document.getElementById(inputId);
    const previewContainer = document.getElementById(`preview-container-${inputId}`);
    const previewGrid = document.getElementById(`preview-grid-${inputId}`);

    input.value = '';
    previewContainer.classList.add('hidden');
    previewGrid.innerHTML = '';
    fileLists[inputId] = [];
}

    // File size validation
    function validateFileSize(file, maxSizeMB = 5) {
        const fileSizeMB = file.size / 1024 / 1024;
        return fileSizeMB <= maxSizeMB;
    }

    // Show toast notification
    function showToast(message, type = 'error') {
        // You can implement your toast notification system here
        console.log(`${type.toUpperCase()}: ${message}`);
    }
</script>
