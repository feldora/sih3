@extends('layouts.admin')

@section('title', 'Load Shapefile')

@section('content')
    <div class="container mx-auto p-4">
        <!-- Upload Section -->
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                {{-- <h2 class="card-title text-2xl">Load Shapefile </h2> --}}
                <div class="flex justify-between items-center mb-4">
                    @if (!empty($data['form_type']))
                        <h2 class="card-title text-2xl">{{ $data['form_type'] }}</h2>
                    @else
                        <h2 class="card-title text-2xl">Load Shapefile - {{ $data['type'] }}</h2>
                    @endif

                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                </div>
                @if (session('success'))
                    <div class="alert alert-success mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form id="shpForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Upload Shapefile</span>
                        </label>
                        <input type="file" class="file-input file-input-bordered w-full" id="shp_file" name="shp_file"
                            accept=".zip" required>
                        <label class="label">
                            <span class="label-text-alt">Upload file .shp atau .zip yang berisi shapefile lengkap (shp, shx,
                                dbf, prj)</span>
                        </label>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit" class="btn btn-primary btn-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Load Shapefile
                        </button>
                        <button type="button" id="openTabelData" class="btn btn-sm btn-primary">
                            <i class="fa fa-database" aria-hidden="true"></i>
                            Daftar Feature
                        </button>
                        <button type="button" class="btn btn-info btn-sm" id="saveVisibilityFeatures">
                            <i class="fa-solid fa-save"></i>
                            simpan
                        </button>

                        {{-- <button type="button" class="btn btn-success btn-sm" id="downloadGeoJson">
                            <i class="fa-solid fa-cloud-arrow-down"></i>
                            Download GeoJSON
                        </button> --}}
                    </div>

                </form>
            </div>
        </div>

        <!-- Map Container -->
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <h2 class="card-title mb-4">Map Visualization</h2>
                <div id="shpmap" style="height: 800px; width: 100%; border-radius: 0.5rem;"></div>
            </div>
        </div>
    </div>

    {{-- tabel data --}}
    <dialog id="tabelDataProperti" class="modal">
        <div class="modal-box w-[100%] max-w-[100%] h-[95%] max-h-screen p-6 mx-4 my-4">
            <h2 class="text-2xl font-semibold mb-4">Tabel Properti</h2>

            <div class="mb-4">
                <span id="countFeatures" class="block text-sm text-gray-600"></span>
            </div>

            <!-- Container tabel dengan scroll horizontal dan margin yang lebih baik -->
            <div class="overflow-x-auto overflow-y-auto border rounded-lg mx-2 px-4 py-2" style="max-height: calc(100% - 140px);">
                <table class="table table-zebra w-full" id="FeatureCollection">
                    <thead class="sticky top-0 bg-base-200">
                        <tr id="tabel-header">
                            <!-- Header kolom akan diisi lewat JavaScript -->
                        </tr>
                    </thead>
                    <tbody id="tabel-body">
                        <!-- Isi tabel akan diisi lewat JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end px-2">
                <button id="closeModal" class="btn btn-error">Tutup</button>
            </div>
        </div>
        <!-- Backdrop untuk menutup modal -->
        <form method="dialog" class="modal-backdrop">
            <button id="closeModal">close</button>
        </form>
    </dialog>

    {{-- Form Save modal --}}
    <dialog id="my_modal_1" class="modal">
        <div class="modal-box w-1/2 max-w-full relative overflow-visible">
            <h3 class="text-lg font-bold mb-2">Mapping Field</h3>

            <!-- Form utama -->
            <form id="fieldMappingForm" method="POST" action="{{ route('admin.loadshp.saveGeo') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4" id="mapping-fields">
                    <!-- Akan diisi lewat JavaScript -->
                </div>

                <div class="modal-action mt-6">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>

            <!-- Tombol Close berada di luar form -->
            <div class="modal-action">
                <button type="button" class="btn" onclick="document.getElementById('my_modal_1').close()">Tutup</button>
            </div>
        </div>
    </dialog>

    <!-- Loading Modal -->
    <input type="checkbox" id="loading-modal" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box text-center">
            <span class="loading loading-spinner loading-lg text-primary"></span>
            <p class="mt-4 text-lg">Processing shapefile...</p>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="
        display:none;
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background:rgba(255,255,255,0.8);
        z-index:9999;
        text-align:center;
        padding-top:20%;
        font-size:20px;
        color:#333; ">
        <div class="spinner" style="
            border:8px solid #f3f3f3;
            border-top:8px solid #3498db;
            border-radius:50%;
            width:60px;
            height:60px;
            animation:spin 1s linear infinite;
            margin:auto;
        "></div>
        <p>Menyimpan data, mohon tunggu...</p>
    </div>

    <style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .leaflet-container {
            border-radius: 0.5rem;
        }

        .popup-content h6 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }


        /* Modal dengan margin dan spacing yang lebih baik */
        #tabelDataProperti .modal-box {
            /* Pastikan modal memiliki margin dari viewport */
            margin: 2rem auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Pastikan tabel tidak overflow dari modal */
        #tabelDataProperti .table {
            white-space: nowrap;
            min-width: max-content;
        }

        /* Sticky header untuk tabel */
        #tabelDataProperti thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Padding internal tabel untuk breathing room */
        #tabelDataProperti .table td,
        #tabelDataProperti .table th {
            padding: 12px 16px;
        }

        /* Container tabel dengan padding internal */
        #tabelDataProperti .overflow-x-auto {
            /* Memberikan ruang bernapas di dalam container tabel */
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        /* Custom scrollbar untuk area tabel */
        #tabelDataProperti .overflow-x-auto::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        #tabelDataProperti .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 6px;
        }

        #tabelDataProperti .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 6px;
            border: 2px solid #f1f5f9;
        }

        #tabelDataProperti .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #tabelDataProperti .modal-box {
                width: 98% !important;
                height: 95% !important;
                margin: 1rem auto !important;
                padding: 1rem !important;
            }
            
            #tabelDataProperti .overflow-x-auto {
                margin: 0 !important;
                padding: 0.5rem !important;
            }
        }

        /* Hover effects untuk rows */
        #tabelDataProperti tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.1);
            transition: background-color 0.2s ease;
        }

        /* Button styling dalam tabel */
        #tabelDataProperti .btn-sm {
            min-height: 2rem;
            height: 2rem;
            padding: 0 0.75rem;
        }

        /* CSS tambahan untuk header tabel */
        #tabelDataProperti th {
            vertical-align: top;
            padding: 8px 12px;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            text-align: left;
            position: relative;
        }

        #tabelDataProperti th .mt-1 {
            margin-top: 0.5rem;
        }

        #tabelDataProperti th .select {
            font-weight: normal;
            font-size: 0.75rem;
            min-height: 1.75rem;
            height: 1.75rem;
        }

        /* Alternativ: Jika ingin filter dalam baris terpisah */
        #tabelDataProperti .filter-row {
            background-color: #f1f5f9;
        }

        #tabelDataProperti .filter-row th {
            padding: 4px 8px;
            border-bottom: 1px solid #e2e8f0;
        }

    </style>
