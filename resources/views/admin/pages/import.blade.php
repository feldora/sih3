@extends('layouts.admin')

@section('title', $title ?? 'Import Excel')

@php
    $routeTemplate = $route_template ?? '#';
    $routeProcess = $route_process ?? '#';
    $mappingJson = json_encode($mapping ?? []);
    $pageTitle = $title ?? 'Import Excel';
    $templateLabel = $template_label ?? 'Template Excel';
@endphp

@section('content')
<div class="container mx-auto p-6">
    <div class="card bg-base-100 card-md shadow-sm">
        <div class="card-body">
            <h1 class="text-2xl font-bold mb-6 text-base-content">
                <i class="fas fa-file-import text-primary mr-2"></i>
                {{ $pageTitle }}
            </h1>

            <!-- Form Import -->
            <form id="importForm" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- File Upload Section -->
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    <i class="fas fa-file-excel text-green-600 mr-1"></i>
                                    Pilih File Excel
                                </span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="file" 
                                    id="excelFile" 
                                    name="excel_file"
                                    accept=".xlsx,.xls,.csv"
                                    class="file-input file-input-bordered file-input-primary w-full"
                                    required
                                />
                            </div>
                            <label class="label">
                                <span class="label-text-alt text-base-content/70">
                                    Format: .xlsx, .xls, .csv (Max: 5MB)
                                </span>
                            </label>
                        </div>

                        <!-- File Info -->
                        <div id="fileInfo" class="hidden">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <div class="font-medium" id="fileName"></div>
                                    <div class="text-sm" id="fileSize"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="button" id="viewBtn" class="btn btn-secondary btn-md" disabled>
                                <i class="fas fa-eye mr-2"></i> View Data
                            </button>
                            <button type="button" id="importBtn" class="btn btn-primary btn-md" disabled>
                                <i class="fas fa-upload mr-2"></i> Import Data
                            </button>
                        </div>
                    </div>

                    <!-- Download Template -->
                    <div>
                        <div class="card bg-primary/10 shadow-sm">
                            <div class="card-body">
                                <h3 class="font-bold text-primary mb-3">
                                    <i class="fas fa-download mr-2"></i>
                                    {{ $templateLabel }}
                                </h3>
                                <p class="text-sm text-base-content/80 mb-3">
                                    Download template untuk memastikan format data yang benar
                                </p>
                                <a href="{{ $routeTemplate }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-download mr-2"></i>
                                    Download {{ $templateLabel }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Data Preview Section -->
            <div id="dataPreview" class="hidden mt-8">
                <div class="divider"><span class="text-base-content font-semibold">Preview Data</span></div>

                <div class="space-y-4">
                    <div class="stats stats-vertical lg:stats-horizontal shadow w-full">
                        <div class="stat">
                            <div class="stat-title">Total Rows</div>
                            <div class="stat-value text-primary" id="totalRows">0</div>
                            <div class="stat-desc">Data yang akan diimport</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">Columns</div>
                            <div class="stat-value text-secondary" id="totalColumns">0</div>
                            <div class="stat-desc">Kolom yang terdeteksi</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">File Size</div>
                            <div class="stat-value text-accent" id="previewFileSize">0</div>
                            <div class="stat-desc">Ukuran file</div>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow-sm">
                        <div class="card-body">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-bold text-base-content">
                                    <i class="fas fa-table mr-2"></i> Data Preview
                                </h3>
                                <div class="text-sm text-base-content/70">Menampilkan maksimal 100 baris pertama</div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="table table-zebra table-sm">
                                    <thead id="tableHeader"></thead>
                                    <tbody id="tableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modals -->
<!-- Modal Loading -->
<div id="loadingModal" class="modal">
    <div class="modal-box">
        <div class="flex flex-col items-center space-y-4">
            <span class="loading loading-spinner loading-lg text-primary"></span>
            <h3 class="font-bold text-lg">Processing...</h3>
            <p id="loadingText">Sedang memproses file...</p>
            <progress id="importProgress" class="progress progress-primary w-full my-4 hidden" value="0" max="100"></progress>
        </div>
    </div>
</div>

<!-- Modal Success -->
<div id="successModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Import Berhasil</h3>
        <p id="successText" class="py-4">Berhasil mengimport data.</p>
        <div class="modal-action">
            <button onclick="closeModal('successModal')" class="btn btn-primary">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Error -->
<div id="errorModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error">Terjadi Kesalahan</h3>
        <p id="errorText" class="py-4">Gagal memproses file.</p>
        <div class="modal-action">
            <button onclick="closeModal('errorModal')" class="btn btn-error">Tutup</button>
        </div>
    </div>
</div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
let excelData = null;
let currentFile = null;

// File selected
document.getElementById('excelFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        currentFile = file;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);
        document.getElementById('fileInfo').classList.remove('hidden');
        document.getElementById('viewBtn').disabled = false;
        document.getElementById('importBtn').disabled = true;
        document.getElementById('dataPreview').classList.add('hidden');
    }
});

