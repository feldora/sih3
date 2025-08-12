@extends('layouts.admin')

@section('title', 'Edit Data Sungai')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Data Sungai</h1>
                <p class="text-gray-600 mt-1">Perbarui informasi data sungai dan dokumen terkait</p>
            </div>
            <a href="{{ route('admin.sungai.index') }}" class="btn btn-ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form id="editSungaiForm" action="{{ route('admin.sungai.update', $sungai->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <!-- Nama Sungai -->
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Nama Sungai <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="nama_sungai" class="input input-bordered w-full @error('nama_sungai') input-error @enderror" 
                               value="{{ old('nama_sungai', $sungai->nama_sungai) }}" placeholder="Masukkan nama sungai" required>
                        @error('nama_sungai')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Panjang Sungai -->
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Panjang Sungai (km)</span>
                        </label>
                        <input type="number" name="panjang_sungai" 
                               class="input input-bordered w-full @error('panjang_sungai') input-error @enderror" 
                               value="{{ old('panjang_sungai', $sungai->panjang_sungai) }}" placeholder="0.00">
                        @error('panjang_sungai')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Luas DAS -->
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Luas DAS (km²)</span>
                        </label>
                        <input type="number" name="luas_das" 
                               class="input input-bordered w-full @error('luas_das') input-error @enderror" 
                               value="{{ old('luas_das', $sungai->luas_das) }}" placeholder="0.00">
                        @error('luas_das')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Ordo -->
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Ordo Sungai</span>
                        </label>
                        <select name="ordo" class="select select-bordered w-full @error('ordo') select-error @enderror">
                            <option value="">Pilih Ordo Sungai</option>
                            @for($i = 1; $i <= 9; $i++)
                                <option value="{{ $i }}" {{ old('ordo', $sungai->ordo) == $i ? 'selected' : '' }}>
                                    Ordo {{ $i }}
                                </option>
                            @endfor
                        </select>
                        @error('ordo')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <!-- Dokumen yang Ada -->
                    @if($sungai->getMedia('dokumen sungai')->count() > 0)
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Dokumen Tersedia</span>
                        </label>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($sungai->getMedia('dokumen sungai') as $media)
                            <div class="flex items-center justify-between p-3 border border-base-300 rounded-lg bg-base-50">
                                <div class="flex items-center space-x-3 cursor-pointer clickAble" data-mediaUrl="{{ route('admin.sungai.media.download', [$sungai->id, $media->id]) }}">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-10 h-10 bg-primary text-primary-content flex items-center justify-center">
                                            <x-document-icon :extension="$media->extension" size="full"  />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-sm">{{ $media->name }}</div>
                                        <div class="text-xs text-gray-500">{{ number_format($media->size / 1024, 2) }} KB</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <label class="cursor-pointer flex items-center">
                                        <input type="checkbox" name="delete_media[]" value="{{ $media->id }}" 
                                               class="checkbox checkbox-error checkbox-xs">
                                        <span class="ml-1 text-xs text-error">Hapus</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <label class="label">
                            <span class="label-text-alt text-gray-500">Centang file yang ingin dihapus</span>
                        </label>
                    </div>
                    @endif

                    <!-- Upload Dokumen Baru -->
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Tambah Dokumen Baru</span>
                        </label>
                        <input type="file" name="documents[]" multiple 
                               class="file-input file-input-bordered w-full @error('documents.*') file-input-error @enderror" 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <label class="label">
                            <span class="label-text-alt text-gray-500 text-xs">
                                Format: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG. Maksimal 10MB per file.
                            </span>
                        </label>
                        @error('documents.*')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Preview Files -->
                    <div id="filePreview" class="space-y-2 hidden">
                        <label class="label">
                            <span class="label-text font-medium">File yang akan diupload:</span>
                        </label>
                        <div id="fileList" class="space-y-2 max-h-32 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-base-300">
                <a href="{{ route('admin.sungai.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-success">Berhasil!</h3>
        <p class="py-4">Data sungai berhasil diperbarui.</p>
        <div class="modal-action">
            <a href="{{ route('admin.sungai.index') }}" class="btn btn-primary">Kembali ke Daftar</a>
            <button id="closeSuccessModal" class="btn btn-ghost">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .file-preview-item {
        transition: all 0.2s ease-in-out;
    }
    
    .file-preview-item:hover {
        transform: translateX(4px);
    }
    
    .checkbox:checked {
        animation: bounce 0.3s ease-in-out;
    }
    
    @keyframes bounce {
        0%, 20%, 60%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-3px);
        }
        80% {
            transform: translateY(-1px);
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editSungaiForm');
    const submitBtn = document.getElementById('submitBtn');
    const fileInput = document.querySelector('input[name="documents[]"]');
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');
    const successModal = document.getElementById('successModal');

    // Toast function
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-top toast-end`;
        toast.innerHTML = `
            <div class="alert alert-${type}">
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Session messages
    @if (session('error'))
        showToast("{{ session('error') }}", "error");
    @endif

    @if (session('success'))
        showToast("{{ session('success') }}", "success");
    @endif

    @if (session('warning'))
        showToast("{{ session('warning') }}", "warning");
    @endif

    // File preview functionality
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        
        if (files.length > 0) {
            filePreview.classList.remove('hidden');
            fileList.innerHTML = '';
            
            files.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-preview-item flex items-center justify-between p-2 border border-base-300 rounded bg-base-50';
                
                const fileInfo = document.createElement('div');
                fileInfo.className = 'flex items-center space-x-2';
                
                // File icon based on extension
                const extension = file.name.split('.').pop().toLowerCase();
                let icon = '📎';
                if (['jpg', 'jpeg', 'png'].includes(extension)) icon = '📷';
                else if (extension === 'pdf') icon = '📄';
                else if (['doc', 'docx'].includes(extension)) icon = '📝';
                else if (['xls', 'xlsx'].includes(extension)) icon = '📊';
                
                fileInfo.innerHTML = `
                    <span class="text-lg">${icon}</span>
                    <div>
                        <div class="text-sm font-medium">${file.name}</div>
                        <div class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</div>
                    </div>
                `;
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-ghost btn-xs text-error';
                removeBtn.innerHTML = '✕';
                removeBtn.addEventListener('click', function() {
                    // Remove file from input
                    const dt = new DataTransfer();
                    files.forEach((f, i) => {
                        if (i !== index) dt.items.add(f);
                    });
                    fileInput.files = dt.files;
                    
                    // Remove from preview
                    fileItem.remove();
                    
                    // Hide preview if no files
                    if (fileList.children.length === 0) {
                        filePreview.classList.add('hidden');
                    }
                });
                
                fileItem.appendChild(fileInfo);
                fileItem.appendChild(removeBtn);
                fileList.appendChild(fileItem);
            });
        } else {
            filePreview.classList.add('hidden');
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="loading loading-spinner loading-sm mr-2"></span>
            Memperbarui...
        `;
        
        // Create FormData for AJAX submission
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // If response is not JSON, it's likely an error page
                const text = await response.text();
                console.error('Non-JSON response received:', text);
                throw new Error('Server error occurred. Please check the console for details.');
            }
            
            const data = await response.json();
            
            if (!response.ok) {
                // Handle validation errors
                if (response.status === 422 && data.errors) {
                    // Display validation errors
                    Object.keys(data.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('input-error', 'select-error', 'file-input-error');
                            
                            // Show error message
                            const errorContainer = input.closest('.form-control');
                            let errorLabel = errorContainer.querySelector('.label-text-alt.text-error');
                            if (!errorLabel) {
                                const label = document.createElement('label');
                                label.className = 'label';
                                label.innerHTML = `<span class="label-text-alt text-error">${data.errors[field][0]}</span>`;
                                errorContainer.appendChild(label);
                            } else {
                                errorLabel.textContent = data.errors[field][0];
                                errorLabel.parentNode.style.display = 'block';
                            }
                        }
                    });
                    throw new Error('Please check the form for validation errors.');
                } else {
                    throw new Error(data.message || 'Server error occurred');
                }
            }
            
            return data;
        })
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Data berhasil diperbarui', 'success');
                // Optional: Show success modal or redirect
                setTimeout(() => {
                    window.location.href = "{{ route('admin.sungai.index') }}";
                }, 1500);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast(error.message || 'Terjadi kesalahan saat memperbarui data', 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Perbarui Data
            `;
        });
    });

    // Modal close functionality
    document.getElementById('closeSuccessModal')?.addEventListener('click', function() {
        successModal.classList.remove('modal-open');
    });

    // Close modal when clicking outside
    successModal?.addEventListener('click', function(e) {
        if (e.target === successModal) {
            successModal.classList.remove('modal-open');
        }
    });

    // Form validation
    form.addEventListener('input', function(e) {
        const target = e.target;
        
        // Remove error classes on input
        if (target.classList.contains('input-error')) {
            target.classList.remove('input-error');
        }
        if (target.classList.contains('select-error')) {
            target.classList.remove('select-error');
        }
        if (target.classList.contains('file-input-error')) {
            target.classList.remove('file-input-error');
        }
        
        // Find and hide error message
        const errorLabel = target.parentNode.querySelector('.label-text-alt.text-error');
        if (errorLabel) {
            errorLabel.parentNode.style.display = 'none';
        }
    });

    document.querySelectorAll('.clickAble').forEach(function (el) {
        el.addEventListener('click', function () {
            const mediaUrl = el.getAttribute('data-mediaUrl');
            if (mediaUrl) {
                window.open(mediaUrl, '_blank');
            }
        });
    });

});
</script>
@endpush