@endpush

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
        <style>
            /* Optional: pastikan form tidak memotong */
            #fieldMappingForm {
                overflow: visible !important;
            }

            #mapping-fields>div {
                margin-bottom: 1rem;
            }
        </style>
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        let map;
        let currentGeoJsonData = null;
        let featureLayers = []; // Layer per fitur
        let visibleFeatures = []; // Status visibilitas
        let currentGeoJsonLayer; // Layer grup (untuk fitBounds)
        let dataTableInstance = null;
        let newFeatures = [];
        let errFeatures = [];
        const type = @json($data['type'] ?? null);

        document.addEventListener('DOMContentLoaded', function() {
            map = L.map('shpmap').setView([-0.8917, 119.8707], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
        });

        document.getElementById('shpForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            showLoadingModal();

            fetch('{{ route('admin.loadshp.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingModal();
                    if (data.success) {
                        currentGeoJsonData = normalizeGeoJson(data.geojson);
                        currentGeoJsonData.features = currentGeoJsonData.features
                            .map(f => sanitizeGeoJSONFeature(f))
                            .filter(f => f !== null);
                        displayGeoJson(currentGeoJsonData);
                        rendertabelFeatures(currentGeoJsonData);
                        document.getElementById("tabelDataProperti").showModal();
                        showToast('Shapefile berhasil diload dan dikonversi ke GeoJSON!', 'success');
                    } else {
                        if (data.list) {
                            console.log(data.list);
                        }
                        showToast(data.message || 'Terjadi kesalahan saat memproses file', 'error');
                    }
                })
                .catch(error => {
                    hideLoadingModal();
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat memproses file', 'error');
                });
        });

        document.getElementById('saveVisibilityFeatures').addEventListener('click', function(e) {
            if (!currentGeoJsonData) {
                showToast('Load File SHP terlebihdahulu', 'warning')
                return false
            }
            let x = getVisibleFeatures();
            if (x.length === 0) {
                showToast('Pilih Data yang akan di simpan terlebih dahulu dengan mengklik ikon mata pda tabel.',
                    'warning')
                return false;
            }
            getFormSave(type);
        })

        function showLoadingModal() {
            document.getElementById('loading-modal').checked = true;
        }

        function hideLoadingModal() {
            document.getElementById('loading-modal').checked = false;
        }

        
        function sanitizeGeoJSONFeature(originalFeature) {
            if (!originalFeature || originalFeature.type !== 'Feature' || !originalFeature.geometry) {
                return null;
            }

            // deep clone supaya tidak memodifikasi input asli
            const feature = JSON.parse(JSON.stringify(originalFeature));

            // 1) Normalisasi geometry.type: hapus trailing 'M' (mis. LineStringM -> LineString)
            if (typeof feature.geometry.type === 'string') {
                feature.geometry.type = feature.geometry.type.replace(/m$/i, '');
            }

            // 2) Validasi/bersihkan bbox: harus array angka
            if (feature.bbox) {
                if (!Array.isArray(feature.bbox) || feature.bbox.some(v => isNaN(Number(v)))) {
                    delete feature.bbox;
                } else {
                    feature.bbox = feature.bbox.map(Number);
                }
            }

            // helper untuk konversi angka yang aman
            function toNumberOrNull(v) {
                if (v === null || v === undefined) return null;
                if (typeof v === 'number') return Number.isFinite(v) ? v : null;
                const n = Number(String(v).trim());
                return Number.isFinite(n) ? n : null;
            }

            // 3) Sanitasi coordinates (rekursif)
            function sanitizeCoords(coords) {
                if (!Array.isArray(coords)) return null;

                // Jika ini posisi (array angka) => konversi tiap elemen ke number
                const isPosition = coords.length > 0 && coords.every(item => typeof item !== 'object');
                if (isPosition) {
                    const nums = coords.map(toNumberOrNull);
                    // minimal harus ada lon & lat valid
                    if (nums.length < 2 || nums[0] === null || nums[1] === null) return null;
                    // Ambil maksimal 3 elemen (lon, lat, z) dan buang elemen lainnya (mis. M)
                    return nums.slice(0, 3);
                }

                // Jika nested array (LineString, MultiLineString, Polygon, dsb.)
                const out = coords
                    .map(sanitizeCoords) // rekursif
                    .filter(c => c !== null);
                return out.length > 0 ? out : null;
            }

            const sanitizedCoords = sanitizeCoords(feature.geometry.coordinates);
            if (!sanitizedCoords) {
                return null;
            }
            feature.geometry.coordinates = sanitizedCoords;

            return feature;
        }

        
        function displayGeoJson(geoJsonData) {
            if (currentGeoJsonLayer) {
                map.removeLayer(currentGeoJsonLayer);
            }

            // clear lama
            if (currentGeoJsonLayer) {
                map.removeLayer(currentGeoJsonLayer);
                currentGeoJsonLayer = null;
            }
            // Hapus semua layer fitur lama dari map
            if (featureLayers.length > 0) {
                featureLayers.forEach(layer => {
                    if (map.hasLayer(layer)) {
                        map.removeLayer(layer);
                    }
                });
            }

            // Reset arrays
            featureLayers = [];
            visibleFeatures = [];
            newFeatures = [];
            errFeatures = [];

            // kalau mau proses semua features: gunakan geoJsonData.features
            // disini saya gunakan single firstFeature sesuai kode sebelumnya
            let firstFeature = [geoJsonData.features[0]];

            geoJsonData.features.forEach((feature, index) => {
                try {
                    const sanitized = sanitizeGeoJSONFeature(feature);
                    if (!sanitized) {
                        errFeatures[index] = {
                            message: `Skipping invalid/unsupported GeoJSON feature at index ${index}`,
                            data: feature
                        };
                        console.warn(`Skipping invalid/unsupported GeoJSON feature at index ${index}`, feature);
                        return;
                    }

                    let layer;
                    try {
                        layer = L.geoJSON(sanitized, {
                            style: {
                                color: '#3388ff',
                                weight: 2,
                                opacity: 1,
                                fillOpacity: 0.2
                            },
                            onEachFeature: function(feat, lyr) {
                                try {
                                    if (feat.properties) {
                                        let popupContent = '<div class="popup-content p-2">';
                                        popupContent +=
                                            '<h6 class="font-semibold text-base mb-2">Properties:</h6>';
                                        for (let key in feat.properties) {
                                            popupContent +=
                                                `<div class="mb-1"><strong>${key}:</strong> ${feat.properties[key]}</div>`;
                                        }
                                        popupContent += '</div>';
                                        lyr.bindPopup(popupContent);
                                    }
                                } catch (err) {
                                    errFeatures[index] = {
                                        message: `Error building popup for feature index ${index}: ${err}`,
                                        data: feature
                                    };
                                    console.error(`Error building popup for feature index ${index}:`,
                                        err);
                                }
                            }
                        });
                    } catch (geoErr) {
                        errFeatures[index] = {
                            message: `Leaflet rejected sanitized feature at index ${index}: ${geoErr} , ${sanitized}`,
                            data: feature
                        };
                        console.error(`Leaflet rejected sanitized feature at index ${index}:`, geoErr, sanitized);
                        return;
                    }

                    if (layer) {
                        featureLayers.push(layer);
                        visibleFeatures.push(false);
                    }
                } catch (err) {
                    errFeatures[index] = {
                        message: `Unexpected error processing feature index ${index}: ${err}`,
                        data: feature
                    };
                    console.error(`Unexpected error processing feature index ${index}:`, err);
                }
            });

            // pastikan tidak ada undefined saat buat featureGroup
            const validLayers = featureLayers.filter(l => l);
            if (validLayers.length > 0) {
                const tempGroup = L.featureGroup(validLayers);
                currentGeoJsonLayer = tempGroup;
                // cek getBounds aman
                try {
                    map.fitBounds(tempGroup.getBounds());
                } catch (err) {
                    console.warn('Tidak bisa fitBounds (bounds invalid):', err);
                }
            } else {
                console.warn('No valid features to display after sanitization.');
            }
        }

