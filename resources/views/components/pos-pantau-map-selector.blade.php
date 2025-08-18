{{-- 
  <!-- Basic usage -->
  <x-pos-pantau-map-selector name="pos_pantau_id" />

  <!-- With label and required -->
  <x-pos-pantau-map-selector 
      name="pos_pantau_id"
      label="Pilih Pos Pantau"
      :required="true"
      placeholder="Klik untuk memilih pos pantau"
  />

  <!-- Custom map height dan center -->
  <x-pos-pantau-map-selector 
      name="pos_pantau_id"
      label="Pos Pantau"
      map-height="500px"
      :center-coordinates="[-1.5, 121.0]"
      :default-zoom="12"
  />
--}}

@php
  $actionAtribut = $viewOnly ? 'Disabled="true" readonly="true"' : "" ;
@endphp

<div class="form-control w-full">
    @if ($label)
        <label class="label">
            <span class="label-text">
                {{ $label }}
                @if ($required)
                    <span class="text-error">*</span>
                @endif
            </span>
        </label>
    @endif

    <!-- Hidden Input untuk menyimpan ID pos pantau yang dipilih -->
    <input {{ $actionAtribut }} type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}"
        @if ($required) required @endif />

    <!-- Display Input untuk menampilkan nama pos yang dipilih -->
    <input {{ $actionAtribut }} type="text" id="{{ $name }}_display" placeholder="{{ $placeholder }}"
        class="input input-bordered w-full" readonly onclick="openPosPantauModal()" />
</div>

<!-- Modal untuk Map Selector -->
<dialog id="posPantauModal" class="modal">
    <div class="modal-box w-11/12 max-w-5xl">
        <h3 class="font-bold text-lg mb-4">Pilih Pos Pantau</h3>

        <!-- Search Box -->
        <div class="form-control mb-4">
            <div class="input-group">
                <input type="text" id="searchPosPantau" placeholder="Cari pos pantau..."
                    class="input input-bordered flex-1" />
                <button type="button" class="btn btn-square" onclick="handleSearch()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Map Container -->
        <div id="posPantauMap" style="height: {{ $mapHeight }}; width: 100%;"
            class="rounded-lg border border-base-300"></div>

        <!-- Selected Info -->
        <div id="selectedPosPantauInfo" class="mt-4 p-4 bg-base-200 rounded-lg hidden">
            <h4 class="font-semibold text-base-content">Pos Pantau Terpilih:</h4>
            <p id="selectedPosPantauName" class="text-sm"></p>
            <p id="selectedPosPantauType" class="text-xs text-base-content/70"></p>
            <p id="selectedPosPantauLocation" class="text-xs text-base-content/70"></p>
        </div>

        <div class="modal-action">
            <button type="button" class="btn btn-ghost" onclick="closePosPantauModal()">Batal</button>
            <button type="button" class="btn btn-primary" id="confirmPosPantauSelection" onclick="confirmSelection()"
                disabled>Pilih</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button type="button" onclick="closePosPantauModal()">close</button>
    </form>
