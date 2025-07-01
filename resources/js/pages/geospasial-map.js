import { setWithExpiry, getWithExpiry } from './geospasial-utils';
import L from 'leaflet';

// Custom marker icons
const posIcon = L.icon({
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    iconSize: [13, 21],
    iconAnchor: [6, 21],
    popupAnchor: [1, -17],
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    shadowSize: [21, 21]
});
const titikPantauIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    iconSize: [13, 21],
    iconAnchor: [6, 21],
    popupAnchor: [1, -17],
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    shadowSize: [21, 21]
});

document.addEventListener('DOMContentLoaded', function() {
    // Loader
    const loader = document.getElementById('loader');
    if (loader) loader.style.display = 'none';
    if (document.getElementById('map')) {
        initMap();
    }
});

function initMap() {
    // Map init
    const map = L.map('map', { zoomControl: false }).setView([-0.8917, 119.8707], 8);
    // Tambahkan zoom control di kanan atas
    L.control.zoom({ position: 'topright' }).addTo(map);
    let wsLayer, posLayer, tpLayer;
    let currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }
    
    // Helper untuk menampilkan info ke sidebar
    function showMarkerInfo(html) {
        const infoDiv = document.getElementById('markerInfo');
        const footerMarkInfo = document.getElementById('footerMarkInfo');
    
        if (infoDiv) infoDiv.innerHTML = html;
        if (footerMarkInfo) footerMarkInfo.innerHTML = html;
    }
    
    // Wilayah Sungai
    const wsCheckbox = document.getElementById('wilayahsungai');
    if (wsCheckbox) {
        wsCheckbox.addEventListener('change', async function(e) {
            if (e.target.checked) {
                let data = getWithExpiry('ws_data');
                if (!data) {
                    const res = await fetch('/api/wilayah-sungai');
                    data = await res.json();
                    setWithExpiry('ws_data', data, 24 * 60 * 60 * 1000);
                }
                if (wsLayer) map.removeLayer(wsLayer);
                wsLayer = L.layerGroup();
                
                data.forEach(item => {
                    
                    if (item.geojson && item.geojson.features) {
                        // Clone geojson agar tidak mengubah data asli
                        let geojson = JSON.parse(JSON.stringify(item.geojson));
                        geojson.features.forEach(f => {
                            if (f.geometry && f.geometry.type === 'Polygon') {
                                // Konversi array koordinat string [lng, lat] ke number [lng, lat] (urutan TIDAK dibalik!) dan bungkus ke array ring
                                const ring = f.geometry.coordinates.map(coord => [
                                    parseFloat(coord[0]), // lng
                                    parseFloat(coord[1])  // lat
                                ]);
                                // Pastikan minimal 3 titik
                                f.geometry.coordinates = ring.length > 2 ? [ring] : [];
                            }
                        });
                        L.geoJSON(geojson, {
                            style: { color: '#0074D9', weight: 1, fillOpacity: 0.2 },
                            onEachFeature: function (feature, layer) {
                                layer.bindPopup(
                                    `<b>${feature.properties?.name || item.name}</b><br>${feature.properties?.description || item.description || ''}`
                                );
                                layer.on('click', function(e) {
                                    const infoHtml =
                                        `<b>${feature.properties?.name || item.name}</b><br>` +
                                        (feature.properties?.luas_area ? `Luas Area: ${feature.properties.luas_area}<br>` : '') +
                                        (feature.properties?.keliling_area ? `Keliling: ${feature.properties.keliling_area}<br>` : '') +
                                        `${feature.properties?.description || item.description || ''}`;
                                    
                                    showMarkerInfo(infoHtml);
                                });
                            }
                        }).addTo(wsLayer);
                    }
                });
                wsLayer.addTo(map);
            } else {
                if (wsLayer) map.removeLayer(wsLayer);
            }
        });
    }
    
    // Pos Pantau
    document.getElementById('pospantau').addEventListener('change', async function(e) {
        if (e.target.checked) {
            let data = getWithExpiry('pos_data');
            if (!data) {
                const res = await fetch('/api/pos-pantau');
                data = await res.json();
                setWithExpiry('pos_data', data, 24 * 60 * 60 * 1000);
            }
            if (posLayer) map.removeLayer(posLayer);
            posLayer = L.layerGroup();
            data.forEach(item => {
                if (item.latitude && item.longitude) {
                    L.marker([item.latitude, item.longitude], { icon: posIcon })
                        .bindPopup(`<b>${item.nama_pos || 'Pos Pantau'}</b><br>${item.alamat || ''}`)
                        .on('click', function(e) {
                            showMarkerInfo(`<b>${item.nama_pos || 'Pos Pantau'}</b><br>${item.alamat || ''}`);
                        })
                        .addTo(posLayer);
                }
            });
            posLayer.addTo(map);
        } else {
            if (posLayer) map.removeLayer(posLayer);
        }
    });
    
    // Titik Pantau
    document.getElementById('titikPantau').addEventListener('change', async function(e) {
        if (e.target.checked) {
            let data = getWithExpiry('tp_data');
            if (!data) {
                const res = await fetch('/api/titik-pantau');
                data = await res.json();
                setWithExpiry('tp_data', data, 24 * 60 * 60 * 1000);
            }
            if (tpLayer) map.removeLayer(tpLayer);
            tpLayer = L.layerGroup();
            data.forEach(item => {
                if (item.latitude && item.longitude) {
                    L.marker([item.latitude, item.longitude], { icon: titikPantauIcon })
                        .bindPopup(`<b>${item.nama_titik || 'Titik Pantau'}</b><br>${item.alamat || ''}`)
                        .on('click', function(e) {
                            showMarkerInfo(`<b>${item.nama_titik || 'Titik Pantau'}</b><br>${item.alamat || ''}`);
                        })
                        .addTo(tpLayer);
                }
            });
            tpLayer.addTo(map);
        } else {
            if (tpLayer) map.removeLayer(tpLayer);
        }
    });
    
    // Layer switching
    const layerItems = document.querySelectorAll('[data-layer]');
    const layers = {
        street: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }),
        satellite: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenTopoMap contributors'
        }),
        terrain: L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        })
    };
    layerItems.forEach(item => {
        item.addEventListener('click', function() {
            const layerType = this.getAttribute('data-layer');
            layerItems.forEach(li => li.classList.remove('active'));
            this.classList.add('active');
            map.removeLayer(currentLayer);
            currentLayer = layers[layerType];
            currentLayer.addTo(map);
        });
    });
    
    // Map info
    function updateMapInfo() {
        const center = map.getCenter();
        const zoom = map.getZoom();
        const coords = document.getElementById('currentCoords');
        const zoomEl = document.getElementById('currentZoom');
        if (coords) coords.textContent = `${center.lat.toFixed(4)}, ${center.lng.toFixed(4)}`;
        if (zoomEl) zoomEl.textContent = zoom;
    }
    map.on('moveend zoomend', updateMapInfo);
    
    // Home button
    const homeBtn = document.getElementById('homeButton');
    if (homeBtn) homeBtn.addEventListener('click', function() { location.href = '/'; });
    
    // Clear cache
    const clearCacheBtn = document.getElementById('clearCacheBtn');
    if (clearCacheBtn) {
        clearCacheBtn.addEventListener('click', function() {
            localStorage.removeItem('ws_data');
            localStorage.removeItem('pos_data');
            localStorage.removeItem('tp_data');
            alert('Cache data peta berhasil dibersihkan!');
        });
    }
    
    // --- SEARCH FUNCTIONALITY ---
    const searchInput = document.getElementById('searchInput');
    const searchResultsSection = document.getElementById('searchResultsSection');
    const searchResults = document.getElementById('searchResults');
    
    function getAllSearchData() {
        const ws = getWithExpiry('ws_data') || [];
        const pos = getWithExpiry('pos_data') || [];
        const tp = getWithExpiry('tp_data') || [];
        // Gabungkan dan beri type
        const wsList = ws.map(item => ({
            type: 'Wilayah Sungai',
            name: item.name,
            description: item.description,
            id: item.id,
            geojson: item.geojson
        }));
        const posList = pos.map(item => ({
            type: 'Pos Pantau',
            name: item.nama_pos,
            description: item.alamat,
            id: item.id,
            latitude: item.latitude,
            longitude: item.longitude
        }));
        const tpList = tp.map(item => ({
            type: 'Titik Pantau',
            name: item.nama_titik,
            description: item.alamat,
            id: item.id,
            latitude: item.latitude,
            longitude: item.longitude
        }));
        return [...wsList, ...posList, ...tpList];
    }
    
    function renderSearchResults(results) {
        if (!searchResults) return;
        searchResults.innerHTML = '';
        if (results.length === 0) {
            searchResults.innerHTML = '<li class="text-gray-400">Tidak ada hasil</li>';
            return;
        }
        results.forEach(item => {
            const li = document.createElement('li');
            li.className = 'py-1 px-2 hover:bg-white hover:bg-opacity-50 hover:text-gray-800 rounded cursor-pointer';
            li.innerHTML = `<span class='text-sm'>${item.name}</span> <span class='text-xs text-gray-300'>(${item.type})</span><br><span class='text-xs text-gray-200 hidden'>${item.description || ''}</span>`;
            li.addEventListener('click', function() {
                // Zoom ke lokasi jika ada koordinat
                if (item.latitude && item.longitude) {
                    map.setView([item.latitude, item.longitude], 14);
                    const tempIcon = item.type === 'Titik Pantau' ? titikPantauIcon : posIcon;
                    const tempMarker = L.marker([item.latitude, item.longitude], { icon: tempIcon }).addTo(map);
                    tempMarker.bindPopup(`<b>${item.name}</b><br>${item.description || ''}`).openPopup();
                    showMarkerInfo(`<b>${item.name}</b><br>${item.description || ''}`);
                    tempMarker.on('popupclose', function() {
                        map.removeLayer(tempMarker);
                    });
                } else if (item.geojson) {
                    try {
                        const geo = JSON.parse(item.geojson);
                        if (geo.features && geo.features[0] && geo.features[0].geometry && geo.features[0].geometry.coordinates) {
                            const coords = geo.features[0].geometry.coordinates[0];
                            if (coords && coords.length > 0) {
                                map.setView([coords[0][0], coords[0][1]], 12);
                                L.popup()
                                    .setLatLng([coords[0][0], coords[0][1]])
                                    .setContent(`<b>${item.name}</b><br>${item.description || ''}`)
                                    .openOn(map);
                                showMarkerInfo(`<b>${item.name}</b><br>${item.description || ''}`);
                            }
                        }
                    } catch (e) {}
                }
            });
            searchResults.appendChild(li);
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const keyword = e.target.value.trim().toLowerCase();
            if (keyword.length === 0) {
                searchResultsSection.classList.add('hidden');
                return;
            }
            const allData = getAllSearchData();
            const filtered = allData.filter(item =>
                (item.name && item.name.toLowerCase().includes(keyword)) ||
                (item.description && item.description.toLowerCase().includes(keyword))
            );
            renderSearchResults(filtered);
            searchResultsSection.classList.remove('hidden');
        });
    }

}