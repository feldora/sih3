@extends('layouts.admin')

@section('title', 'Data Cekungan Air Tanah')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Data Cekungan Air Tanah</h1>
            <a href="{{ route('admin.cat.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Tambah Data
            </a>
        </div>

        <div class="overflow-x-auto">
            <table id="cekunganTable" class="table table-striped table-hover w-full">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama CAT</th>
                        <th>Luas CAT</th>
                        <th>Potensi Air Tanah Bebas</th>
                        <th>Potensi Air Tanah Tertekan</th>
                        {{-- <th>Total Potensi</th> --}}
                        {{-- <th>Tanggal Dibuat</th> --}}
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
        <p class="py-4">Apakah Anda yakin ingin menghapus data ini?</p>
        <div class="modal-action">
            <button class="btn" onclick="closeDeleteModal()">Batal</button>
            <button id="confirmDelete" class="btn btn-error">Hapus</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    window.addEventListener('load', function() {
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded yet.');
            return;
        }

        let deleteId = null;
        
        // Initialize DataTable
        const table = $('#cekunganTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.cat.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'nama_cat', name: 'nama_cat'},
                {data: 'luas_cat_ha', name: 'luas_cat_ha'},
                {data: 'potensi_air_tanah_bebas', name: 'potensi_air_tanah_bebas'},
                {data: 'potensi_air_tanah_tertekan', name: 'potensi_air_tanah_tertekan'},
                // {data: 'total_potensi', name: 'total_potensi', orderable: false, searchable: false},
                // {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                processing: "Memproses...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                search: "Cari:",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            pageLength: 10,
            responsive: true
        });

        // Delete function
        window.deleteData = function(id) {
            deleteId = id;
            document.getElementById('deleteModal').classList.add('modal-open');
        };

        window.closeDeleteModal = function() {
            document.getElementById('deleteModal').classList.remove('modal-open');
            deleteId = null;
        };

        // Confirm delete
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (deleteId) {
                fetch(`{{ url('admin/cekungan-air-tanah') }}/${deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        table.ajax.reload();
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    showToast('Terjadi kesalahan saat menghapus data', 'error');
                })
                .finally(() => {
                    closeDeleteModal();
                });
            }
        });

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