function rendertabelFeatures(geoJsonData) {
    if (dataTableInstance !== null) {
        dataTableInstance.destroy();
        dataTableInstance = null;
    }
    document.getElementById('tabel-header').innerHTML = '';
    document.getElementById('tabel-body').innerHTML = '';

    const headers = new Set();
    const tbody = document.getElementById('tabel-body');
    const thead = document.getElementById('tabel-header');

    tbody.innerHTML = '';
    thead.innerHTML = '';

    geoJsonData.features.forEach(feature => {
        const props = feature.properties || {};
        Object.keys(props).forEach(key => headers.add(key));
    });

    const finalHeaders = [...headers, 'type'];
    finalHeaders.forEach(key => {
        const th = document.createElement('th');
        th.textContent = key.charAt(0).toUpperCase() + key.slice(1);
        thead.appendChild(th);
    });

    const thBtn = document.createElement('th');
    thBtn.innerHTML = `
        <button type="button" class="btn btn-outline btn-sm" id="toggleAllFeatures" title="Tampilkan Semua Fitur">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
    `;
    thead.appendChild(thBtn);

    geoJsonData.features.forEach((feature, index) => {
        const tr = document.createElement('tr');
        const props = feature.properties || {};

        headers.forEach(key => {
            const td = document.createElement('td');
            td.textContent = props[key] || '-';
            tr.appendChild(td);
        });

        const tdType = document.createElement('td');
        tdType.textContent = feature.geometry?.type || 'Unknown';
        tr.appendChild(tdType);

        const tdAction = document.createElement('td');
        tdAction.innerHTML = `
        <button class="btn btn-sm btn-ghost text-gray-400 eye-feature-btn" 
                title="Lihat" 
                data-index="${index}" 
                id="eye-btn-${index}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 616 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
    `;
        tr.appendChild(tdAction);
        tbody.appendChild(tr);
    });

    // Update count features
    document.getElementById('countFeatures').textContent = `Total Features: ${geoJsonData.features.length}`;

    // Initialize DataTable
    dataTableInstance = new DataTable('#FeatureCollection', {
        destroy: true,
        scrollX: true,
        columnDefs: [{
            targets: -1, // Kolom terakhir (tombol)
            orderable: false // Nonaktifkan sorting
        }],
        initComplete: function () {
            // Setup event handlers setelah DataTable selesai
            setupIndividualEyeButtons();
            
            // Setup filter columns
            this.api().columns().every(function (index) {
                // Abaikan kolom terakhir (tombol action)
                if (index === this.count() - 1) {
                    return;
                }

                var column = this;
                var columnHeader = $(column.header());
                
                // Buat container untuk filter di bawah header
                var filterContainer = $('<div class="mt-1"></div>');
                var select = $('<select class="select select-xs w-full max-w-xs"><option value="">All</option></select>')
                    .appendTo(filterContainer)
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });

                // Tambahkan unique values ke select
                column.data().unique().sort().each(function (d, j) {
                    if (d && d.toString().trim() !== '') {
                        select.append('<option value="' + d + '">' + d + '</option>');
                    }
                });

                // Append filter container ke header cell
                columnHeader.append(filterContainer);
            });
        },
        // Event yang dipanggil setelah tabel di-redraw (penting untuk paging/filtering)
        drawCallback: function () {
            setupIndividualEyeButtons();
        }
    });

    // Setup toggle all button
    setupToggleAllButton();
}

