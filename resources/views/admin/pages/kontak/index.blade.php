@extends('layouts.admin')

@section('title', 'Data Kontak')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Data Kontak</h1>
                <p class="text-base-content/70 mt-1">Kelola data kontak per instansi</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openAddModal()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kontak
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table id="kontakTable" class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th class="text-center">No</th>
                        <th>Instansi</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telp</th>
                        <th>Website</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will load data here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<dialog id="kontakModal" class="modal">
    <div class="modal-box w-11/12 max-w-lg">
        <h3 class="font-bold text-lg mb-4" id="modalTitle">Tambah Kontak</h3>
        
        <form id="kontakForm">
            <input type="hidden" id="kontakId" name="id">
            
            <div class="form-control mb-4">
                <label class="label"><span class="label-text">Instansi</span></label>
                <select id="instansi_id" name="instansi_id" class="select select-bordered w-full">
                    <option value="">-- Pilih Instansi --</option>
                    @foreach(\App\Models\Instansi::all() as $instansi)
                        <option value="{{ $instansi->id }}">{{ $instansi->nama }}</option>
                    @endforeach
                </select>
                <label class="label"><span class="label-text-alt text-error" id="instansi_idError"></span></label>
            </div>

            <div class="form-control mb-4">
                <label class="label"><span class="label-text">Nama <span class="text-error">*</span></span></label>
                <input type="text" id="nama" name="nama" class="input input-bordered w-full" required>
                <label class="label"><span class="label-text-alt text-error" id="namaError"></span></label>
            </div>

            <div class="form-control mb-4">
                <label class="label"><span class="label-text">Email</span></label>
                <input type="email" id="email" name="email" class="input input-bordered w-full">
                <label class="label"><span class="label-text-alt text-error" id="emailError"></span></label>
            </div>

            <div class="form-control mb-4">
                <label class="label"><span class="label-text">Telp</span></label>
                <input type="text" id="telp" name="telp" class="input input-bordered w-full">
                <label class="label"><span class="label-text-alt text-error" id="telpError"></span></label>
            </div>

            <div class="form-control mb-4">
                <label class="label"><span class="label-text">Website</span></label>
                <input type="text" id="website" name="website" class="input input-bordered w-full">
                <label class="label"><span class="label-text-alt text-error" id="websiteError"></span></label>
            </div>

            <div class="form-control mb-4">
                <label class="label"><span class="label-text">Alamat</span></label>
                <textarea id="alamat" name="alamat" class="textarea textarea-bordered w-full"></textarea>
                <label class="label"><span class="label-text-alt text-error" id="alamatError"></span></label>
            </div>

            <div class="form-control mb-6">
                <label class="label"><span class="label-text">Keterangan</span></label>
                <textarea id="keterangan" name="keterangan" class="textarea textarea-bordered w-full"></textarea>
                <label class="label"><span class="label-text-alt text-error" id="keteranganError"></span></label>
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
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<!-- Modal Confirm Delete -->
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-warning mb-4">Konfirmasi Hapus</h3>
        <p class="mb-4">Yakin ingin menghapus kontak <strong id="deleteKontakName"></strong>?</p>
        <div class="modal-action">
            <button class="btn" onclick="closeDeleteModal()">Batal</button>
            <button class="btn btn-error" id="confirmDeleteBtn">
                <span class="loading loading-spinner loading-sm hidden" id="deleteLoadingSpinner"></span>
                Ya, Hapus
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>
@endsection

@push('scripts')
<script>
    let table;
    let currentKontakId = null;

    document.addEventListener('DOMContentLoaded', function() {
        initDataTable();
        setupFormSubmit();
        setupDeleteConfirm();
    });

    function initDataTable() {
        table = new DataTable('#kontakTable', {
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.kontak.getData") }}',
            columns: [
                { data: 'no', className: 'text-center', orderable: false, searchable: false },
                { data: 'instansi' },
                { data: 'nama' },
                { data: 'email' },
                { data: 'telp' },
                { data: 'website' },
                {
                    data: 'action',
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-ghost btn-xs" onclick="editKontak(${data})">Edit</button>
                            <button class="btn btn-error btn-xs" onclick="deleteKontak(${data}, '${row.nama}')">Hapus</button>
                        `;
                    }
                }
            ]
        });
    }

    function openAddModal() {
        resetForm();
        document.getElementById('modalTitle').textContent = 'Tambah Kontak';
        document.getElementById('kontakModal').showModal();
    }

    function editKontak(id) {
        fetch(`/admin/kontak/${id}/edit`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('modalTitle').textContent = 'Edit Kontak';
                    Object.keys(res.data).forEach(key => {
                        if (document.getElementById(key)) {
                            document.getElementById(key).value = res.data[key] ?? '';
                        }
                    });
                    currentKontakId = res.data.id;
                    document.getElementById('kontakModal').showModal();
                }
            });
    }

    function deleteKontak(id, nama) {
        currentKontakId = id;
        document.getElementById('deleteKontakName').textContent = nama;
        document.getElementById('deleteModal').showModal();
    }

    function setupFormSubmit() {
        document.getElementById('kontakForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = Object.fromEntries(new FormData(this));
            const url = currentKontakId ? `/admin/kontak/${currentKontakId}` : '/admin/kontak';
            const method = currentKontakId ? 'PUT' : 'POST';

            fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    table.ajax.reload();
                    closeModal();
                } else {
                    alert(res.message);
                }
            });
        });
    }

    function setupDeleteConfirm() {
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!currentKontakId) return;
            fetch(`/admin/kontak/${currentKontakId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    table.ajax.reload();
                    closeDeleteModal();
                } else {
                    alert(res.message);
                }
            });
        });
    }

    function closeModal() {
        document.getElementById('kontakModal').close();
        resetForm();
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').close();
        currentKontakId = null;
    }

    function resetForm() {
        document.getElementById('kontakForm').reset();
        currentKontakId = null;
    }
</script>
@endpush
