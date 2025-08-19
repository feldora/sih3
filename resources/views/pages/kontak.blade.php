@extends('layouts.app-lain')

@section('title', 'Beranda')

@section('content')
<div class="min-h-screen mb-20 pt-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-white shadow rounded-lg">
        <h1 class="text-3xl font-semibold mb-6 text-gray-800">
            Hubungi kami
        </h1>

        <div class="overflow-x-auto">
            <table id="KontakTable" class="min-w-full border border-gray-300 text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border w-12"></th> <!-- tombol expand -->
                        <th class="px-4 py-2 border">Nama Instansi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let table;

    document.addEventListener('DOMContentLoaded', function () {
        initDataTable();
    });

    function formatKontak(kontak) {
        if (!kontak || kontak.length === 0) {
            return '<div class="px-4 py-2 text-gray-500">Tidak ada kontak tersedia.</div>';
        }

        let rows = kontak.map(k => `
            <tr>
                <td class="px-2 py-1 border">${k.nama ?? ''}</td>
                <td class="px-2 py-1 border">${k.alamat ?? ''}</td>
                <td class="px-2 py-1 border">${k.email ?? ''}</td>
                <td class="px-2 py-1 border">${k.telp ?? ''}</td>
                <td class="px-2 py-1 border">${k.website ?? ''}</td>
                <td class="px-2 py-1 border">${k.keterangan ?? ''}</td>
            </tr>
        `).join('');

        return `
            <table class="min-w-full border border-gray-200 text-sm text-left mt-2">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-1 border">Nama</th>
                        <th class="px-2 py-1 border">Alamat</th>
                        <th class="px-2 py-1 border">Email</th>
                        <th class="px-2 py-1 border">Telp</th>
                        <th class="px-2 py-1 border">Website</th>
                        <th class="px-2 py-1 border">Keterangan</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        `;
    }

    function initDataTable() {
        table = new DataTable('#KontakTable', {
            processing: true,
            serverSide: true,
            ajax: {
                url: 'api/kontak/data',
                type: 'GET',
                dataSrc: 'data' // <<-- ambil dari properti "data" di response
            },
            columns: [
                {
                    className: 'dt-control px-4 py-2 border text-center',
                    orderable: false,
                    data: null,
                    defaultContent: '<button class="text-blue-500">+</button>'
                },
                { data: 'nama', className: 'px-4 py-2 border' }
            ],
            order: [[1, 'asc']]
        });

        // Event expand/collapse
        document.querySelector('#KontakTable tbody').addEventListener('click', function (e) {
            if (e.target.closest('td.dt-control')) {
                const tr = e.target.closest('tr');
                const row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.classList.remove('shown');
                    e.target.innerText = '+';
                } else {
                    row.child(formatKontak(row.data().kontak)).show();
                    tr.classList.add('shown');
                    e.target.innerText = '-';
                }
            }
        });
    }
</script>
@endpush