// Fungsi untuk setup individual eye buttons dengan vanilla JavaScript
function setupIndividualEyeButtons() {
    // Gunakan selector yang benar sesuai dengan HTML
    const eyeButtons = document.querySelectorAll('.eye-feature-btn');
    
    eyeButtons.forEach((btn) => {
        // Ambil index dari data attribute
        const index = parseInt(btn.getAttribute('data-index'));
        
        // Remove existing event listener dengan cloning
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);

        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Toggle visibility
            if (visibleFeatures[index]) {
                // Sembunyikan feature
                map.removeLayer(featureLayers[index]);
                visibleFeatures[index] = false;
                newFeatures = newFeatures.filter(i => i !== index);
            } else {
                // Tampilkan feature
                featureLayers[index].addTo(map);
                visibleFeatures[index] = true;
                if (!newFeatures.includes(index)) {
                    newFeatures.push(index);
                }
            }

            // Update warna tombol berdasarkan visibility
            updateEyeButtonColor(newBtn, visibleFeatures[index]);
            
            // Update warna tombol toggle all setelah perubahan individual
            const toggleBtn = document.getElementById('toggleAllFeatures');
            const anyVisible = visibleFeatures.some(v => v === true);
            updateToggleAllButtonColor(toggleBtn, anyVisible);
            
            // Optional: Zoom to feature when shown
            if (visibleFeatures[index]) {
                try {
                    const bounds = featureLayers[index].getBounds();
                    if (bounds.isValid()) {
                        map.fitBounds(bounds, { padding: [20, 20] });
                    }
                } catch (e) {
                    console.log('Could not zoom to feature:', e);
                }
            }
        });

        // Set initial color based on current visibility
        updateEyeButtonColor(newBtn, visibleFeatures[index]);
    });
    
    // Update warna tombol toggle all setelah setup semua tombol individual
    const toggleBtn = document.getElementById('toggleAllFeatures');
    const anyVisible = visibleFeatures.some(v => v === true);
    updateToggleAllButtonColor(toggleBtn, anyVisible);
}

