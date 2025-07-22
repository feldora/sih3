@extends('layouts.admin')

@section('title', 'Import Pos')

@section('content')
    <div class="container mx-auto p-6">
        <div class="card bg-base-100 card-md shadow-sm">
            <div class="card-body">
                <h1 class="text-2xl font-bold mb-6 text-base-content">
                    <i class="fas fa-file-import text-primary mr-2"></i>
                    Import Pos
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
                                        Format yang didukung: .xlsx, .xls, .csv (Max: 5MB)
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
                                <button 
                                    type="button" 
                                    id="viewBtn" 
                                    class="btn btn-secondary btn-md"
                                    disabled
                                >
                                    <i class="fas fa-eye mr-2"></i>
                                    View Data
                                </button>
                                <button 
                                    type="button" 
                                    id="importBtn" 
                                    class="btn btn-primary btn-md"
                                    disabled
                                >
                                    <i class="fas fa-upload mr-2"></i>
                                    Import Data
                                </button>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="">
                            {{-- <div class="card bg-base-200 shadow-sm">
                                <div class="card-body">
                                    <h3 class="font-bold text-base-content mb-3">
                                        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                                        Petunjuk Import
                                    </h3>
                                    <ul class="text-sm space-y-2 text-base-content/80">
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-success mr-2 mt-1"></i>
                                            File harus berformat Excel (.xlsx, .xls) atau CSV
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-success mr-2 mt-1"></i>
                                            Baris pertama harus berisi header kolom
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-success mr-2 mt-1"></i>
                                            Pastikan format data sesuai dengan template
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-success mr-2 mt-1"></i>
                                            Maksimal ukuran file 5MB
                                        </li>
                                    </ul>
                                </div>
                            </div> --}}

                            <!-- Download Template -->
                            <div class="card bg-primary/10 shadow-sm">
                                <div class="card-body">
                                    <h3 class="font-bold text-primary mb-3">
                                        <i class="fas fa-download mr-2"></i>
                                        Template Excel
                                    </h3>
                                    <p class="text-sm text-base-content/80 mb-3">
                                        Download template untuk memastikan format data yang benar
                                    </p>
                                    <a href="{{ route('admin.pos-pengamatan.import-excel.template') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download mr-2"></i>
                                        Download Template
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Data Preview Section -->
                <div id="dataPreview" class="hidden mt-8">
                    <div class="divider">
                        <span class="text-base-content font-semibold">Preview Data</span>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Preview Stats -->
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
                        
                        <!-- Data Table -->
                        <div class="card bg-base-100 shadow-sm">
                            <div class="card-body">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="font-bold text-base-content">
                                        <i class="fas fa-table mr-2"></i>
                                        Data Preview
                                    </h3>
                                    <div class="text-sm text-base-content/70">
                                        Menampilkan maksimal 100 baris pertama
                                    </div>
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
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
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

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-box">
            <div class="flex flex-col items-center space-y-4">
                <i class="fas fa-check-circle text-5xl text-success"></i>
                <h3 class="font-bold text-lg">Import Berhasil!</h3>
                <p id="successText">Data berhasil diimport ke sistem.</p>
                <div class="modal-action">
                    <button class="btn btn-primary" onclick="closeModal('successModal')">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-box">
            <div class="flex flex-col items-center space-y-4">
                <i class="fas fa-exclamation-circle text-5xl text-error"></i>
                <h3 class="font-bold text-lg">Import Gagal!</h3>
                <p id="errorText">Terjadi kesalahan saat mengimport data.</p>
                <div class="modal-action">
                    <button class="btn btn-error" onclick="closeModal('errorModal')">OK</button>
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

    // File input change handler
    document.getElementById('excelFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            currentFile = file;
            showFileInfo(file);
            document.getElementById('viewBtn').disabled = false;
            document.getElementById('importBtn').disabled = true;
            document.getElementById('dataPreview').classList.add('hidden');
        }
    });

    // View button handler
    document.getElementById('viewBtn').addEventListener('click', function() {
        if (currentFile) {
            processExcelFile(currentFile);
        }
    });

    // Import button handler
    document.getElementById('importBtn').addEventListener('click', function() {
        if (excelData) {
            importData();
        }
    });

    // Show file info
    function showFileInfo(file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);
        document.getElementById('fileInfo').classList.remove('hidden');
    }

    // Process Excel file
    function processExcelFile(file) {
        showModal('loadingModal');
        document.getElementById('loadingText').textContent = 'Membaca file Excel...';

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                if (jsonData.length > 0) {
                    const startRowIndex = 1;
                    const dataOnly = jsonData.slice(startRowIndex);
                    excelData = dataOnly;
                    displayPreview(jsonData);
                    document.getElementById('importBtn').disabled = false;
                    closeModal('loadingModal');
                    
                    // Show success toast
                    showToast('File berhasil dibaca!', 'success');
                } else {
                    throw new Error('File Excel kosong atau tidak valid');
                }
            } catch (error) {
                closeModal('loadingModal');
                showErrorModal('Error membaca file: ' + error.message);
            }
        };
        reader.readAsArrayBuffer(file);
    }

    // Display data preview
    function displayPreview(data) {
        const preview = document.getElementById('dataPreview');
        const tableHeader = document.getElementById('tableHeader');
        const tableBody = document.getElementById('tableBody');
        
        // Clear previous data
        tableHeader.innerHTML = '';
        tableBody.innerHTML = '';
        
        if (data.length === 0) return;
        
        // Update stats
        document.getElementById('totalRows').textContent = data.length - 1; // -1 for header
        document.getElementById('totalColumns').textContent = data[0].length;
        document.getElementById('previewFileSize').textContent = formatFileSize(currentFile.size);
        
        // Create header
        const headerRow = document.createElement('tr');
        data[0].forEach(header => {
            const th = document.createElement('th');
            th.textContent = header || 'Column';
            th.className = 'text-center';
            headerRow.appendChild(th);
        });
        tableHeader.appendChild(headerRow);
        
        // Create body (limit to 100 rows)
        const maxRows = Math.min(data.length, 101); // 100 + 1 for header
        for (let i = 1; i < maxRows; i++) {
            const row = document.createElement('tr');
            data[i].forEach(cell => {
                const td = document.createElement('td');
                td.textContent = cell || '';
                td.className = 'text-center';
                row.appendChild(td);
            });
            tableBody.appendChild(row);
        }
        
        preview.classList.remove('hidden');
    }

    // Import data to backend
    async function importData() {
        if (!excelData || !currentFile) return;

        showModal('loadingModal');
        document.getElementById('loadingText').textContent = 'Mengimport data...';
        
        try {
            const batchSize = 100; // atau 100
            const mappedData = excelData.slice(1).map(row => ({
                jenis_pos: row[7],
                nama_pos: row[0],
                latitude: row[3],
                longitude: row[4],
                tahun_pembangunan: row[5],
                kewenangan: row[2],
                status: row[6],
                wilayah_sungai: row[1]
            }));

            const chunks = chunkArray(mappedData, batchSize);
            let total = mappedData.length;
            let successCount = 0;
            document.getElementById('importProgress').classList.remove('hidden');
            for (let i = 0; i < chunks.length; i++) {
                const formData = new FormData();
                formData.append('data', JSON.stringify(chunks[i]));

                document.getElementById('loadingText').textContent = `Mengimpor batch ${i + 1} dari ${chunks.length}...`;

                const response = await fetch('{{ route("admin.pos-pengamatan.import-excel.process") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (!response.ok || result.success === false) {
                    throw new Error(result.message || `Batch ke-${i + 1} gagal`);
                }
                document.getElementById('importProgress').value = Math.round(((i + 1) / chunks.length) * 100);
                successCount += chunks[i].length;
            }

            closeModal('loadingModal');
            document.getElementById('successText').textContent = 
                `Berhasil mengimport ${successCount} dari ${total} data.`;
            showModal('successModal');

            setTimeout(() => {
                resetForm();
            }, 2000);
        } catch (error) {
            closeModal('loadingModal');
            showErrorModal(error.message);
        }
    }


    // Reset form
    function resetForm() {
        document.getElementById('excelFile').value = '';
        document.getElementById('fileInfo').classList.add('hidden');
        document.getElementById('dataPreview').classList.add('hidden');
        document.getElementById('viewBtn').disabled = true;
        document.getElementById('importBtn').disabled = true;
        excelData = null;
        currentFile = null;
    }

    // Utility functions
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showModal(modalId) {
        document.getElementById(modalId).classList.add('modal-open');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('modal-open');
    }

    function showErrorModal(message) {
        document.getElementById('errorText').textContent = message;
        showModal('errorModal');
    }

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

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('modal-open');
        }
    });

    function map_data(){

    }
</script>
@endpush