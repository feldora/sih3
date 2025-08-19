@extends('layouts.admin')

@section('title', 'Data Produk Hukum')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Data Produk Hukum</h1>
                <p class="text-base-content/70 mt-1">Kelola data produk hukum (peraturan, dokumen, dll)</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openAddModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk Hukum
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table id="produkHukumTable" class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th class="text-center">No</th>
                        <th>Tahun</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th class="text-center">File</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<dialog id="produkHukumModal" class="modal">
    <div class="modal-box w-11/12 max-w-lg">
        <h3 class="font-bold text-lg mb-4" id="modalTitle">Tambah Produk Hukum</h3>
        
        <form id="produkHukumForm" enctype="multipart/form-data">
            <input type="hidden" id="produkHukumId" name="id">
            
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Tahun <span class="text-error">*</span></span>
                </label>
                <input type="number" id="tahun" name="tahun" placeholder="Masukkan tahun" 
                       class="input input-bordered w-full" required>
                <label class="label">
                    <span class="label-text-alt text-error" id="tahunError"></span>
                </label>
            </div>

            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Judul <span class="text-error">*</span></span>
                </label>
                <input type="text" id="judul" name="judul" placeholder="Masukkan judul produk hukum" 
                       class="input input-bordered w-full" required>
                <label class="label">
                    <span class="label-text-alt text-error" id="judulError"></span>
                </label>
            </div>

            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Deskripsi</span>
                </label>
                <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi" 
                          class="textarea textarea-bordered w-full"></textarea>
                <label class="label">
                    <span class="label-text-alt text-error" id="deskripsiError"></span>
                </label>
            </div>

            <div class="form-control mb-6">
                <label class="label">
                    <span class="label-text">Upload File (PDF/DOC/DOCX)</span>
                </label>
                <input type="file" id="file" name="file" accept=".pdf,.doc,.docx" 
                       class="file-input file-input-bordered w-full">
                <label class="label">
                    <span class="label-text-alt text-error" id="fileError"></span>
                </label>
            </div>

            <div class="modal-action">
                <button type="button" class="btn" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="loading loading-spinner loading-sm hidden" id="loadingSpinner"></span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Modal Confirm Delete -->
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-warning mb-4">Konfirmasi Hapus</h3>
        <p class="mb-4">Apakah Anda yakin ingin menghapus produk hukum <strong id="deleteProdukHukumName"></strong>?</p>
        <p class="text-sm text-base-content/70 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
        
        <div class="modal-action">
            <button class="btn" onclick="closeDeleteModal()">Batal</button>
            <button class="btn btn-error" id="confirmDeleteBtn">
                <span class="loading loading-spinner loading-sm hidden" id="deleteLoadingSpinner"></span>
                Ya, Hapus
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
    let table;
    let currentProdukHukumId = null;

    document.addEventListener('DOMContentLoaded', function() {
        initDataTable();
        setupFormSubmit();
        setupDeleteConfirm();
    });

    function initDataTable() {
        table = new DataTable('#produkHukumTable', {
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.produk-hukum.getData") }}',
                type: 'GET',
                error: function(xhr) {
                    console.error('Error loading data:', xhr);
                    showToast('Error loading data', 'error');
                }
            },
            columns: [
                { data: 'no', searchable: false, orderable: false, className: 'text-center' },
                { data: 'tahun' },
                { data: 'judul' },
                { data: 'deskripsi' },
                { 
                    data: 'file',
                    searchable: false,
                    orderable: false,
                    className: 'text-center',
                    render: function(data) {
                        if (data === '-' || !data) return '-';
                        return `<a href="${data}" target="_blank" class="btn btn-xs btn-outline">Lihat File</a>`;
                    }
                },
                { 
                    data: 'action',
                    searchable: false,
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </div>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a onclick="editProdukHukum(${data})">Edit</a></li>
                                    <li><a onclick="deleteProdukHukum(${data}, '${row.judul}')" class="text-error">Hapus</a></li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ]
        });
    }

    function openAddModal() {
        resetForm();
        document.getElementById('modalTitle').textContent = 'Tambah Produk Hukum';
        document.getElementById('produkHukumModal').showModal();
    }

    function editProdukHukum(id) {
        fetch(`/admin/produk-hukum/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modalTitle').textContent = 'Edit Produk Hukum';
                    document.getElementById('produkHukumId').value = data.data.id;
                    document.getElementById('tahun').value = data.data.tahun;
                    document.getElementById('judul').value = data.data.judul;
                    document.getElementById('deskripsi').value = data.data.deskripsi || '';
                    currentProdukHukumId = data.data.id;
                    document.getElementById('produkHukumModal').showModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error loading data', 'error');
            });
    }

    function deleteProdukHukum(id, judul) {
        currentProdukHukumId = id;
        document.getElementById('deleteProdukHukumName').textContent = judul;
        document.getElementById('deleteModal').showModal();
    }

    function setupFormSubmit() {
        document.getElementById('produkHukumForm').addEventListener('submit', function(e) {
            e.preventDefault();
            clearErrors();
            setLoading(true);

            const formData = new FormData(this);
            const isEdit = currentProdukHukumId !== null;
            const url = isEdit ? `/admin/produk-hukum/${currentProdukHukumId}` : '/admin/produk-hukum';
            const method = isEdit ? 'POST' : 'POST'; // gunakan POST, Laravel form method spoofing utk PUT
            if (isEdit) formData.append('_method', 'PUT');

            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal();
                    table.ajax.reload();
                } else {
                    if (data.errors) {
                        showValidationErrors(data.errors);
                    } else {
                        showToast(data.message, 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menyimpan data', 'error');
            })
            .finally(() => {
                setLoading(false);
            });
        });
    }

    function setupDeleteConfirm() {
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentProdukHukumId === null) return;
            setDeleteLoading(true);

            fetch(`/admin/produk-hukum/${currentProdukHukumId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    closeDeleteModal();
                    table.ajax.reload();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menghapus data', 'error');
            })
            .finally(() => {
                setDeleteLoading(false);
            });
        });
    }

    function closeModal() {
        document.getElementById('produkHukumModal').close();
        resetForm();
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').close();
        currentProdukHukumId = null;
    }

    function resetForm() {
        document.getElementById('produkHukumForm').reset();
        document.getElementById('produkHukumId').value = '';
        currentProdukHukumId = null;
        clearErrors();
    }

    function clearErrors() {
        ['tahun','judul','deskripsi','file'].forEach(f => {
            const el = document.getElementById(f + 'Error');
            if (el) el.textContent = '';
        });
    }

    function showValidationErrors(errors) {
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById(field + 'Error');
            if (errorElement) {
                errorElement.textContent = errors[field][0];
            }
        });
    }

    function setLoading(loading) {
        const submitBtn = document.getElementById('submitBtn');
        const spinner = document.getElementById('loadingSpinner');
        if (loading) {
            submitBtn.disabled = true;
            spinner.classList.remove('hidden');
        } else {
            submitBtn.disabled = false;
            spinner.classList.add('hidden');
        }
    }

    function setDeleteLoading(loading) {
        const deleteBtn = document.getElementById('confirmDeleteBtn');
        const spinner = document.getElementById('deleteLoadingSpinner');
        if (loading) {
            deleteBtn.disabled = true;
            spinner.classList.remove('hidden');
        } else {
            deleteBtn.disabled = false;
            spinner.classList.add('hidden');
        }
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = 'toast toast-top toast-end';
        toast.innerHTML = `
            <div class="alert alert-${type}">
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => { toast.remove(); }, 3000);
    }
</script>
@endpush