// Fungsi untuk setup toggle all button
function setupToggleAllButton() {
    const toggleBtn = document.getElementById('toggleAllFeatures');
    if (toggleBtn) {
        // Remove existing event listener
        const newToggleBtn = toggleBtn.cloneNode(true);
        toggleBtn.parentNode.replaceChild(newToggleBtn, toggleBtn);
        
        // Add new event listener
        newToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSemuaFitur();
        });

        // Set initial color
        const semuaTertampil = visibleFeatures.some(v => v === true);
        updateToggleAllButtonColor(newToggleBtn, semuaTertampil);
    }
}

// Fungsi helper untuk update warna tombol eye individual
function updateEyeButtonColor(button, isVisible) {
    if (button) {
        if (isVisible) {
            // Feature terlihat - warna biru/aktif
            button.classList.remove('text-gray-400');
            button.classList.add('text-blue-500');
        } else {
            // Feature tersembunyi - warna abu-abu/tidak aktif
            button.classList.remove('text-blue-500');
            button.classList.add('text-gray-400');
        }
    }
}

// Fungsi helper untuk update warna tombol toggle all
function updateToggleAllButtonColor(button, anyVisible) {
    if (button) {
        if (anyVisible) {
            // Ada feature yang terlihat - warna biru/aktif
            button.classList.remove('text-gray-400');
            button.classList.add('text-blue-500');
        } else {
            // Semua feature tersembunyi - warna abu-abu/tidak aktif
            button.classList.remove('text-blue-500');
            button.classList.add('text-gray-400');
        }
    }
}

