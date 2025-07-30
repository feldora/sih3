@extends('layouts.admin')

@section('title', 'Data Hujan Harian')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-header px-6 py-4 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="card-title text-xl font-bold">Data Curah Hujan</h3>

    <div class="flex justify-end items-center mb-6 gap-5">
        <a href="{{ route('admin.meteorologi.curah-hujan.import-excel.form') }}?type=Curah Hujan" class="btn btn-sm btn-info">Import Excel</a>
        <a href="{{ route('admin.meteorologi.curah-hujan.create') }}" class="btn btn-primary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Data
        </a>
    </div>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="overflow-x-auto">
                <table id="dataTable" class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-center">No</th>
                            <th>Pos Pantau</th>
                            <th>Tanggal</th>
                            <th>Curah Hujan (mm)</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
            <button type="button" class="btn btn-sm btn-circle btn-ghost" onclick="closeDeleteModal()">✕</button>
        </div>
        <div class="py-4">
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
        </div>
        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="closeDeleteModal()">Batal</button>
            <button type="button" class="btn btn-error" id="confirmDelete">Hapus</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Toast Notification Container -->
<div class="toast toast-top toast-end" id="toastContainer"></div>
@endsection

@push('css')
<style>
  /* Custom DataTables styling for DaisyUI */
  .dataTables_wrapper {
      color: inherit;
  }

  .dataTables_length select,
  .dataTables_filter input {
      @apply input input-bordered input-sm;
  }

  .dataTables_info,
  .dataTables_paginate {
      @apply text-sm;
  }

  .dataTables_paginate .paginate_button {
      @apply btn btn-sm btn-ghost mx-1;
  }

  .dataTables_paginate .paginate_button.current {
      @apply btn-active;
  }

  .dataTables_paginate .paginate_button.disabled {
      @apply btn-disabled;
  }

  /* Toast styles */
  .toast-success {
      @apply alert alert-success;
  }

  .toast-error {
      @apply alert alert-error;
  }
</style>
@endpush

@push('scripts')
<script>
window.addEventListener('load', function () {
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded yet.');
        return;
    }

    $(document).ready(function () {
        let deleteId = null;

        // Initialize DataTable
        const table = new DataTable('#dataTable', {
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.meteorologi.curah-hujan.index') }}",
                type: "GET"
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
                {data: 'nama_pos', name: 'nama_pos'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'curah_hujan', name: 'curah_hujan'},
                {data: 'kategori', name: 'kategori'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
            ],
            language: {
                processing: "Memproses...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                },
                emptyTable: "Tidak ada data yang tersedia",
                zeroRecords: "Tidak ada data yang cocok"
            },
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[2, 'desc']]
        });

        // Delete button handler
        $(document).on('click', '.delete-btn', function () {
            deleteId = $(this).data('id');
            document.getElementById('deleteModal').showModal();
        });

        // Confirm delete handler
        $('#confirmDelete').click(function () {
            if (!deleteId) return;

            $.ajax({
                url: `/admin/meteorologi/curah-hujan/${deleteId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        table.ajax.reload();
                        closeDeleteModal();
                        showToast(response.message, 'success');
                    }
                },
                error: function () {
                    showToast('Terjadi kesalahan saat menghapus data', 'error');
                }
            });
        });

        // Helper functions
        function closeDeleteModal() {
            document.getElementById('deleteModal').close();
            deleteId = null;
        }

        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `alert ${type === 'success' ? 'alert-success' : 'alert-error'} mb-2`;
            toast.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                    }
                </svg>
                <span>${message}</span>
            `;

            toastContainer.appendChild(toast);

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 3000);
        }
    });
});
</script>
@endpush
