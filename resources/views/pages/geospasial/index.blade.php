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
        #listLayerCAT,
        #listLayerWS,
        #listLayerKAB {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
        }

        #listLayerCAT.open,
        #listLayerWS.open,
        #listLayerKAB.open {
            max-height: 1000px;
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

                <div class="sidebar-section pb-3">
                    <div id="toggleCAT" class="flex items-center justify-start cursor-pointer hover:text-primary hover:bg-primary-content bg-white bg-opacity-10 p-2 rounded-lg open flex-1 gap-2 p-2">
                        <input type="checkbox" id="checkAllCAT" class="toggle-all-checkbox">
                        <h3 class="text-xs hover:text-primary ">Daftar CAT</h3>
                    </div>
                    <div id="listLayerCAT"></div>
                </div>

                <div class="sidebar-section pb-3">
                    <div id="toggleWS" class="flex items-center justify-start cursor-pointer hover:text-primary hover:bg-primary-content bg-white bg-opacity-10 p-2 rounded-lg open flex-1 gap-2 p-2">
                        <input type="checkbox" id="checkAllWS" class="toggle-all-checkbox">
                        <h3 class="text-xs hover:text-primary ">Daftar WS</h3>
                    </div>
                    <div id="listLayerWS"></div>
                </div>

                <div class="sidebar-section pb-3">
                    <div id="toggleKAB" class="flex items-center justify-start cursor-pointer hover:text-primary hover:bg-primary-content bg-white bg-opacity-10 p-2 rounded-lg open flex-1 gap-2 p-2">
                        <input type="checkbox" id="checkAllKAB" class="toggle-all-checkbox">
                        <h3 class="text-xs hover:text-primary ">Daftar Kabupaten/Kota</h3>
                    </div>
                    <div id="listLayerKAB"></div>
                </div>

                <!-- Info Section -->
                <div class="sidebar-section pt-3">
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
    
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
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
            
            const centerOfMaps = [-1.2842, 121.8274];
            const map = L.map('map').setView(centerOfMaps, 8);
            const esriSatLayer = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles © Esri — Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye',
                    maxZoom: 19
                }).addTo(map);

            const addedMarkers = new Set();

            const layerGroups = {
                KAB: L.layerGroup().addTo(map),
                WS: L.layerGroup().addTo(map),
                CAT: L.layerGroup().addTo(map),
                // jika tambah baru tambahkan disini 1 : NEW: L.layerGroup().addTo(map),
            };

            async function loadGeoLayer({
                url,
                containerId,
                layerGroup,
                defaultShow = false,
                colorList = [],
                customStyle = null,
                clickCallback = null
            }) {
                const container = document.getElementById(containerId);
                if (!container) return;

                try {
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Gagal fetch ' + url);

                    const geoJsonData = await response.json();
                    if (geoJsonData.type !== 'FeatureCollection' || !Array.isArray(geoJsonData.features)) {
                        throw new Error('Format GeoJSON tidak valid');
                    }

                    geoJsonData.features.forEach((feature, index) => {
                        const color = colorList[index % colorList.length] || '#007bff';

                        const layer = L.geoJSON(feature, {
                            style: typeof customStyle === 'function'
                                ? customStyle(color)
                                : {
                                    color: color,
                                    weight: 0.5,
                                    opacity: 0.9,
                                    fillOpacity: 0.3,
                                    fillColor: color
                                },
                            onEachFeature: (geoJsonFeature, leafletLayer) => {
                                leafletLayer.bindPopup(
                                    `<b>${feature.properties.name}</b>`);

                                if (typeof clickCallback === 'function') {
                                    leafletLayer.on('click', e => clickCallback(feature,
                                        e));
                                }
                            }
                        });

                        // Tambahkan ke peta hanya jika defaultShow true
                        if (defaultShow) {
                            layerGroup.addLayer(layer);
                        }

                        // Checkbox untuk layer ini
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.checked = defaultShow;
                        checkbox.className = 'mr-2';

                        // Label
                        const label = document.createElement('label');
                        label.className = 'cursor-pointer flex items-center w-full';
                        label.textContent = feature.properties.name;

                        const colorDot = document.createElement('span');
                        colorDot.className = 'w-3 h-3 rounded-full ml-2';
                        colorDot.style.backgroundColor = color;
                        label.appendChild(colorDot);

                        const toggleContainer = document.createElement('div');
                        toggleContainer.className =
                            'flex items-center py-1 px-2 cursor-pointer hover:bg-blue-50 hover:text-primary';
                        toggleContainer.appendChild(checkbox);
                        toggleContainer.appendChild(label);

                        checkbox.addEventListener('change', e => {
                            if (e.target.checked) {
                                layerGroup.addLayer(layer);
                            } else {
                                layerGroup.removeLayer(layer);
                            }
                        });

                        container.appendChild(toggleContainer);
                    });
                } catch (error) {
                    console.error(`Error loading ${containerId}:`, error);
                }
            }

            await loadGeoLayer({
                url: '/api/geo-features/map-kabupaten',
                containerId: 'listLayerKAB',
                layerGroup: layerGroups.KAB,
                defaultShow: false,
                colorList: [
                    '#FF5733', '#C70039', '#900C3F', '#581845', '#FF8C00', '#FF4500',
                    '#FF1493', '#FF69B4', '#FF6347', '#FFB6C1', '#FFD700', '#FFA500',
                    '#FFDAB9', '#FFDEAD', '#FFFACD', '#EEE8AA', '#F0E68C', '#BDB76B',
                    '#DAA520', '#B8860B', '#CD853F', '#D2691E', '#A0522D', '#8B4513'
                ],
                clickCallback: (feature, e) => getKabInfo(feature.properties.properties.KDWKB),
                customStyle: (color) => ({
                    color: color,
                    weight: 1.5,
                    opacity: 1,
                    dashArray: '5, 5',
                    fill: false
                })
            });
            setupToggleAll('listLayerCAT', 'checkAllCAT', layerGroups.CAT);

            await loadGeoLayer({
                url: '/api/geo-features/map-ws',
                containerId: 'listLayerWS',
                layerGroup: layerGroups.WS,
                defaultShow: false,
                colorList: [
                    '#1E90FF', '#00BFFF', '#87CEFA', '#4682B4', '#5F9EA0', '#6495ED',
                    '#7B68EE', '#6A5ACD', '#483D8B', '#4169E1', '#0000CD', '#00008B',
                    '#8A2BE2', '#9370DB', '#BA55D3', '#9400D3', '#9932CC', '#8B008B',
                    '#6B8E23', '#3CB371', '#2E8B57', '#228B22', '#008000', '#006400'
                ]
            });
            setupToggleAll('listLayerWS', 'checkAllWS', layerGroups.WS);

            await loadGeoLayer({
                url: '/api/geo-features/map-cat',
                containerId: 'listLayerCAT',
                layerGroup: layerGroups.CAT,
                defaultShow: false,
                colorList: [
                    '#00CED1', '#20B2AA', '#40E0D0', '#48D1CC', '#00FA9A', '#7FFFD4',
                    '#7FFF00', '#ADFF2F', '#32CD32', '#90EE90', '#98FB98', '#00FF7F',
                    '#DC143C', '#E9967A', '#FA8072', '#F08080', '#CD5C5C', '#8B0000',
                    '#F4A460', '#DEB887', '#D2B48C', '#BC8F8F', '#FFE4B5', '#FFDAB9'
                ]
            });
            setupToggleAll('listLayerKAB', 'checkAllKAB', layerGroups.KAB);
            // jika tambah baru tambahkan disini 2 (dari await sampai setupToggleAll)

            async function getKabInfo(kab_id) {
                try {
                    const res = await fetch(`/api/geo-features/map-kabupaten/info?KDWKB=${kab_id}`);
                    const data = await res.json();
                    data.titik_pantau.forEach(item => {
                        const {
                            latitude,
                            longitude,
                            nama_pos,
                            jenis_pos
                        } = item;
                        const latLng = `${latitude},${longitude}`;
                        if (!addedMarkers.has(latLng)) {
                            const icon = getIconForJenisPos(jenis_pos);
                            L.marker([parseFloat(latitude), parseFloat(longitude)], {
                                    icon
                                }).addTo(map)
                                .bindPopup(
                                    `<b>${nama_pos}</b><br>Jenis Pos: ${jenis_pos}<br>Lat: ${latitude}<br>Lng: ${longitude}`
                                    );
                            addedMarkers.add(latLng);
                        }
                    });
                } catch (e) {
                    console.error('Error fetch kab info:', e);
                }
            }

            function getIconForJenisPos(jenis_pos) {
                let iconUrl = '/images/pin/kuning.svg';
                if (jenis_pos === "Pos Curah Hujan") iconUrl = '/images/pin/merah.svg';
                if (jenis_pos === "Pos Duga Air") iconUrl = '/images/pin/biru.svg';
                if (jenis_pos === "Pos Klimatologi") iconUrl = '/images/pin/hijau.svg';

                return L.icon({
                    iconUrl,
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });
            }

            document.getElementById('homeButton').addEventListener('click', () => {
                window.location.href = '/';
            });

            // Toggle sidebar sections
            document.getElementById('toggleCAT').addEventListener('click', () => {
                document.getElementById('listLayerCAT').classList.toggle('open');
            });

            document.getElementById('toggleWS').addEventListener('click', () => {
                document.getElementById('listLayerWS').classList.toggle('open');
            });

            document.getElementById('toggleKAB').addEventListener('click', () => {
                document.getElementById('listLayerKAB').classList.toggle('open');
            });

            // Display koordinat dan zoom
            map.on('mousemove', e => {
                document.getElementById('currentCoords').textContent =
                    `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`;
            });

            map.on('zoomend', () => {
                document.getElementById('currentZoom').textContent = map.getZoom();
            });

            document.getElementById('loader').style.display = 'none';

            function setupToggleAll(containerId, checkboxId, layerGroup) {
                const masterCheckbox = document.getElementById(checkboxId);
                const container = document.getElementById(containerId);

                masterCheckbox.addEventListener('change', () => {
                    const checkboxes = container.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(cb => {
                        cb.checked = masterCheckbox.checked;
                        const event = new Event('change');
                        cb.dispatchEvent(event);
                    });
                });
            }

        });
    </script>

</body>

</html>