// Fungsi toggleSemuaFitur yang diperbaiki
function toggleSemuaFitur() {
    // Cek apakah semua feature yang visible di tabel sedang ditampilkan
    const visibleRowIndexes = dataTableInstance.rows({
        filter: 'applied'
    }).indexes().toArray();

    // Cek apakah semua feature yang terfilter sedang ditampilkan
    const semuaTertampil = visibleRowIndexes.every(index => visibleFeatures[index] === true);
    const toggleBtn = document.getElementById('toggleAllFeatures');

    visibleRowIndexes.forEach(index => {
        const layer = featureLayers[index];
        if (!layer) return;

        if (semuaTertampil) {
            // Sembunyikan semua
            if (map.hasLayer(layer)) {
                map.removeLayer(layer);
            }
            visibleFeatures[index] = false;
            newFeatures = newFeatures.filter(i => i !== index);
        } else {
            // Tampilkan semua
            if (!map.hasLayer(layer)) {
                layer.addTo(map);
            }
            visibleFeatures[index] = true;
            if (!newFeatures.includes(index)) {
                newFeatures.push(index);
            }
        }
    });

    // Update warna semua tombol individual yang terlihat di tabel saat ini
    updateAllVisibleEyeButtons();

    // Update warna tombol toggle all
    const anyVisible = visibleFeatures.some(v => v === true);
    updateToggleAllButtonColor(toggleBtn, anyVisible);
}

// Fungsi untuk update semua tombol eye yang terlihat di tabel saat ini
function updateAllVisibleEyeButtons() {
    // Cari semua tombol eye yang ada di DOM saat ini
    const currentEyeButtons = document.querySelectorAll('.eye-feature-btn');
    
    currentEyeButtons.forEach(button => {
        const index = parseInt(button.getAttribute('data-index'));
        if (!isNaN(index)) {
            updateEyeButtonColor(button, visibleFeatures[index]);
        }
    });
}

// Perbaiki fungsi toggleEyeIcon yang sudah ada (untuk kompatibilitas)
function toggleEyeIcon(index, isVisible) {
    const button = document.getElementById(`eye-btn-${index}`);
    updateEyeButtonColor(button, isVisible);
}

