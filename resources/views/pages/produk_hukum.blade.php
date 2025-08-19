@extends('layouts.app-lain')

@section('title', 'Beranda')

@section('content')
    <div class="min-h-screen w- mb-20 pt-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-white shadow rounded-lg">
            <h1 class="text-3xl font-semibold mb-6 text-gray-800">
                Daftar Produk Hukum
            </h1>
            <div class="overflow-x-auto">
                <table id="produkHukumTable" class="min-w-full border border-gray-300 text-sm text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border">Tahun</th>
                            <th class="px-4 py-2 border">Judul</th>
                            <th class="px-4 py-2 border">Deskripsi</th>
                            <th class="px-4 py-2 border">File</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let table;
        let currentProdukHukumId = null;

        document.addEventListener('DOMContentLoaded', function() {
            initDataTable();
        });

        function initDataTable() {
            table = new DataTable('#produkHukumTable', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'api/produk-hukum/get-data',
                    type: 'GET',
                    data: function(d) {
                        // Additional parameters for search, sorting, etc.
                    },
                    error: function(xhr) {
                        console.error('Error loading data:', xhr);
                        showToast('Error loading data', 'error');
                    }
                },
                columns: [{
                        data: 'no',
                        name: 'no'
                    },
                    {
                        data: 'tahun',
                        name: 'tahun'
                    },
                    {
                        data: 'judul',
                        name: 'judul'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
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
                ],
                // order: [[5, 'desc']],
                pageLength: 10,
            });
        }
    </script>
@endpush