// View button
document.getElementById('viewBtn').addEventListener('click', function() {
    if (currentFile) processExcelFile(currentFile);
});

// Import button
document.getElementById('importBtn').addEventListener('click', function() {
    if (excelData) importData();
});

// Excel reader
function processExcelFile(file) {
    showModal('loadingModal');
    document.getElementById('loadingText').textContent = 'Membaca file Excel...';
    const reader = new FileReader();

    reader.onload = function(e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];
            const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });
            if (jsonData.length === 0) throw new Error('File kosong');

            excelData = jsonData;
            displayPreview(jsonData);
            document.getElementById('importBtn').disabled = false;
            closeModal('loadingModal');
            showToast('File berhasil dibaca!', 'success');
        } catch (error) {
            closeModal('loadingModal');
            showErrorModal('Gagal membaca file: ' + error.message);
        }
    };
    reader.readAsArrayBuffer(file);
}

// Display preview
function displayPreview(data) {
    const headerEl = document.getElementById('tableHeader');
    const bodyEl = document.getElementById('tableBody');
    headerEl.innerHTML = '';
    bodyEl.innerHTML = '';

    document.getElementById('totalRows').textContent = data.length - 1;
    document.getElementById('totalColumns').textContent = data[0].length;
    document.getElementById('previewFileSize').textContent = formatFileSize(currentFile.size);

    const headerRow = document.createElement('tr');
    data[0].forEach(h => {
        const th = document.createElement('th');
        th.textContent = h;
        headerRow.appendChild(th);
    });
    headerEl.appendChild(headerRow);

    for (let i = 1; i < Math.min(data.length, 101); i++) {
        const row = document.createElement('tr');
        data[i].forEach(cell => {
            const td = document.createElement('td');
            td.textContent = cell || '';
            row.appendChild(td);
        });
        bodyEl.appendChild(row);
    }

    document.getElementById('dataPreview').classList.remove('hidden');
}

// Import to backend
async function importData() {
    const mapping = {!! $mappingJson !!};
    let mappedData;

    if (Object.keys(mapping).length > 0) {
        // Jika mapping tersedia → gunakan mapping manual
        mappedData = excelData.slice(1).map(row => {
            let obj = {};
            for (const [key, index] of Object.entries(mapping)) {
                obj[key] = row[index];
            }
            return obj;
        });
    } else {
        // Jika mapping kosong → gunakan header dari baris pertama
        const headers = excelData[0];
        mappedData = excelData.slice(1).map(row => {
            let obj = {};
            headers.forEach((key, index) => {
                obj[key] = row[index];
            });
            return obj;
        });
    }

    const batchSize = 100;
    const chunks = chunkArray(mappedData, batchSize);

    showModal('loadingModal');
    document.getElementById('loadingText').textContent = 'Mengimport data...';
    document.getElementById('importProgress').classList.remove('hidden');

    let success = 0;

    try {
        for (let i = 0; i < chunks.length; i++) {
            const formData = new FormData();
            formData.append('data', JSON.stringify(chunks[i]));

            const response = await fetch(`{{ $routeProcess }}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Import gagal');
            }

            success += chunks[i].length;
            document.getElementById('importProgress').value = Math.round(((i + 1) / chunks.length) * 100);
        }

        closeModal('loadingModal');
        document.getElementById('successText').textContent = `Berhasil mengimport ${success} data.`;
        showModal('successModal');
        resetForm();
    } catch (error) {
        closeModal('loadingModal');
        showErrorModal(error.message);
    }
}


function resetForm() {
    document.getElementById('excelFile').value = '';
    document.getElementById('fileInfo').classList.add('hidden');
    document.getElementById('dataPreview').classList.add('hidden');
    document.getElementById('viewBtn').disabled = true;
    document.getElementById('importBtn').disabled = true;
    excelData = null;
    currentFile = null;
}

function formatFileSize(bytes) {
    const k = 1024, sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
function chunkArray(arr, size) {
    return Array.from({ length: Math.ceil(arr.length / size) }, (_, i) =>
        arr.slice(i * size, i * size + size)
    );
}
function showModal(id) {
    document.getElementById(id).classList.add('modal-open');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('modal-open');
}
function showErrorModal(msg) {
    document.getElementById('errorText').textContent = msg;
    showModal('errorModal');
}
function showToast(msg, type = 'info') {
    const div = document.createElement('div');
    div.className = 'toast toast-top toast-end';
    div.innerHTML = `<div class="alert alert-${type}"><span>${msg}</span></div>`;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}


</script>
@endpush

@push('scripts')
<script>
    @if (session('error'))
        showToast("{{ session('error') }}", "error");
    @endif

    @if (session('success'))
        showToast("{{ session('success') }}", "success");
    @endif

    @if (session('warning'))
        showToast("{{ session('warning') }}", "warning");
    @endif
</script>
@endpush