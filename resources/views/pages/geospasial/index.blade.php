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
        #listLayerPP,
        #listLayerSS,
        #listLayerKAB {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
        }

        #listLayerCAT.open,
        #listLayerWS.open,
        #listLayerPP.open,
        #listLayerSS.open,
        #listLayerKAB.open {
            max-height: 150px;
            opacity: 1;
            overflow-y: auto;
        }

        .custom-popup {
            /* max-width: 500px !important; */
            width: 500px !important;
        }

        .custom-popup .leaflet-popup-content-wrapper,
        .custom-popup .leaflet-popup-content {
            width: auto !important;
            max-width: 100% !important;
        }

        .custom-popup .flex {
            flex-wrap: wrap;
            /* biar isi bisa turun ke baris berikutnya */
            min-width: 0;
            /* cegah item flex melebar */
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
        <div class="relative sidebar" id="sidebar">
            <div class="absolute top-2 right-2">
                <button class="btn btn-sm btn-circle btn-ghost sidebar-close">✕</button>
            </div>
            <div class="sidebar-header">
                <h2 class="text-xl font-bold">Map Controls</h2>
                <p class="text-sm opacity-75 mt-2">Explore and navigate the map</p>
            </div>
            <div class="sidebar-content">
                <div class="sidebar-item flex items-center py-1" id="homeButton">
                    <span class="mr-2">🏠</span> SIH3 SULTENG
                </div>

                <div class="sidebar-section pb-3">
                    <div id="toggleCAT"
                        class="flex items-center justify-start cursor-pointer hover:text-primary hover:bg-primary-content bg-white bg-opacity-10 p-2 rounded-lg open flex-1 gap-2 p-2">
                        <input type="checkbox" id="checkAllCAT" class="toggle-all-checkbox toggle toggle-xs">
                        <span class="text-sm hover:text-primary ">Daftar CAT</span>
                    </div>
                    <div id="listLayerCAT" class="listLayer"></div>
                </div>

                <div class="sidebar-section pb-3">
                    <div id="toggleWS"
                        class="flex items-center justify-start cursor-pointer hover:text-primary hover:bg-primary-content bg-white bg-opacity-10 p-2 rounded-lg open flex-1 gap-2 p-2">
                        <input type="checkbox" id="checkAllWS" class="toggle-all-checkbox toggle toggle-xs">
                        <span class="text-sm hover:text-primary ">Daftar WS</span>
                    </div>
                    <div id="listLayerWS" class="listLayer"></div>
                </div>

                <div class="sidebar-section pb-3">
                    <div id="togglePP"
                        class="flex items-center justify-start cursor-pointer hover:text-primary hover:bg-primary-content bg-white bg-opacity-10 p-2 rounded-lg open flex-1 gap-2 p-2">
                        <input type="checkbox" id="checkAllPP" class="toggle-all-checkbox toggle toggle-xs">
                        <span class="text-sm hover:text-primary ">Pos Pantau</span>
                    </div>
                    <div id="listLayerPP" class="listLayer"></div>
                </div>

                <div class="sidebar-section pb-3">
                    <div id="toggleSS"
                        class="flex items-center justify-start cursor-pointer hover:text-primary hover:bg-primary-content bg-white bg-opacity-10 p-2 rounded-lg open flex-1 gap-2 p-2">
                        <input type="checkbox" id="checkAllSS" class="toggle-all-checkbox toggle toggle-xs">
                        <span class="text-sm hover:text-primary ">Sungai</span>
                    </div>
                    <div id="listLayerSS" class="listLayer"></div>
                </div>

                <div class="sidebar-section pb-3">
                    <div id="toggleKAB"
                        class="flex items-center justify-start cursor-pointer hover:text-primary hover:bg-primary-content bg-white bg-opacity-10 p-2 rounded-lg open flex-1 gap-2 p-2">
                        <input type="checkbox" id="checkAllKAB" class="toggle-all-checkbox toggle toggle-xs">
                        <span class="text-sm hover:text-primary ">Daftar Kabupaten/Kota</span>
                    </div>
                    <div id="listLayerKAB" class="listLayer"></div>
                </div>

                <div class="sidebar-section pb-3">
                    <div class="flex items-center justify-start bg-white bg-opacity-10 p-2 rounded-lg flex-1 gap-2 p-2">
                        <input type="checkbox" checked="checked" class="toggle toggle-xs" id="toggleBackgroundMap" />
                        <span class="text-md">Gambar Latar</span>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="sidebar-section pt-3">
                    <h3 class="text-xs">Map Information</h3>
                    <div class="bg-white bg-opacity-10 p-2 rounded-lg text-xs">
                        <div class="mb-1">
                            <strong>Coordinates : </strong>
                            <span id="currentCoords"></span>
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

    <!-- Modal -->
<div id="modalData"  class="fixed inset-0 z-[9999] bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-6xl p-4">
        <div class="flex justify-between items-center border-b pb-2 mb-4">
            <h2 id="modalTitle" class="text-lg font-bold">Data</h2>
            <button onclick="closeModal()" class="text-red-500 text-xl font-bold">&times;</button>
        </div>
        <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
            <table class="table-auto border-collapse border border-gray-300 w-full text-sm">
                <thead>
                    <tr id="modalHeader" class="bg-gray-100"></tr>
                </thead>
                <tbody id="modalBody"></tbody>
            </table>
        </div>
    </div>
</div>


    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebarclose = document.querySelector('.sidebar-close');

            const observer = new MutationObserver(() => {
                const visible = sidebar.classList.contains('open');
                if (visible) {
                    sidebarToggle.classList.add('invisible');
                } else {
                    sidebarToggle.classList.remove('invisible');
                }
            });

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });
            sidebarclose.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });

            observer.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });

            const centerOfMaps = [-0.9071, 119.9119];
            const map = L.map('map', {
                zoomControl: false,
            }).setView(centerOfMaps, 8);
            map.on('popupopen', function(e) {
                const popupContent = e.popup.getElement()?.querySelector('.leaflet-popup-content');
                if (popupContent) {
                    popupContent.style.width = ''; // hapus inline width
                }
            });

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
                PP: L.layerGroup().addTo(map),
                SS: L.layerGroup().addTo(map),
                // jika tambah baru tambahkan disini 1 : NEW: L.layerGroup().addTo(map),
            };

            async function loadGeoLayer({
                url,
                containerId,
                layerGroup,
                defaultShow = false,
                defaultLoad = false,
                colorList = [],
                customStyle = null,
                clickCallback = null
            }) {
                const container = document.getElementById(containerId);
                if (!container) return;

                try {
                    container.classList.add('px-2');
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Gagal fetch ' + url);

                    const geoJsonData = await response.json();
                    if (geoJsonData.type !== 'FeatureCollection' || !Array.isArray(geoJsonData.features)) {
                        throw new Error('Format GeoJSON tidak valid');
                    }

                    geoJsonData.features.forEach((feature, index) => {
                        const color = colorList[index % colorList.length] || '#007bff';
                        // Tentukan style berdasarkan jenis geometri
                        const style = (geoJsonFeature) => {
                            if (typeof customStyle === 'function') {
                                return customStyle(color, geoJsonFeature);
                            }
                            const geometryType = geoJsonFeature.geometry.type;

                            if (geometryType === 'Polygon' || geometryType === 'MultiPolygon') {
                                return {
                                    color: color,
                                    weight: 0.5,
                                    opacity: 0.9,
                                    fillOpacity: 0.3,
                                    fillColor: color
                                };
                            } else if (geometryType === 'LineString' || geometryType === 'MultiLineString') {
                                if (typeof customStyle === 'function') {
                                    return customStyle(color, geoJsonFeature);
                                }
                            }
                            // Default style untuk jenis geometri selain Point
                            return {
                                color: '#333',
                                weight: 1,
                                opacity: 1
                            };
                        };

                        // Tambahkan fungsi pointToLayer untuk fitur Point
                        const pointToLayer = (geoJsonFeature, latlng) => {
                            const icon = getIconForJenisPos(geoJsonFeature.properties
                                .tag);
                            return L.marker(latlng, {
                                icon: icon
                            });
                        };

                        const layer = L.geoJSON(feature, {
                            style: style,
                            pointToLayer: pointToLayer,
                            onEachFeature: (geoJsonFeature, leafletLayer) => {
                                const viewPopup = setupPopUp(feature);
                                leafletLayer.bindPopup(viewPopup, {
                                    className: 'custom-popup'
                                });

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

                        // sidebar handler
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.checked = defaultShow;
                        checkbox.className = 'mr-2 toggle toggle-xs';

                        const label = document.createElement('label');
                        label.className = 'cursor-pointer flex items-center w-full';
                        label.textContent = feature.properties.name;

                        const colorDot = document.createElement('span');
                        colorDot.className = 'w-3 h-3 rounded-full ml-2';
                        colorDot.style.backgroundColor = color;
                        label.appendChild(colorDot);

                        const toggleContainer = document.createElement('div');
                        toggleContainer.className =
                            'flex items-center py-1 px-2 cursor-pointer hover:bg-blue-50 hover:text-primary text-sm';
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

            // Menggunakan konsistensi dalam penulisan dan struktur kode
            const layerConfigs = [{
                    layerType: "polygon",
                    id_check_list: 'checkAllKAB',
                    url: '/api/geo-features/map-kabupaten',
                    containerId: 'listLayerKAB',
                    layerGroup: layerGroups.KAB,
                    defaultShow: false,
                    defaultLoad: false,
                    colorList: [
                        '#FF5733', '#C70039', '#900C3F', '#581845', '#FF8C00', '#FF4500',
                        '#FF1493', '#FF69B4', '#FF6347', '#FFB6C1', '#FFD700', '#FFA500',
                        '#FFDAB9', '#FFDEAD', '#FFFACD', '#EEE8AA', '#F0E68C', '#BDB76B',
                        '#DAA520', '#B8860B', '#CD853F', '#D2691E', '#A0522D', '#8B4513'
                    ],
                    // clickCallback: (feature, e) => getKabInfo(feature.properties.properties.KDWKB),
                    customStyle: (color) => ({
                        color: color,
                        weight: 1.5,
                        // opacity: 0.1,
                        dashArray: '5, 5',
                        // fill: false
                    })
                },
                {
                    layerType: "polygon",
                    id_check_list: 'checkAllWS',
                    url: '/api/geo-features/map-ws',
                    containerId: 'listLayerWS',
                    layerGroup: layerGroups.WS,
                    defaultShow: true,
                    defaultLoad: true,
                    colorList: [
                        '#BA55D3', '#8A2BE2', '#7B68EE', '#00BFFF', '#5F9EA0', '#9932CC',
                        '#4169E1', '#4682B4', '#8B008B', '#1E90FF', '#6B8E23', '#3CB371',
                        '#00CED1', '#483D8B', '#6A5ACD', '#228B22', '#2E8B57', '#0000CD',
                        '#F08080', '#006400', '#D2691E', '#FF6347', '#8B4513', '#00008B'
                    ],
                    customStyle: (color) => ({
                        color: color,
                        weight: 1.5,
                        // dashArray: '5, 5',
                    })
                },
                {
                    layerType: "polygon",
                    id_check_list: 'checkAllCAT',
                    url: '/api/geo-features/map-cat',
                    containerId: 'listLayerCAT',
                    layerGroup: layerGroups.CAT,
                    defaultShow: false,
                    defaultLoad: false,
                    colorList: [
                        '#00CED1', '#20B2AA', '#40E0D0', '#48D1CC', '#00FA9A', '#7FFFD4',
                        '#7FFF00', '#ADFF2F', '#32CD32', '#90EE90', '#98FB98', '#00FF7F',
                        '#DC143C', '#E9967A', '#FA8072', '#F08080', '#CD5C5C', '#8B0000',
                        '#F4A460', '#DEB887', '#D2B48C', '#BC8F8F', '#FFE4B5', '#FFDAB9'
                    ]
                },
                {
                    layerType: "point",
                    id_check_list: 'checkAllPP',
                    url: '/api/pos-pantau',
                    containerId: 'listLayerPP',
                    layerGroup: layerGroups.PP,
                    defaultShow: true,
                    defaultLoad: true,
                    // customStyle: (feature) => {
                    //     const jenis_pos = feature.jenis_pos;
                    //     const icon = getIconForJenisPos(jenis_pos);
                    //     return {
                    //         icon: icon
                    //     };
                    // }
                },
                {
                    layerType: "LineString",
                    id_check_list: 'checkAllSS',
                    url: '/api/geo-features/map-sungai',
                    containerId: 'listLayerSS',
                    layerGroup: layerGroups.SS,
                    defaultShow: true,
                    defaultLoad: true,
                    customStyle: (color, feature) => ({
                        color: color,
                        weight: 3 / feature.properties.properties.ORDE ,
                        // opacity: 1,
                        // dashArray: '5, 5',
                        // fill: false
                    })
                }
            ];

            // Load Geo Layers with the configurations
            for (const config of layerConfigs) {
                config.loaded = false;
                if (config.defaultLoad) {
                    await loadGeoLayer({
                        url: config.url,
                        containerId: config.containerId,
                        layerGroup: config.layerGroup,
                        defaultShow: config.defaultShow,
                        defaultLoad: config.defaultLoad,
                        colorList: config.colorList,
                        clickCallback: config.clickCallback,
                        customStyle: config.customStyle
                    });
                    await setupToggleAll(config.containerId, config.id_check_list, layerGroups.KAB);
                    document.getElementById(config.id_check_list).checked = config.defaultShow
                    config.loaded = true;
                }
                document.getElementById(config.id_check_list).addEventListener('change', async e => {
                    if (!config.loaded) {
                        await loadGeoLayer({
                            url: config.url,
                            containerId: config.containerId,
                            layerGroup: config.layerGroup,
                            defaultShow: true,
                            defaultLoad: config.defaultLoad,
                            colorList: config.colorList,
                            clickCallback: config.clickCallback,
                            customStyle: config.customStyle
                        });
                        await setupToggleAll(config.containerId, config.id_check_list, layerGroups.KAB);
                        document.getElementById(config.id_check_list).checked = config.defaultShow
                        config.loaded = true;
                    }
                });
            }

            function getIconForJenisPos(jenis_pos) {
                let iconUrl = '/images/pin/kuning.svg';
                if (jenis_pos === "Pos Curah Hujan") iconUrl = '/images/pin/merah.svg';
                if (jenis_pos === "Pos Duga Air") iconUrl = '/images/pin/biru.svg';
                if (jenis_pos === "Pos Klimatologi") iconUrl = '/images/pin/hijau.svg';

                return L.icon({
                    iconUrl,
                    iconSize: [25, 25],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });
            }

            function toggleBackgroundMap() {
                // Cek jika layer esriSatLayer sudah ada di peta
                if (map.hasLayer(esriSatLayer)) {
                    map.removeLayer(esriSatLayer); // Hapus layer jika sudah ada
                } else {
                    esriSatLayer.addTo(map); // Tambahkan layer jika belum ada
                }
            }
            document.getElementById('toggleBackgroundMap').addEventListener('click', toggleBackgroundMap);

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
            document.getElementById('togglePP').addEventListener('click', () => {
                document.getElementById('listLayerPP').classList.toggle('open');
            });
            document.getElementById('toggleSS').addEventListener('click', () => {
                document.getElementById('listLayerSS').classList.toggle('open');
            });

            // Display koordinat dan zoom
            map.on('mousemove', e => {
                document.getElementById('currentCoords').textContent =
                    `${decimalToDMS(e.latlng.lat)}, ${decimalToDMS(e.latlng.lng)}`;
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

            function setupPopUp(feature) {
                // Wilayah Sungai
                if (feature.properties.tag === "Wilayah Sungai") {
                    return `
                        <div class="w-full space-y-3">
                            <h4 class="font-bold text-base text-gray-800 mb-1 break-words">
                                ${feature.properties.name}
                            </h4>
                            <table class="w-full text-sm border-collapse">
                                <tbody>
                                    <tr>
                                        <td class="text-gray-600 pr-2">Luas WS</td>
                                        <td class="text-gray-600">:</td>
                                        <td class="font-semibold text-gray-800 break-words">
                                            ${feature.ws.luas ?? 0} km²
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 pr-2">Kewenangan</td>
                                        <td class="text-gray-600">:</td>
                                        <td class="font-semibold text-blue-600 break-words">
                                            ${feature.ws.kewenangan.singkatan}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `;
                }

                // Pos Klimatologi, Pos Duga Air, Pos Curah Hujan
                if (['Pos Klimatologi', 'Pos Duga Air', 'Pos Curah Hujan'].includes(feature.properties.tag)) {
                    if (!feature.data_klimatologi) {
                        console.log(feature);
                    }
                    
                    const btnDtPk = feature.data_klimatologi.length ?
                        `<tr>
                            <td colspan="3" class="p-2">
                                <button onclick='openModalData(${JSON.stringify(feature.data_klimatologi)}, {
                                    title: "Data Klimatologi - ${feature.properties.name}",
                                    columns: [
                                        { label: "Tanggal", key: "tanggal" },
                                        { label: "Kecepatan Angin", key: "kecepatan_angin" },
                                        { label: "Arah Angin", key: "arah_angin" },
                                        { label: "Kelembapan", key: "kelembapan" },
                                        { label: "Suhu", key: "suhu" },
                                        { label: "Curah Hujan", key: "curah_hujan" },
                                        { label: "Keterangan", key: "keterangan" }
                                    ]
                                })' class="w-full btn btn-primary btn-sm">
                                    Data Klimatologi
                                </button>
                            </td>
                        </tr>` : '';

                        const btnDtCh = feature.data_curah_hujan.length ?
                            `<tr>
                                <td colspan="3" class="p-2">
                                    <button onclick='openModalData(${JSON.stringify(feature.data_curah_hujan)}, {
                                        title: "Data Curah Hujan",
                                        columns: [
                                            { label: "Tanggal", key: "tanggal" },
                                            { label: "Curah Hujan", key: "curah_hujan" },
                                            { label: "Keterangan", key: "keterangan" }
                                        ]
                                    })' class="w-full btn btn-primary btn-sm">
                                        Data Curah Hujan
                                    </button>
                                </td>
                            </tr>` : '';

                        const btnDtTma = feature.data_tinggi_muka_air.length ?
                            `<tr>
                                <td colspan="3" class="p-2">
                                    <button onclick='openModalData(${JSON.stringify(feature.data_tinggi_muka_air)}, {
                                        title: "Data Tinggi Muka Air",
                                        columns: [
                                            { label: "Tanggal", key: "tanggal" },
                                            { label: "Tinggi Muka Air (cm)", key: "tinggi_muka_air" },
                                            { label: "Keterangan", key: "keterangan" }
                                        ]
                                    })' class="w-full btn btn-primary btn-sm">
                                        Data Tinggi Muka Air
                                    </button>
                                </td>
                            </tr>` : '';

                        const disableBtn = 
                            !feature.data_klimatologi.length &&
                            !feature.data_curah_hujan.length &&
                            !feature.data_tinggi_muka_air.length
                            ? '<tr class="text-center p-2"> <td colspan="3"><button class="w-full btn btn-grey btn-sm" disabled> Tidak Ada Data</button></td> </tr>' : '';
                    return `<div class="w-full space-y-3">
                            <h4 class="font-bold text-base text-gray-800 mb-1 break-words">
                                ${feature.jenis_pos} - ${feature.nama_pos}
                            </h4>
                            <table class="w-full text-sm border-collapse">
                                <tbody>
                                    <tr>
                                        <td class="text-gray-600 pr-2">Kewenangan</td>
                                        <td class="text-gray-600">:</td>
                                        <td class="font-semibold text-blue-600 break-words">
                                            ${feature.kewenangan.singkatan}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 pr-2">Koordinat</td>
                                        <td class="text-gray-600">:</td>
                                        <td class="font-semibold text-gray-800 break-words">
                                            ${decimalToDMS(feature.latitude)}<br>
                                            ${decimalToDMS(feature.longitude)}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 pr-2 align-top">Lokasi</td>
                                        <td class="text-gray-600 align-top">:</td>
                                        <td class="font-semibold text-blue-600 break-words">
                                            Kabupaten : ${feature.kabupaten.nama} <br>
                                            Kecamatan : ${feature.kecamatan.nama} <br>
                                            Desa :
                                        </td>
                                    </tr>
                                    ${btnDtPk}
                                    ${btnDtCh}
                                    ${btnDtTma}
                                    ${disableBtn}
                                </tbody>
                            </table>
                        </div>`;
                }

                if (feature.properties.tag === 'Sungai') {
                    const btnData = feature.sungai.media.length ?
                        `<tr>
                            <td colspan="3" class="p-2">
                                <button onclick='openModalMedia(${JSON.stringify(feature.sungai.media)}, {
                                    title: "Data SUngai",
                                    columns: [
                                        { label: "Tanggal", key: "tanggal" },
                                        { label: "Tinggi Muka Air (cm)", key: "tinggi_muka_air" },
                                        { label: "Keterangan", key: "keterangan" }
                                    ]
                                })' class="w-full btn btn-primary btn-sm">
                                    Lihat Data
                                </button>
                            </td>
                        </tr>`  : '<tr class="text-center p-2"> <td colspan="3"><button class="w-full btn btn-grey btn-sm" disabled> Tidak Ada Data</button></td> </tr>' ;
                    return `
                        <div class="w-full space-y-3">
                            <h4 class="font-bold text-base text-gray-800 mb-1 break-words">
                                Sungai - ${feature.properties.name}
                            </h4>
                            <table class="w-full text-sm border-collapse">
                                <tbody>
                                    <tr>
                                        <td class="text-gray-600 pr-2">Panjang SUngai</td>
                                        <td class="text-gray-600">:</td>
                                        <td class="font-semibold text-gray-800 break-words">
                                            ${feature.sungai.panjang_sungai} Km
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 pr-2">Luas DAS</td>
                                        <td class="text-gray-600">:</td>
                                        <td class="font-semibold text-blue-600 break-words">
                                            ${feature.sungai.luas_das}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 pr-2">Ordo Sungai</td>
                                        <td class="text-gray-600">:</td>
                                        <td class="font-semibold text-blue-600 break-words">
                                            ${feature.sungai.ordo}
                                        </td>
                                    </tr>
                                    ${btnData}
                                </tbody>
                            </table>
                        </div>
                    `;
                }

                if (feature.properties.tag === 'Cekungan Air Tanah') {
                    return `
                        <div class="w-full space-y-3">
                            <h4 class="font-bold text-base text-gray-800 mb-1 break-words">
                                ${feature.cat.nama_cat}
                            </h4>
                            <table class="w-full text-sm border-collapse">
                                <tbody>
                                    <tr>
                                        <td class="text-gray-600 pr-2">Luas CAT</td>
                                        <td class="text-gray-600">:</td>
                                        <td class="font-semibold text-gray-800 break-words">
                                            ${feature.cat.luas_cat_ha} ha
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 pr-2">Potensi air tanah bebas</td>
                                        <td class="text-gray-600">:</td>
                                        <td class="font-semibold text-blue-600 break-words">
                                            ${feature.cat.potensi_air_tanah_bebas}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 pr-2">Potensi air tanah tertekan</td>
                                        <td class="text-gray-600">:</td>
                                        <td class="font-semibold text-blue-600 break-words">
                                            ${feature.cat.potensi_air_tanah_tertekan}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                // Default
                return `<b class="break-words">${feature.properties.name}</b>`;
            }

        });

        function decimalToDMS(decimal, isLatitude = true) {
            const isNegative = decimal < 0;
            const absolute = Math.abs(decimal);

            const degrees = Math.floor(absolute);
            const minutesDecimal = (absolute - degrees) * 60;
            const minutes = Math.trunc(minutesDecimal);

            const secondsDecimal = (minutesDecimal - minutes) * 60;
            const seconds = Number((secondsDecimal + Number.EPSILON).toFixed(6));

            const direction = isLatitude ?
                (isNegative ? 'S' : 'N') :
                (isNegative ? 'W' : 'E');

            return `${degrees}° ${minutes}' ${seconds}" ${direction}`;
        }

        function DMSToDecimal(dms) {
            // Regex untuk memisahkan derajat, menit, detik dari string
            const regex = /^(-?\d+)° (\d+)' (\d+)"$/;
            const matches = dms.match(regex);

            if (matches) {
                const degrees = parseInt(matches[1]);
                const minutes = parseInt(matches[2]);
                const seconds = parseInt(matches[3]);

                const sign = degrees < 0 ? -1 : 1;
                const decimal = Math.abs(degrees) + minutes / 60 + seconds / 3600;
                return sign * decimal;
            } else {
                throw new Error("Format DMS tidak valid. Pastikan dalam format 'deg° min' sec\"'");
            }
        }

        function openModalData(data, fields) {
            
            const modalTitle = document.getElementById('modalTitle');
            const modalHeader = document.getElementById('modalHeader');
            const modalBody = document.getElementById('modalBody');

            // Judul modal dari fields.title
            modalTitle.textContent = fields.title || "Data";

            // Header tabel
            modalHeader.innerHTML = fields.columns.map(col => `<th class="border p-2">${col.label}</th>`).join('');

            // Isi tabel
            modalBody.innerHTML = data.map(row => `
                <tr>
                    ${fields.columns.map(col => `<td class="border p-2">${row[col.key] ?? ''}</td>`).join('')}
                </tr>
            `).join('');

            // Tampilkan modal
            document.getElementById('modalData').classList.remove('hidden');
        }
        function openModalMedia(medias){
            const modalTitle = document.getElementById('modalTitle');
            const modalHeader = document.getElementById('modalHeader');
            const modalBody = document.getElementById('modalBody');

            // Set judul modal
            modalTitle.textContent = 'Daftar Media';

            // Clear existing content
            modalHeader.innerHTML = '';
            modalBody.innerHTML = '';

            // Buat header tabel
            modalHeader.innerHTML = `
                <th class="border border-gray-300 px-4 py-2 text-left">No</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Nama File</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Ukuran</th>
                <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
            `;

            // Buat body tabel
            let tableContent = '';
            medias.forEach((media, index) => {
                const fileSizeKB = (media.size / 1024).toFixed(2);
                tableContent += `
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2">${index + 1}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <div class="flex items-center gap-2">
                                <div class="badge badge-outline badge-sm">${media.mime_type.split('/')[1].toUpperCase()}</div>
                                <span>${media.file_name}</span>
                            </div>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">${fileSizeKB} KB</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <button class="btn btn-sm btn-ghost btn-circle" onclick="viewMedia('${media.original_url}')" title="Lihat File">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;
            });

            modalBody.innerHTML = tableContent;

            // Tampilkan modal
            document.getElementById('modalData').classList.remove('hidden');
        }

        function viewMedia(url) {
            window.open(url, '_blank');
        }

        function closeModal() {
            document.getElementById('modalData').classList.add('hidden');
        }

    </script>

</body>

</html>
