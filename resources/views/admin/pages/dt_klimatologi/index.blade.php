@extends('layouts.admin')

@section('title', 'Data Klimatologi')

@section('content')
<div class="container mx-auto px-4">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-header px-6 py-4 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="card-title text-xl font-bold">Data Klimatologi</h3>
                </div>

                <div class="flex justify-end items-center mb-6 gap-5">
                    <a href="{{ route('admin.hidrologi.klimatologi.import-excel.form') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-file-import mr-1"></i>
                        Import Excel
                    </a>
                    <a href="{{ route('admin.hidrologi.klimatologi.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Tambah Data
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="overflow-x-auto">
                <table id="dataTable" class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pos Pantau</th>
                            <th>Tanggal</th>
                            <th>Kecepatan Angin</th>
                            <th>Arah Angin</th>
                            <th>Kelembapan</th>
                            <th>Suhu</th>
                            <th>Curah Hujan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will fill this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<dialog id="detailModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg mb-4">Detail Data Klimatologi</h3>
        <div id="detailContent"></div>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Tutup</button>
            </form>
        </div>
    </div>
</dialog>

<!-- Delete Modal -->
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error">Konfirmasi Hapus</h3>
        <p class="py-4">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Batal</button>
            </form>
            <button id="confirmDelete" class="btn btn-error">Hapus</button>
        </div>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
    window.addEventListener('load', function() {
        const table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.hidrologi.klimatologi.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'pos_pantau_nama', name: 'posPantau.nama_pos' },
                { data: 'tanggal_formatted', name: 'created_at' },
                { data: 'kecepatan_angin', name: 'kecepatan_angin', className: 'text-center' },
                { data: 'arah_angin', name: 'arah_angin' },
                { data: 'kelembapan', name: 'kelembapan', className: 'text-center' },
                { data: 'suhu', name: 'suhu', className: 'text-center' },
                { data: 'curah_hujan', name: 'curah_hujan', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                processing: "Memproses...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
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

        window.showDetail = function(id) {
            $.ajax({
                url: "{{ route('admin.hidrologi.klimatologi.show', ':id') }}".replace(':id', id),
                type: 'GET',
                success: function(response) {
                    const d = response.data;
                    let content = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label font-semibold">Pos Pantau</label>
                                <p class="text-base-content">${response.pos_pantau_nama}</p>
                            </div>
                            <div class="form-control">
                                <label class="label font-semibold">Tanggal Input</label>
                                <p class="text-base-content">${response.tanggal_formatted}</p>
                            </div>
                            <div class="form-control">
                                <label class="label font-semibold">Kecepatan Angin</label>
                                <p class="text-base-content">${d.kecepatan_angin ?? '-'} m/s</p>
                            </div>
                            <div class="form-control">
                                <label class="label font-semibold">Arah Angin</label>
                                <p class="text-base-content">${d.arah_angin ?? '-'}</p>
                            </div>
                            <div class="form-control">
                                <label class="label font-semibold">Kelembapan</label>
                                <p class="text-base-content">${d.kelembapan ?? '-'}%</p>
                            </div>
                            <div class="form-control">
                                <label class="label font-semibold">Suhu</label>
                                <p class="text-base-content">${d.suhu ?? '-'}°C</p>
                            </div>
                            <div class="form-control">
                                <label class="label font-semibold">Curah Hujan</label>
                                <p class="text-base-content">${d.curah_hujan ?? '-'} mm</p>
                            </div>
                        </div>
                    `;
                    $('#detailContent').html(content);
                    document.getElementById('detailModal').showModal();
                },
                error: function() {
                    alert('Gagal memuat data detail.');
                }
            });
        }

        let deleteId = null;

        window.deleteData = function(id) {
            deleteId = id;
            document.getElementById('deleteModal').showModal();
        }

        $('#confirmDelete').click(function() {
            if (deleteId) {
                $.ajax({
                    url: "{{ route('admin.hidrologi.klimatologi.destroy', ':id') }}".replace(':id', deleteId),
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.success) {
                            document.getElementById('deleteModal').close();
                            showToast(response.message, 'success');
                            table.ajax.reload();
                        } else {
                            alert(response.message);
                        }
                        deleteId = null;
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat menghapus.');
                        deleteId = null;
                    }
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
            setTimeout(() => toast.remove(), 3000);
        }

        @if (session('success'))
            showToast("{{ session('success') }}", "success");
        @endif

        @if (session('error'))
            showToast("{{ session('error') }}", "error");
        @endif
    });
</script>
@endpush
