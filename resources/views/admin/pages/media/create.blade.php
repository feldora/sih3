@extends('layouts.admin')

@section('title', 'Upload Media')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.media.index') }}" class="btn btn-ghost btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
        <div>
            <h1 class="text-3xl font-bold text-base-content">Upload Media</h1>
            <p class="text-base-content/70 mt-1">Upload file media baru ke library</p>
        </div>
    </div>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" id="upload-form">
            @csrf
            
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <!-- Collection Name -->
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text">Collection Name (Opsional)</span>
                            <span class="label-text-alt">Kelompokkan file dalam collection</span>
                        </label>
                        <input type="text" name="collection" value="{{ old('collection') }}" 
                               placeholder="e.g., avatars, products, documents" 
                               class="input input-bordered @error('collection') input-error @enderror">
                        @error('collection')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- File Upload Area -->
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text">Upload Files</span>
                            <span class="label-text-alt">Max 10MB per file</span>
                        </label>
                        
                        <div class="file-upload-area border-2 border-dashed border-base-300 rounded-lg p-8 text-center transition-colors hover:border-primary"
                             id="upload-area">
                            <svg class="w-12 h-12 text-base-content/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-base-content/70 mb-2">Drop files here or click to browse</h3>
                            <p class="text-base-content/50 mb-4">Support: Images, Videos, Audio, Documents</p>
                            <input type="file" name="files[]" multiple accept="*/*" 
                                   class="file-input file-input-bordered file-input-primary w-full max-w-xs @error('files') file-input-error @enderror"
                                   id="file-input">
                        </div>
                        
                        @error('files')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror

                        @if($errors->has('files.*'))
                            <div class="alert alert-error mt-2">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->get('files.*') as $messages)
                                        @foreach($messages as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <!-- File Preview -->
                    <div id="file-preview" class="hidden mb-6">
                        <label class="label">
                            <span class="label-text">File yang akan diupload:</span>
                        </label>
                        <div id="preview-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- File previews will be inserted here -->
                        </div>
                    </div>

                    <!-- Upload Progress -->
                    <div id="upload-progress" class="hidden mb-6">
                        <label class="label">
                            <span class="label-text">Upload Progress</span>
                        </label>
                        <progress class="progress progress-primary w-full" value="0" max="100"></progress>
                        <div class="text-center mt-2">
                            <span id="progress-text">0%</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('admin.media.index') }}" class="btn btn-ghost">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="upload-btn" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Upload Files
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-input');
    const uploadArea = document.getElementById('upload-area');
    const filePreview = document.getElementById('file-preview');
    const previewContainer = document.getElementById('preview-container');
    const uploadBtn = document.getElementById('upload-btn');
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = uploadProgress.querySelector('progress');
    const progressText = document.getElementById('progress-text');
    const uploadForm = document.getElementById('upload-form');

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-primary', 'bg-primary/5');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-primary', 'bg-primary/5');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-primary', 'bg-primary/5');
        
        const files = e.dataTransfer.files;
        fileInput.files = files;
        handleFileSelection(files);
    });

    // Click to browse
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // File input change
    fileInput.addEventListener('change', function() {
        handleFileSelection(this.files);
    });

    function handleFileSelection(files) {
        if (files.length === 0) {
            filePreview.classList.add('hidden');
            uploadBtn.disabled = true;
            return;
        }

        previewContainer.innerHTML = '';
        filePreview.classList.remove('hidden');
        uploadBtn.disabled = false;

        Array.from(files).forEach((file, index) => {
            const fileCard = createFilePreview(file, index);
            previewContainer.appendChild(fileCard);
        });
    }

    function createFilePreview(file, index) {
        const div = document.createElement('div');
        div.className = 'card bg-base-200 shadow-sm';
        
        const isImage = file.type.startsWith('image/');
        const isVideo = file.type.startsWith('video/');
        const isAudio = file.type.startsWith('audio/');
        
        let preview = '';
        if (isImage) {
            const url = URL.createObjectURL(file);
            preview = `<img src="${url}" alt="${file.name}" class="w-full h-32 object-cover rounded-t-lg">`;
        } else if (isVideo) {
            preview = `
                <div class="w-full h-32 bg-base-300 rounded-t-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>`;
        } else if (isAudio) {
            preview = `
                <div class="w-full h-32 bg-base-300 rounded-t-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-secondary" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                    </svg>
                </div>`;
        } else {
            const ext = file.name.split('.').pop().toUpperCase();
            preview = `
                <div class="w-full h-32 bg-base-300 rounded-t-lg flex flex-col items-center justify-center">
                    <svg class="w-8 h-8 text-accent mb-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                    </svg>
                    <span class="text-xs font-medium">${ext}</span>
                </div>`;
        }

        div.innerHTML = `
            ${preview}
            <div class="card-body p-3">
                <h3 class="text-sm font-medium truncate" title="${file.name}">${file.name}</h3>
                <p class="text-xs text-base-content/70">${formatFileSize(file.size)}</p>
                <div class="flex justify-between items-center mt-2">
                    <span class="badge badge-outline badge-xs">${file.type || 'Unknown'}</span>
                    <button type="button" onclick="removeFile(${index})" class="btn btn-ghost btn-xs text-error">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        return div;
    }

    // Remove file function
    window.removeFile = function(index) {
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        
        files.splice(index, 1);
        
        files.forEach(file => {
            dt.items.add(file);
        });
        
        fileInput.files = dt.files;
        handleFileSelection(fileInput.files);
    };

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submission with progress
    uploadForm.addEventListener('submit', function(e) {
        if (fileInput.files.length === 0) {
            e.preventDefault();
            alert('Pilih file untuk diupload terlebih dahulu.');
            return;
        }

        // Show progress
        uploadProgress.classList.remove('hidden');
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = `
            <span class="loading loading-spinner loading-sm"></span>
            Uploading...
        `;

        // Simulate progress (since we can't track real progress with regular form submission)
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) {
                progress = 90;
                clearInterval(interval);
            }
            progressBar.value = progress;
            progressText.textContent = Math.round(progress) + '%';
        }, 200);
    });
});
</script>
@endpush
@endsection