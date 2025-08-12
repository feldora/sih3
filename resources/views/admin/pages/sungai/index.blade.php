@extends('layouts.admin')

@section('title', 'Data Sungai')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl my-6 p-6 rounded-xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Sungai</h1>
                {{-- <p class="text-gray-600 mt-1">Kelola data informasi sungai dan dokumen terkait</p> --}}
            </div>
            <a href="{{ route('admin.loadshp.index') }}?type=Sungai" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Import shp Sungai
            </a>
        </div>

        <!-- DataTable -->
        <div class="overflow-x-auto">
            <table id="sungaiTable" class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Sungai</th>
                        <th>Panjang</th>
                        <th>Luas DAS</th>
                        <th>Ordo</th>
                        <th>Dokumen</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan dimuat via DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
            <p class="py-4">Apakah Anda yakin ingin menghapus data sungai ini? Semua dokumen terkait juga akan dihapus.</p>
            <div class="modal-action">
                <button id="cancelDelete" class="btn btn-ghost">Batal</button>
                <button id="confirmDelete" class="btn btn-error">Hapus</button>
            </div>
        </div>
    </div>
    <!-- Modal List Media -->
<dialog id="modalListMedia" class="modal">
    <div class="modal-box">
    </div>
</dialog>

    
</div>
@endsection

@push('styles')

<style>
    .dataTables_wrapper .dataTables_length select {
        @apply select select-bordered select-sm;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        @apply input input-bordered input-sm;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        @apply btn btn-sm btn-ghost;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        @apply btn-active;
    }
    
    .table th {
        background-color: #f8fafc;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
window.addEventListener('load', function() {
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded yet.');
        return;
    }

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

    // Initialize DataTable
    let table = $('#sungaiTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('admin.sungai.index') }}",
            type: 'GET'
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center',
                width: '5%'
            },
            {
                data: 'nama_sungai',
                name: 'nama_sungai',
                render: function(data, type, row) {
                    return `<div class="font-medium text-gray-900">${data}</div>`;
                }
            },
            {
                data: 'panjang_sungai',
                name: 'panjang_sungai',
                className: 'text-center',
                render: function(data, type, row) {
                    return data ? `<span class="badge badge-info">${data}</span>` : '<span class="text-gray-400">-</span>';
                }
            },
            {
                data: 'luas_das',
                name: 'luas_das',
                className: 'text-center',
                render: function(data, type, row) {
                    return data ? `<span class="badge badge-success">${data}</span>` : '<span class="text-gray-400">-</span>';
                }
            },
            {
                data: 'ordo',
                name: 'ordo',
                className: 'text-center',
                render: function(data, type, row) {
                    if (!data) {
                        return '<span class="text-gray-400">-</span>';
                    }
                    const colorMap = {
                        '9': 'bg-blue-100 text-blue-800',
                        '8': 'bg-blue-200 text-blue-900',
                        '7': 'bg-blue-300 text-blue-900',
                        '6': 'bg-blue-400 text-white',
                        '5': 'bg-blue-500 text-white',
                        '4': 'bg-blue-600 text-white',
                        '3': 'bg-blue-700 text-white',
                        '2': 'bg-blue-800 text-white',
                        '1': 'bg-blue-900 text-white' 
                    };
                    const badgeClass = colorMap[data] || 'badge-outline';
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            {
                data: 'media_count',
                name: 'media_count',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    const count = data.split(' ')[0];
                    if (count > 0) {
                        const baseUrl = "{{ route('admin.sungai.media.list', ':id') }}"
                            .replace(':id', row.id);
                        return `<span class="badge badge-primary cursor-pointer listMedia" data-getListMediaUrl="${baseUrl}">${data}</span>`;
                    }


                    return `<span class="text-gray-400 ">0 file(s)</span>`;
                }
            },            
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center',
                width: '15%'
            }
        ],
        language: {
            processing: '<div class="loading loading-spinner loading-md"></div>',
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            },
            emptyTable: "Tidak ada data sungai tersedia",
            zeroRecords: "Tidak ditemukan data yang sesuai"
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[6, 'desc']] // Order by created_at desc
    });

    // Delete functionality
    let deleteId = null;
    const deleteModal = document.getElementById('deleteModal');

    // Handle delete button click
    $(document).on('click', '.delete-btn', function() {
        deleteId = $(this).data('id');
        deleteModal.classList.add('modal-open');
    });

    // Handle cancel delete
    document.getElementById('cancelDelete').addEventListener('click', function() {
        deleteModal.classList.remove('modal-open');
        deleteId = null;
    });

    // Handle confirm delete
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            // Show loading
            this.innerHTML = '<span class="loading loading-spinner loading-xs"></span> Menghapus...';
            this.disabled = true;

            $.ajax({
                url: `{{ route('admin.sungai.destroy', ':id') }}`.replace(':id', deleteId),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    deleteModal.classList.remove('modal-open');
                    showToast(response.message || 'Data berhasil dihapus', 'success');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    let message = 'Terjadi kesalahan saat menghapus data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(message, 'error');
                },
                complete: function() {
                    document.getElementById('confirmDelete').innerHTML = 'Hapus';
                    document.getElementById('confirmDelete').disabled = false;
                    deleteId = null;
                }
            });
        }
    });

    // Close modal when clicking outside
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            deleteModal.classList.remove('modal-open');
            deleteId = null;
        }
    });

    // Refresh table function (if needed)
    window.refreshSungaiTable = function() {
        table.ajax.reload();
    };

    document.addEventListener('click', function (event) {
        const el = event.target.closest('.listMedia');
        const modal = document.getElementById('modalListMedia');
        modal.addEventListener('click', (event) => {
            const modalBox = modal.querySelector('.modal-box');
            if (!modalBox.contains(event.target)) {
                modal.close();
            }
        });

        if (!el) return;

        const mediaUrl = el.getAttribute('data-getListMediaUrl');
        if (!mediaUrl) return;

        fetch(mediaUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(res => {
                if (!res.success || !Array.isArray(res.data)) {
                    throw new Error('Data dokumen tidak valid');
                }

                const modalBox = document.querySelector('#modalListMedia .modal-box');
                let html = `<h3 class="font-bold text-lg mb-3">Daftar Dokumen</h3>`;

                if (res.data.length === 0) {
                    html += `<p class="text-gray-500">Tidak ada dokumen tersedia.</p>`;
                } else {
                    html += `<ul class="space-y-2">`;
                    res.data.forEach(media => {
                        const isPdf = media.extension.toLowerCase() === 'pdf';
                        html += `
                            <li class="flex justify-between items-center border-b border-base-300 pb-2">
                                <span>${media.file_name} <small class="text-gray-400">(${media.size_kb} KB)</small></span>
                                <a href="${media.url}" 
                                target="_blank" 
                                class="btn btn-xs ${isPdf ? 'btn-primary' : 'btn-secondary'}">
                                    ${isPdf ? 'Lihat' : 'Download'}
                                </a>
                            </li>
                        `;
                    });
                    html += `</ul>`;
                }

                modalBox.innerHTML = html;

                // Tampilkan modal (DaisyUI modal)
                document.getElementById('modalListMedia').showModal();
                // document.getElementById('modalListMedia').classList.add('modal-open');
            })
            .catch(error => {
                console.error('Error fetching media list:', error);
            });
    });


});
</script>
@endpush