// Fungsi lihatFeature yang diperbaiki (menghapus duplikasi logika)
function lihatFeature(index) {
    console.log('lihatFeature called with index:', index);
    
    if (errFeatures[index]) {
        console.log('Error feature data:', errFeatures[index].data);
        showToast(errFeatures[index].message, 'error');
        return false;
    }
    
    const layer = featureLayers[index];
    if (!layer) {
        console.log('No layer found for index:', index);
        showToast('Layer tidak ditemukan untuk feature ini', 'warning');
        return;
    }

    const isVisible = visibleFeatures[index];
    console.log('Current visibility for index', index, ':', isVisible);

    if (isVisible) {
        // Sembunyikan feature
        map.removeLayer(layer);
        visibleFeatures[index] = false;
        newFeatures = newFeatures.filter(i => i !== index);
        console.log('Feature hidden:', index);
    } else {
        // Tampilkan feature
        layer.addTo(map);
        visibleFeatures[index] = true;
        if (!newFeatures.includes(index)) {
            newFeatures.push(index);
        }
        console.log('Feature shown:', index);
    }

    // Update warna tombol
    const eyeBtn = document.getElementById(`eye-btn-${index}`);
    updateEyeButtonColor(eyeBtn, visibleFeatures[index]);
    
    console.log('Updated newFeatures:', newFeatures);
    
    // Optional: Zoom to feature when shown
    if (visibleFeatures[index]) {
        try {
            const bounds = layer.getBounds();
            if (bounds.isValid()) {
                map.fitBounds(bounds, { padding: [20, 20] });
            }
        } catch (e) {
            console.log('Could not zoom to feature:', e);
        }
    }
}

        function lihatFeature(index) {
            if (errFeatures[index]) {
                console.log(errFeatures[index].data);

                showToast(errFeatures[index].message, 'error');
                return false;
            }
            const layer = featureLayers[index];
            if (!layer) return;

            const isVisible = visibleFeatures[index];

            if (isVisible) {
                map.removeLayer(layer);
                visibleFeatures[index] = false;

                // Hapus index dari newFeatures
                newFeatures = newFeatures.filter(i => i !== index);
            } else {
                layer.addTo(map);
                visibleFeatures[index] = true;

                // Tambahkan index ke newFeatures jika belum ada
                if (!newFeatures.includes(index)) {
                    newFeatures.push(index);
                }
            }

            toggleEyeIcon(index, visibleFeatures[index]);
            console.log(newFeatures);

        }

        function toggleEyeIcon(index, isVisible) {
            const button = document.getElementById(`eye-btn-${index}`);
            if (button) {
                if (isVisible) {
                    button.classList.remove('text-gray-400');
                    button.classList.add('text-blue-500');
                } else {
                    button.classList.remove('text-blue-500');
                    button.classList.add('text-gray-400');
                }
            }
        }

        function getVisibleFeatures() {
            if (!currentGeoJsonData || !currentGeoJsonData.features) return [];

            return newFeatures.map(index => currentGeoJsonData.features[index]);
        }
        
        function sanitizeGeoJSONFeature(feature) {
            if (!feature || typeof feature !== 'object') {
                return null; // Feature tidak valid
            }

            // Pastikan type = Feature
            if (feature.type !== 'Feature') {
                return null;
            }

            // Bersihkan geometry
            if (!feature.geometry || typeof feature.geometry !== 'object') {
                return null;
            }

            // 1️⃣ Hapus "M" di akhir tipe geometry (LineStringM → LineString)
            if (typeof feature.geometry.type === 'string') {
                feature.geometry.type = feature.geometry.type.replace(/M$/i, '');
            }

            // 2️⃣ Hapus bbox yang tidak valid di geometry
            if (feature.geometry.bbox && (!Array.isArray(feature.geometry.bbox) || feature.geometry.bbox.some(v => isNaN(
                    Number(v))))) {
                delete feature.geometry.bbox;
            }

            // 3️⃣ Hapus bbox invalid di root feature
            if (feature.bbox && (!Array.isArray(feature.bbox) || feature.bbox.some(v => isNaN(Number(v))))) {
                delete feature.bbox;
            }

            // 4️⃣ Paksa koordinat jadi 2D
            const stripZ = coords => {
                if (Array.isArray(coords[0])) {
                    return coords.map(stripZ);
                }
                return coords.slice(0, 2); // Ambil hanya lon & lat
            };

            if (feature.geometry.coordinates) {
                feature.geometry.coordinates = stripZ(feature.geometry.coordinates);
            }

            return feature;
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} fixed top-4 right-4 w-auto max-w-sm z-50 shadow-lg`;
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'transform 0.3s ease-in-out';

            let icon = '';
            if (type === 'success') {
                icon =
                    '<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
            } else if (type === 'error') {
                icon =
                    '<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
            }

            toast.innerHTML = `
            ${icon}
            <span>${message}</span>
            <button class="btn btn-sm btn-ghost" onclick="this.parentElement.remove()">×</button>
            `;

            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 10);
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toast.parentElement) toast.remove();
                }, 300);
            }, 5000);
        }

        function normalizeGeoJson(geojson) {
            if (geojson.type === 'FeatureCollection') {
                geojson.features.forEach(feature => {
                    if (feature.geometry.type === 'PointM') {
                        feature.geometry.type = 'Point';
                        // Buang koordinat 3 dan 4 (z, m), hanya ambil x, y
                        feature.geometry.coordinates = feature.geometry.coordinates.slice(0, 2);
                    }
                    // Kalau ada tipe lain yang perlu normalisasi bisa ditambah di sini
                });
            }
            return geojson;
        }

        function getFormSave(type) {
            if (!type) {
                showToast("Tidak dapat menyimpan maps", "warning");
                return;
            }

            fetch(`{{ route('admin.formFields') }}?type=${encodeURIComponent(type)}`)
                .then(response => response.json())
                .then(data => {
                    const mappingContainer = document.getElementById('mapping-fields');
                    mappingContainer.innerHTML = '';

                    if (!data.success) {
                        showToast('Gagal memuat field.', 'error');
                        return;
                    }

                    const fillable = data.fillable || {};
                    const geoProps = currentGeoJsonData?.features?.[0]?.properties || {};

                    Object.entries(fillable).forEach(([field, mode]) => {
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('relative'); // z-index diatur dinamis
                        wrapper.style.zIndex = 1; // z-index awal

                        if (mode === 'input') {
                            // Input teks biasa
                            wrapper.innerHTML = `
                                <label class="label">${field}</label>
                                <input type="text" name="${field}" class="input input-bordered w-full" />
                            `;
                        } else if (mode === 'mapping') {
                            // Select multiple (mapping)
                            const selectId = `select-${field}`;
                            wrapper.innerHTML = `
                                <label class="label">${field}</label>
                                <select id="${selectId}" name="mapped[${field}][]" class="w-full" multiple></select>
                            `;
                        } else if (mode === 'instansi') {

                            const selectId = `select-${field}`;
                            wrapper.innerHTML = `
                                <label class="label">${field}</label>
                                <x-instansi-select name="instansi_id" :selected="old('instansi_id', $data->instansi_id ?? null)" class="w-full" />
                            `;
                        }


                        mappingContainer.appendChild(wrapper);

                        if (mode === 'mapping') {
                            const selectEl = wrapper.querySelector('select');
                            Object.keys(geoProps).forEach(prop => {
                                const option = document.createElement('option');
                                option.value = `properties.${prop}`;
                                option.text = `properties.${prop}`;
                                selectEl.appendChild(option);
                            });

                            const choicesInstance = new Choices(selectEl, {
                                removeItemButton: true,
                                placeholder: true,
                                placeholderValue: 'Pilih field dari properties...',
                                searchEnabled: true,
                                shouldSort: false,
                            });

                            selectEl.addEventListener('showDropdown', function() {
                                wrapper.style.zIndex = 9999;
                            }, false);

                            selectEl.addEventListener('hideDropdown', function() {
                                wrapper.style.zIndex = 1;
                            }, false);
                        }
                    });

                    document.getElementById('my_modal_1').showModal();
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showToast('Terjadi kesalahan saat memuat form field', 'error');
                });
        }

        document.getElementById('fieldMappingForm').addEventListener('submit', function(e) {
            e.preventDefault(); // cegah reload halaman

            const form = this;
            const formData = new FormData(form);
            const visibleFeatures = getVisibleFeatures();

            formData.append('visibleFeatures', JSON.stringify(visibleFeatures));
            formData.append('pos_type', type);
            document.getElementById('loadingOverlay').style.display = 'block';

            fetch(form.action, {
                    method: form.method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        // Kalau form pakai multipart/form-data, jangan set Content-Type manual
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    document.getElementById('my_modal_1').close();
                    showToast(data.message, 'success')
                    // Lakukan sesuatu dengan response, misalnya tampilkan pesan sukses
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('my_modal_1').close();
                    showToast(error, 'warning')
                    // Tampilkan pesan error
                })
                .finally(() => {
                    // Sembunyikan loading overlay
                    document.getElementById('loadingOverlay').style.display = 'none';
                });
        });

        document.getElementById("openTabelData").addEventListener("click", function() {
            document.getElementById("tabelDataProperti").showModal();
        });

        // Menutup Modal
        document.getElementById("closeModal").addEventListener("click", function() {
            document.getElementById("tabelDataProperti").close();
        });

    </script>
@endpush