</dialog>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .custom-marker-icon {
            background-color: #3b82f6;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .selected-marker-icon {
            background-color: #ef4444;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.4);
        }

        .leaflet-popup-content-wrapper {
            border-radius: 8px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let posPantauMap;
        let posPantauData = [];
        let posPantauMarkers = [];
        let selectedPosPantau = null;
        let pos_pantau_id = @json($value);
        let viewOnly = @json($viewOnly);
        loadPosPantauData()

        function openPosPantauModal() {
          if (viewOnly) {
            showToast('Komponen read only...', 'warning');
            return false;
          }
            document.getElementById('posPantauModal').showModal();

            if (!posPantauMap) {
                initPosPantauMap();
                // loadPosPantauData();
                displayPosPantauMarkers();
            }
        }

        function closePosPantauModal() {
            document.getElementById('posPantauModal').close();
        }

        function initPosPantauMap() {
            posPantauMap = L.map('posPantauMap').setView([{{ $centerCoordinates[0] }}, {{ $centerCoordinates[1] }}],
                {{ $defaultZoom }});

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(posPantauMap);
        }

        function loadPosPantauData() {
            fetch('/api/pos-pantau')
                .then(response => response.json())
                .then(data => {
                    posPantauData = data.features;
                    
                    if (pos_pantau_id) {
                        selectedPosPantau = posPantauData.find(item => item.id == pos_pantau_id);
                        confirmSelection()
                    }

                })
                .catch(error => {
                    console.error('Error loading pos pantau data:', error);
                    // Show toast notification using DaisyUI
                    showToast('Error loading pos pantau data', 'error');
                });
        }

        function displayPosPantauMarkers(filteredData = null) {
            // Clear existing markers
            posPantauMarkers.forEach(marker => {
                posPantauMap.removeLayer(marker);
            });
            posPantauMarkers = [];

            const dataToDisplay = filteredData || posPantauData;

            dataToDisplay.forEach(feature => {
                const lat = parseFloat(feature.latitude);
                const lng = parseFloat(feature.longitude);

                if (isNaN(lat) || isNaN(lng)) return;

                // Create custom marker
                const markerIcon = L.divIcon({
                    className: 'custom-marker-icon',
                    html: `<div style="background-color: ${getMarkerColor(feature.jenis_pos)}; width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: bold;">${getMarkerSymbol(feature.jenis_pos)}</div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                const marker = L.marker([lat, lng], {
                        icon: markerIcon
                    })
                    .bindPopup(`
                <div class="text-sm">
                    <strong>${feature.nama_pos}</strong><br>
                    <span class="text-xs badge badge-outline">${feature.jenis_pos}</span><br>
                    <span class="text-xs">${feature.kecamatan?.nama || 'N/A'}, ${feature.kabupaten?.nama || 'N/A'}</span><br>
                    <span class="text-xs">Instansi: ${feature.kewenangan?.singkatan || 'N/A'}</span>
                </div>
            `)
                    .on('click', function() {
                        selectPosPantau(feature);
                    });

                marker.addTo(posPantauMap);
                posPantauMarkers.push(marker);
                if (feature.id === pos_pantau_id) {
                    marker.openPopup();
                    selectPosPantau(feature);
                }

            });
        }

        function getMarkerColor(jenisPos) {
            const colors = {
                'Pos Curah Hujan': '#3b82f6',
                'Pos Klimatologi': '#10b981',
                'Pos Tinggi Muka Air': '#f59e0b',
                'Pos Duga Air': '#8b5cf6'
            };
            return colors[jenisPos] || '#6b7280';
        }

        function getMarkerSymbol(jenisPos) {
            const symbols = {
                'Pos Curah Hujan': '☔',
                'Pos Klimatologi': '🌡️',
                'Pos Tinggi Muka Air': '🌊',
                'Pos Duga Air': '📊'
            };
            return symbols[jenisPos] || '📍';
        }

        function selectPosPantau(feature) {
            selectedPosPantau = feature;

            // Update selected marker appearance
            posPantauMarkers.forEach(marker => {
                const currentIcon = marker.getIcon();
                if (currentIcon.options.className === 'selected-marker-icon') {
                    // Reset to normal marker
                    const normalIcon = L.divIcon({
                        className: 'custom-marker-icon',
                        html: `<div style="background-color: ${getMarkerColor(feature.jenis_pos)}; width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: bold;">${getMarkerSymbol(feature.jenis_pos)}</div>`,
                        iconSize: [30, 30],
                        iconAnchor: [15, 15]
                    });
                    marker.setIcon(normalIcon);
                }
            });

            // Find and update selected marker
            const selectedMarker = posPantauMarkers.find(marker => {
                const markerLatLng = marker.getLatLng();
                return Math.abs(markerLatLng.lat - parseFloat(feature.latitude)) < 0.0001 &&
                    Math.abs(markerLatLng.lng - parseFloat(feature.longitude)) < 0.0001;
            });

            if (selectedMarker) {
                const selectedIcon = L.divIcon({
                    className: 'selected-marker-icon',
                    html: `<div style="background-color: #ef4444; width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">${getMarkerSymbol(feature.jenis_pos)}</div>`,
                    iconSize: [35, 35],
                    iconAnchor: [17, 17]
                });
                selectedMarker.setIcon(selectedIcon);
            }

            // Show selected info
            document.getElementById('selectedPosPantauInfo').classList.remove('hidden');
            document.getElementById('selectedPosPantauName').textContent = feature.nama_pos;
            document.getElementById('selectedPosPantauType').textContent = feature.jenis_pos;
            document.getElementById('selectedPosPantauLocation').textContent =
                `${feature.kecamatan?.nama || 'N/A'}, ${feature.kabupaten?.nama || 'N/A'}`;

            // Enable confirm button
            document.getElementById('confirmPosPantauSelection').disabled = false;
        }

        function confirmSelection() {
            if (selectedPosPantau) {
                // Set hidden input value
                document.getElementById('{{ $name }}').value = selectedPosPantau.id;

                // Set display input value
                document.getElementById('{{ $name }}_display').value =
                    `${selectedPosPantau.nama_pos} (${selectedPosPantau.jenis_pos})`;

                closePosPantauModal();
                showToast('Pos pantau berhasil dipilih', 'success');
            }
        }

        function handleSearch() {
            const searchTerm = document.getElementById('searchPosPantau').value.toLowerCase();

            if (!searchTerm) {
                displayPosPantauMarkers();
                return;
            }

            const filteredData = posPantauData.filter(feature => {
                return feature.nama_pos.toLowerCase().includes(searchTerm) ||
                    feature.jenis_pos.toLowerCase().includes(searchTerm) ||
                    (feature.kecamatan?.nama || '').toLowerCase().includes(searchTerm) ||
                    (feature.kabupaten?.nama || '').toLowerCase().includes(searchTerm);
            });

            displayPosPantauMarkers(filteredData);

            // Fit map to filtered markers
            if (filteredData.length > 0) {
                const group = new L.featureGroup();
                filteredData.forEach(feature => {
                    const lat = parseFloat(feature.latitude);
                    const lng = parseFloat(feature.longitude);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        group.addLayer(L.marker([lat, lng]));
                    }
                });
                posPantauMap.fitBounds(group.getBounds(), {
                    padding: [20, 20]
                });
            }
        }

        // Search on Enter key
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchPosPantau');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        handleSearch();
                    }
                });
            }
        });

        function showToast(message, type = 'info') {
            // Simple toast notification
            const toast = document.createElement('div');
            toast.className = `toast toast-top toast-end z-50`;
            toast.innerHTML = `
                <div class="alert alert-${type === 'success' ? 'success' : type === 'error' ? 'error' : type === 'warning' ? 'warning' : 'info'}">
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        }

        // Clear selection if modal is closed without confirming
        document.getElementById('posPantauModal').addEventListener('close', function() {
            if (selectedPosPantau) {
                // Reset selected marker appearance
                posPantauMarkers.forEach(marker => {
                    const currentIcon = marker.getIcon();
                    if (currentIcon.options.className === 'selected-marker-icon') {
                        // Find the feature for this marker to get proper color
                        const markerLatLng = marker.getLatLng();
                        const feature = posPantauData.find(f => {
                            return Math.abs(parseFloat(f.latitude) - markerLatLng.lat) < 0.0001 &&
                                Math.abs(parseFloat(f.longitude) - markerLatLng.lng) < 0.0001;
                        });

                        if (feature) {
                            const normalIcon = L.divIcon({
                                className: 'custom-marker-icon',
                                html: `<div style="background-color: ${getMarkerColor(feature.jenis_pos)}; width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: bold;">${getMarkerSymbol(feature.jenis_pos)}</div>`,
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            });
                            marker.setIcon(normalIcon);
                        }
                    }
                });

                selectedPosPantau = null;
                document.getElementById('selectedPosPantauInfo').classList.add('hidden');
                document.getElementById('confirmPosPantauSelection').disabled = true;
            }
        });
    </script>
@endpush
