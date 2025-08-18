@extends('layouts.admin')

@section('title', 'Data Instansi')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Data Instansi</h1>
                <p class="text-base-content/70 mt-1">Kelola data instansi/organisasi</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openAddModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Instansi
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table id="instansiTable" class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th class="text-center">No</th>
                        <th>Nama Instansi</th>
                        <th>Singkatan</th>
                        <th class="text-center">Jumlah User</th>
                        {{-- <th class="text-center">Tanggal Dibuat</th> --}}
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
<dialog id="instansiModal" class="modal">
    <div class="modal-box w-11/12 max-w-md">
        <h3 class="font-bold text-lg mb-4" id="modalTitle">Tambah Instansi</h3>
        
        <form id="instansiForm">
            <input type="hidden" id="instansiId" name="id">
            
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Nama Instansi <span class="text-error">*</span></span>
                </label>
                <input type="text" id="nama" name="nama" placeholder="Masukkan nama instansi" 
                       class="input input-bordered w-full" required>
                <label class="label">
                    <span class="label-text-alt text-error" id="namaError"></span>
                </label>
            </div>

            <div class="form-control mb-6">
                <label class="label">
                    <span class="label-text">Singkatan</span>
                </label>
                <input type="text" id="singkatan" name="singkatan" placeholder="Masukkan singkatan" 
                       class="input input-bordered w-full">
                <label class="label">
                    <span class="label-text-alt text-error" id="singkatanError"></span>
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
        <p class="mb-4">Apakah Anda yakin ingin menghapus instansi <strong id="deleteInstansiName"></strong>?</p>
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

@push('styles')
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css"> --}}
<style>
    /* Custom DataTables styling for DaisyUI */
    .dataTables_wrapper {
        font-family: inherit;
    }
    
    .dataTables_length select,
    .dataTables_filter input {
        @apply input input-bordered input-sm;
    }
    
    .dataTables_info,
    .dataTables_paginate {
        @apply text-sm text-base-content/70;
    }
    
    .paginate_button {
        @apply btn btn-sm btn-ghost mx-1;
    }
    
    .paginate_button.current {
        @apply btn-active;
    }
    
    .paginate_button.disabled {
        @apply btn-disabled;
    }
</style>
@endpush

@push('scripts')
{{-- <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script> --}}
<script>
    let table;
    let currentInstansiId = null;

    document.addEventListener('DOMContentLoaded', function() {
        initDataTable();
        setupFormSubmit();
        setupDeleteConfirm();
    });

    function initDataTable() {
        table = new DataTable('#instansiTable', {
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.instansi.getData") }}',
                type: 'GET',
                error: function(xhr) {
                    console.error('Error loading data:', xhr);
                    showToast('Error loading data', 'error');
                }
            },
            columns: [
                { data: 'no', searchable: false, orderable: false, className: 'text-center' },
                { data: 'nama' },
                { data: 'singkatan' },
                { data: 'users_count', searchable: false, className: 'text-center' },
                // { data: 'created_at', searchable: false, className: 'text-center' },
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
                                    <li><a onclick="editInstansi(${data})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a></li>
                                    <li><a onclick="deleteInstansi(${data}, '${row.nama}')" class="text-error">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </a></li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                processing: "Memuat data...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                },
                emptyTable: "Tidak ada data yang tersedia"
            }
        });
    }

    function openAddModal() {
        resetForm();
        document.getElementById('modalTitle').textContent = 'Tambah Instansi';
        document.getElementById('instansiModal').showModal();
    }

    function editInstansi(id) {
        fetch(`/admin/instansi/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modalTitle').textContent = 'Edit Instansi';
                    document.getElementById('instansiId').value = data.data.id;
                    document.getElementById('nama').value = data.data.nama;
                    document.getElementById('singkatan').value = data.data.singkatan || '';
                    currentInstansiId = data.data.id;
                    document.getElementById('instansiModal').showModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error loading data', 'error');
            });
    }

    function deleteInstansi(id, nama) {
        currentInstansiId = id;
        document.getElementById('deleteInstansiName').textContent = nama;
        document.getElementById('deleteModal').showModal();
    }

    function setupFormSubmit() {
        document.getElementById('instansiForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            clearErrors();
            setLoading(true);
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            const isEdit = currentInstansiId !== null;
            const url = isEdit ? `/admin/instansi/${currentInstansiId}` : '/admin/instansi';
            const method = isEdit ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
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
            if (currentInstansiId === null) return;
            
            setDeleteLoading(true);
            
            fetch(`/admin/instansi/${currentInstansiId}`, {
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
        document.getElementById('instansiModal').close();
        resetForm();
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').close();
        currentInstansiId = null;
    }

    function resetForm() {
        document.getElementById('instansiForm').reset();
        document.getElementById('instansiId').value = '';
        currentInstansiId = null;
        clearErrors();
    }

    function clearErrors() {
        document.getElementById('namaError').textContent = '';
        document.getElementById('singkatanError').textContent = '';
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

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Handle existing session messages
    window.addEventListener('load', function() {
        @if (session('error'))
            showToast("{{ session('error') }}", "error");
        @endif

        @if (session('success'))
            showToast("{{ session('success') }}", "success");
        @endif

        @if (session('warning'))
            showToast("{{ session('warning') }}", "warning");
        @endif
    });
</script>
@endpush