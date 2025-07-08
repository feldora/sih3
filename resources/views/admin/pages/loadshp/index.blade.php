@extends('layouts.admin')

@section('title', 'Load Shapefile - Snow Clone')

@section('content')
    <div class="container mx-auto p-4">
        <!-- Upload Section -->
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                {{-- <h2 class="card-title text-2xl">Load Shapefile </h2> --}}
                @if (!empty($data['form_type']))
                    <h2 class="card-title text-2xl mb-4">{{ $data['form_type'] }}</h2>
                @else
                    <h2 class="card-title text-2xl mb-4">Load Shapefile </h2>
                @endif


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

                        <button type="button" class="btn btn-info btn-sm" id="saveVisibilityFeatures">
                            <i class="fa-solid fa-save"></i>
                            simpan
                        </button>

                        <button type="button" class="btn btn-success btn-sm" id="downloadGeoJson">
                            <i class="fa-solid fa-cloud-arrow-down"></i>
                            Download GeoJSON
                        </button>
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
            <div class="tabeljson m-5 overflow-x-auto w-full">
                <span id="countFeatures" class="block mb-2"></span>
                <table class="table w-full whitespace-nowrap" id="FeatureCollection">
                    <thead>
                        <tr id="tabel-header">
                            <!-- Header kolom akan diisi lewat JavaScript -->
                        </tr>
                    </thead>
                    <tbody id="tabel-body">
                        <!-- Isi tabel akan diisi lewat JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
            #mapping-fields > div {
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
        let currentGeoJsonData;
        let featureLayers = []; // Layer per fitur
        let visibleFeatures = []; // Status visibilitas
        let currentGeoJsonLayer; // Layer grup (untuk fitBounds)
        let dataTableInstance = null;
        let newFeatures = [];
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
                        displayGeoJson(currentGeoJsonData);
                        rendertabelFeatures(currentGeoJsonData);
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
            if(!currentGeoJsonData){
                showToast('Load File SHP terlebihdahulu','warning')
                return false
            }
            let x = getVisibleFeatures();
            if (x.length === 0) {
                showToast('Pilih Data yang akan di simpan terlebih dahulu dengan mengklik ikon mata pda tabel.', 'warning')
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

        function displayGeoJson(geoJsonData) {
            if (currentGeoJsonLayer) {
                map.removeLayer(currentGeoJsonLayer);
            }

            featureLayers.forEach(layer => map.removeLayer(layer));
            featureLayers = [];
            visibleFeatures = [];

            geoJsonData.features.forEach((feature, index) => {
                const layer = L.geoJSON(feature, {
                    style: {
                        color: '#3388ff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.2
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties) {
                            let popupContent = '<div class="popup-content p-2">';
                            popupContent += '<h6 class="font-semibold text-base mb-2">Properties:</h6>';
                            for (let key in feature.properties) {
                                popupContent +=
                                    `<div class="mb-1"><strong>${key}:</strong> ${feature.properties[key]}</div>`;
                            }
                            popupContent += '</div>';
                            layer.bindPopup(popupContent);
                        }
                    }
                });

                featureLayers[index] = layer; // Simpan tanpa ditambahkan ke map
                visibleFeatures[index] = false; // Default: hidden
            });

            const tempGroup = L.featureGroup(featureLayers);
            currentGeoJsonLayer = tempGroup;
            map.fitBounds(tempGroup.getBounds());
        }

        function rendertabelFeatures(geoJsonData) {
            if (dataTableInstance !== null) {
                dataTableInstance.destroy();
                dataTableInstance = null;
            }

            const headers = new Set();
            const tbody = document.getElementById('tabel-body');
            const thead = document.getElementById('tabel-header');

            tbody.innerHTML = '';
            thead.innerHTML = '';

            geoJsonData.features.forEach(feature => {
                const props = feature.properties || {};
                Object.keys(props).forEach(key => headers.add(key));
            });

            const finalHeaders = [...headers, 'type', 'action'];

            finalHeaders.forEach(key => {
                const th = document.createElement('th');
                th.textContent = key.charAt(0).toUpperCase() + key.slice(1);
                thead.appendChild(th);
            });

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
                <button class="btn btn-sm btn-ghost text-gray-400" title="Lihat" onclick="lihatFeature(${index})" id="eye-btn-${index}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            `;
                tr.appendChild(tdAction);
                tbody.appendChild(tr);
            });

            dataTableInstance = new DataTable('#FeatureCollection', {
                scrollX: true
            });
            dataTableInstance.columns().every(function(index) {
                // Abaikan kolom terakhir (misalnya kolom "action")
                if (index === dataTableInstance.columns().count() - 1) {
                    return;
                }

                var column = this;
                var columnHeader = $(column.header());
                var select = $('<select class="select select-xs choice"><option value="">Filter</option></select>')
                    .appendTo(columnHeader)
                    .on('change', function() {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });

                column.data().unique().sort().each(function(d) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });

        }

        function lihatFeature(index) {
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

        document.getElementById('downloadGeoJson').addEventListener('click', function() {
            if (currentGeoJsonData) {
                const dataStr = JSON.stringify(currentGeoJsonData, null, 2);
                const dataBlob = new Blob([dataStr], {
                    type: 'application/json'
                });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(dataBlob);
                link.download = 'shapefile_converted.geojson';
                link.click();
            } else {
                showToast("Data GeoJSON tidak ditemukan.", "info");
            }
        });

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
                        } else {
                            // Select multiple (mapping)
                            const selectId = `select-${field}`;
                            wrapper.innerHTML = `
                                <label class="label">${field}</label>
                                <select id="${selectId}" name="mapped[${field}][]" class="w-full" multiple></select>
                            `;
                        }

                        mappingContainer.appendChild(wrapper);

                        if (mode !== 'input') {
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

            fetch(form.action, {
                method: form.method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            });
        });

    </script>
@endpush
