<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="sih3-modern">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/pages/geospasial-map.css'])
    <style>
        #listLayer {
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }

        #listLayer.open {
            max-height: 1000px;
            /* Atur sesuai kebutuhan konten */
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Loader -->
    <div id="loader" role="status" aria-live="polite"
        class="fixed inset-0 flex items-center justify-center bg-white z-50">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500"></div>
    </div>

    <!-- Toggle Button untuk Mobile -->
    <button class="sidebar-toggle" id="sidebarToggle">
        ☰
    </button>

    <main class="main-container h-max-screen">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2 class="text-xl font-bold">Map Controls</h2>
                <p class="text-sm opacity-75 mt-2">Explore and navigate the map</p>
            </div>
            <div class="sidebar-content">
                <div class="sidebar-item flex items-center py-1" id="homeButton">
                    <span class="mr-2">🏠</span> SIH3 SULTENG
                </div>
                <!-- Search Section -->
                {{-- <div class="sidebar-section">
                    <h3>Cari</h3>
                    <input type="text" class="search-input px-2 py-1 text-xs rounded w-32"
                        placeholder="Cari tempat..." id="searchInput">

                    <div class="sidebar-section hidden" id="searchResultsSection">
                        <small>Hasil pencarian</small>
                        <div class="bg-white bg-opacity-10 p-2 rounded-lg">
                            <ul id="searchResults" class="list-none p-0 m-0">
                            </ul>
                        </div>
                    </div>
                </div> --}}

                <div class="sidebar-section">
                    <h3 class="text-sm cursor-pointer hover:bg-primary-content hover:text-primary p-2 bg-white bg-opacity-10 p-2 rounded-lg open"
                        id="toggleButton">Daftar Kabupaten/Kota</h3>
                    <div id="listLayer">

                    </div>
                </div>
                <!-- Info Section -->
                <div class="sidebar-section">
                    <h3 class="text-xs">Map Information</h3>
                    <div class="bg-white bg-opacity-10 p-2 rounded-lg text-xs">
                        <div class="mb-1">
                            <strong>Coordinates : </strong>
                            <span id="currentCoords">-0.8917, 119.8707</span>
                        </div>
                        <div>
                            <strong>Zoom Level : </strong>
                            <span id="currentZoom">8</span>
                        </div>
                        <div id="pin-legend">
                            <h4>Keterangan Pin</h4>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                <li style="display: flex; align-items: center; margin-bottom: 6px;">
                                    <img src="/images/pin/merah.svg" alt="Merah"
                                        style="width: 24px; height: 24px; margin-right: 8px;">
                                    Pos Curah Hujan
                                </li>
                                <li style="display: flex; align-items: center; margin-bottom: 6px;">
                                    <img src="/images/pin/biru.svg" alt="Biru"
                                        style="width: 24px; height: 24px; margin-right: 8px;">
                                    Pos Duga Air
                                </li>
                                <li style="display: flex; align-items: center; margin-bottom: 6px;">
                                    <img src="/images/pin/hijau.svg" alt="Hijau"
                                        style="width: 24px; height: 24px; margin-right: 8px;">
                                    Pos Klimatologi
                                </li>
                                <li style="display: flex; align-items: center;">
                                    <img src="/images/pin/kuning.svg" alt="Kuning"
                                        style="width: 24px; height: 24px; margin-right: 8px;">
                                    Lainnya / Tidak Diketahui
                                </li>
                            </ul>
                        </div>

                    </div>
                    <div id="markerInfo" class="bg-white bg-opacity-10 p-2 rounded-lg text-xs">
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="map-container">
            <div class="overlay-container">
                <!-- Peta -->
                <div id="map"></div>
            </div>
        </div>
    </main>
    <footer id="mobileFooter"
        class="block md:hidden fixed bottom-0 left-0 w-full bg-white bg-opacity-90 text-slate-700 text-center py-2 z-50 shadow border-t border-slate-200">
        <div id="footerMarkInfo"></div>
        SIH3 SULTENG &copy; 2025
    </footer>
    <!-- Di bagian head atau sebelum penutup </body> -->
    <script src="https://cdn.jsdelivr.net/npm/terraformer@1.0.8/terraformer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/terraformer-wkt-parser@1.2.1/terraformer-wkt-parser.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            const observer = new MutationObserver(() => {
                const visible = sidebar.classList.contains('open');
                if (visible) {
                    sidebarToggle.classList.add('invisible');
                } else {
                    sidebarToggle.classList.remove('invisible');
                }
            });
            observer.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            // Inisialisasi peta Leaflet
            const centerOfMaps = [-1.2842, 121.8274];
            const map = L.map('map').setView(centerOfMaps, 8);

            // Tile layer (pakai OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            const listLayerContainer = document.getElementById('listLayer');

            let addedMarkers = new Set(); // Untuk menyimpan koordinat marker yang sudah ditambahkan

            try {
                const response = await fetch('/api/geo-features/map-kabupaten');
                if (!response.ok) throw new Error('Network response was not ok');
                const geoJsonData = await response.json();

                // Validasi format GeoJSON
                if (!geoJsonData.type || geoJsonData.type !== 'FeatureCollection' || !Array.isArray(geoJsonData
                        .features)) {
                    throw new Error('Invalid GeoJSON format');
                }

                // Simpan referensi layer untuk toggle visibility
                const areaLayers = {};

                // Warna berbeda untuk setiap kabupaten
                const colors = [
                    '#FF5733', '#581845', '#3357FF', '#F333FF', '#FF33A8',
                    '#33FFF5', '#FF8C33', '#8C33FF', '#33FF8C', '#FF338C',
                    '#FFC300', '#900C3F', '#33FF57'
                ];

                geoJsonData.features.forEach((feature, index) => {
                    try {
                        // Buat layer GeoJSON di Leaflet
                        const layer = L.geoJSON(feature, {
                            style: {
                                color: colors[index % colors.length],
                                weight: 0.5,
                                opacity: 0.9,
                                fillOpacity: 0.3,
                                fillColor: colors[index % colors.length]
                            },
                            onEachFeature: (geoJsonFeature, leafletLayer) => {
                                leafletLayer.bindPopup(`
                            <b>${feature.properties.name}</b><br>
                            <small>Kabupaten: ${feature.properties.properties.WADMKK}</small>
                        `);

                                // Menambahkan event klik
                                leafletLayer.on('click', (e) => {
                                    console.log(
                                        `Area ${feature.properties.name} diklik!`
                                    );
                                    console.log(
                                        `Koordinat: ${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`
                                    );
                                    getKabInfo(feature.properties.properties.KDWKB);
                                });
                            }
                        });

                        // Tambah ke peta
                        layer.addTo(map);
                        areaLayers[feature.properties.id] = layer;

                        // Buat elemen toggle di sidebar
                        const toggleContainer = document.createElement('div');
                        toggleContainer.classList.add('layer-toggle', 'flex', 'items-center', 'py-1',
                            'cursor-pointer', 'hover:bg-blue-50', 'px-2', 'rounded',
                            'hover:text-primary');

                        // Checkbox untuk toggle
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.checked = true;
                        checkbox.classList.add('mr-2', 'layer-checkbox');
                        checkbox.id = `layer-${feature.properties.id}`;

                        // Label nama layer
                        const label = document.createElement('label');
                        label.htmlFor = `layer-${feature.properties.id}`;
                        label.classList.add('cursor-pointer', 'flex', 'items-center', 'w-full');

                        // Icon mata
                        const eyeIcon = document.createElement('span');
                        eyeIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>`;

                        // Nama kabupaten
                        const nameSpan = document.createElement('span');
                        nameSpan.textContent = feature.properties.name;
                        nameSpan.classList.add('text-sm');

                        // Warna identifikasi
                        const colorIndicator = document.createElement('span');
                        colorIndicator.classList.add('w-3', 'h-3', 'rounded-full', 'ml-2');
                        colorIndicator.style.backgroundColor = colors[index % colors.length];

                        label.appendChild(nameSpan);
                        label.appendChild(colorIndicator);

                        toggleContainer.appendChild(checkbox);
                        toggleContainer.appendChild(label);

                        // Event toggle visibility
                        checkbox.addEventListener('change', (e) => {
                            if (e.target.checked) {
                                map.addLayer(layer);
                            } else {
                                map.removeLayer(layer);
                            }
                        });

                        listLayerContainer.appendChild(toggleContainer);

                    } catch (error) {
                        console.error(
                            `Error processing feature ${feature.properties?.name || 'unknown'}:`,
                            error);
                    }
                });

                // Update koordinat saat mouse move di peta
                map.on('mousemove', (e) => {
                    document.getElementById('currentCoords').textContent =
                        `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`;
                });

                // Update zoom level
                map.on('zoomend', () => {
                    document.getElementById('currentZoom').textContent = map.getZoom();
                });

                // Sembunyikan loader setelah peta siap
                document.getElementById('loader').style.display = 'none';

            } catch (error) {
                console.error('Error loading GeoJSON data:', error);
                document.getElementById('loader').style.display = 'none';
                alert('Gagal memuat data peta. Silakan coba lagi.');
            }

            // Fungsi untuk mengambil data titik pantau berdasarkan kabupaten
            async function getKabInfo(kab_id) {
                console.log(kab_id);
                try {
                    const dataFetch = await fetch(`/api/geo-features/map-kabupaten/info?KDWKB=${kab_id}`);
                    const data = await dataFetch.json();
                    console.log(data);

                    // Loop untuk menambahkan marker berdasarkan titik pantau
                    data.titik_pantau.forEach((item) => {
                        const {
                            latitude,
                            longitude,
                            nama_pos,
                            jenis_pos
                        } = item;
                        const latLng = `${latitude},${longitude}`;

                        // Pastikan marker belum ada di koordinat yang sama
                        if (!addedMarkers.has(latLng)) {
                            // const marker = L.marker([parseFloat(latitude), parseFloat(longitude)]).addTo(map);
                            const icon = getIconForJenisPos(jenis_pos);
                            const marker = L.marker([parseFloat(latitude), parseFloat(longitude)], {
                                    icon
                                })
                                .addTo(map);
                            marker.bindPopup(`
                                <b>${nama_pos}</b><br>
                                <small>Jenis Pos: ${jenis_pos}</small><br>
                                <small>Latitude: ${latitude}</small><br>
                                <small>Longitude: ${longitude}</small>
                            `);
                            addedMarkers.add(
                                latLng); // Menyimpan koordinat marker yang sudah ditambahkan
                        }
                    });
                } catch (error) {
                    console.error(`Error fetching kabupaten info: ${error}`);
                }
            }

            function getIconForJenisPos(jenis_pos) {
                let iconUrl = '/images/pin/kuning.svg'; // default
                if (jenis_pos === "Pos Curah Hujan") {
                    iconUrl = '/images/pin/merah.svg';
                } else if (jenis_pos === "Pos Duga Air") {
                    iconUrl = '/images/pin/biru.svg';
                } else if (jenis_pos === "Pos Klimatologi") {
                    iconUrl = '/images/pin/hijau.svg';
                }

                return L.icon({
                    iconUrl: iconUrl,
                    iconSize: [32, 32], // sesuaikan ukuran jika perlu
                    iconAnchor: [16, 32], // titik bawah ikon
                    popupAnchor: [0, -32] // posisi popup terhadap ikon
                });
            }

            const toggleButton = document.getElementById('toggleButton');
            const listLayer = document.getElementById('listLayer');

            toggleButton.addEventListener('click', () => {
                // Toggle kelas 'open' untuk membuka/tutup
                listLayer.classList.toggle('open');
            });

        });
    </script>
</body>

</html>
