import { decimalToDMS, DMSToDecimal } from '../utiliti.js';
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

document.addEventListener('DOMContentLoaded', function () {
    const loader = document.getElementById('loader');
    if (loader) loader.style.display = 'none';
    if (document.getElementById('map')) initMap();
});

function initMap() {
    const map = L.map('map', { zoomControl: false }).setView([-0.8917, 119.8707], 8);
    L.control.zoom({ position: 'topright' }).addTo(map);

    let wsLayer, posLayer, tpLayer, allFeatureLayer;
    const allFeaturesCheckbox = document.getElementById('all_features');
    let currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
        document.addEventListener('click', e => {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }

    function showMarkerInfo(html) {
        const infoDiv = document.getElementById('markerInfo');
        const footerMarkInfo = document.getElementById('footerMarkInfo');
        if (infoDiv) infoDiv.innerHTML = html;
        if (footerMarkInfo) footerMarkInfo.innerHTML = html;
    }

    // Semua Fitur checkbox
    if (allFeaturesCheckbox) {
        allFeaturesCheckbox.addEventListener('change', async e => {
            if (e.target.checked) {
                if (allFeatureLayer) map.removeLayer(allFeatureLayer);
                allFeatureLayer = L.layerGroup();

                let data;
                try {
                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams();
                    for (const pair of formData) {
                        if (pair[1]) {
                            params.append(pair[0], pair[1]);
                        }
                    }
                    const res = await fetch(`/api/geo-features?${params.toString()}`);
                    const json = await res.json();
                    data = json.features || [];

                    // Fit map to bounds of the new data
                    if (data.length > 0) {
                        const tempLayer = L.geoJSON(data);
                        map.fitBounds(tempLayer.getBounds());
                    }

                } catch (err) {
                    console.error('Gagal memuat geo features:', err);
                    return;
                }

                data.forEach(feature => {
                    if (!feature || !feature.geometry) return;

                    const layer = L.geoJSON(feature, {
                        onEachFeature: (feature, layer) => {
                            const name = feature.properties?.name || 'Fitur';
                            const popup = `<b>${name}</b><br>${
                                Object.entries(feature.properties?.properties || {})
                                      .map(([k, v]) => `<b>${k}</b>: ${v}`)
                                      .join('<br>')
                            }`;
                            layer.bindPopup(popup);
                            layer.on('click', () => showMarkerInfo(popup));
                        },
                        pointToLayer: (feature, latlng) => L.marker(latlng, { icon: posIcon }),
                        style: { color: '#28a745', weight: 1, fillOpacity: 0.3 }
                    });

                    layer.addTo(allFeatureLayer);
                });

                allFeatureLayer.addTo(map);
            } else {
                if (allFeatureLayer) map.removeLayer(allFeatureLayer);
            }
        });
    }

    // Wilayah Sungai
    const wsCheckbox = document.getElementById('wilayahsungai');
    if (wsCheckbox) {
        wsCheckbox.addEventListener('change', async e => {
            if (e.target.checked) {
                let data;
                try {
                    const res = await fetch('/api/wilayah-sungai');
                    data = await res.json();
                } catch (err) {
                    console.error('Gagal memuat data wilayah sungai:', err);
                    return;
                }
                if (wsLayer) map.removeLayer(wsLayer);
                wsLayer = L.layerGroup();

                data.forEach(item => {
                    if (item.geojson?.features) {
                        let geojson = JSON.parse(JSON.stringify(item.geojson));
                        geojson.features.forEach(f => {
                            if (f.geometry?.type === 'Polygon') {
                                const ring = f.geometry.coordinates.map(c => [parseFloat(c[0]), parseFloat(c[1])]);
                                f.geometry.coordinates = ring.length > 2 ? [ring] : [];
                            }
                        });
                        L.geoJSON(geojson, {
                            style: { color: '#0074D9', weight: 1, fillOpacity: 0.2 },
                            onEachFeature: (feature, layer) => {
                                layer.bindPopup(`<b>${feature.properties?.name || item.name}</b><br>${feature.properties?.description || item.description || ''}`);
                                layer.on('click', () => {
                                    const p = feature.properties;
                                    const html = `<b>${p?.name || item.name}</b><br>` +
                                        (p?.luas_area ? `Luas Area: ${p.luas_area}<br>` : '') +
                                        (p?.keliling_area ? `Keliling: ${p.keliling_area}<br>` : '') +
                                        (p?.description || item.description || '');
                                    showMarkerInfo(html);
                                });
                            }
                        }).addTo(wsLayer);
                    }
                });
                wsLayer.addTo(map);
            } else if (wsLayer) map.removeLayer(wsLayer);
        });
    }

    // Pos Pantau
    document.getElementById('pospantau').addEventListener('change', async e => {
        if (e.target.checked) {
            let data;
            try {
                const res = await fetch('/api/pos-pantau');
                data = await res.json();
            } catch (err) {
                console.error('Gagal memuat data pos pantau:', err);
                return;
            }
            if (posLayer) map.removeLayer(posLayer);
            posLayer = L.layerGroup();

            data.forEach(item => {
                if (item.latitude && item.longitude) {
                    L.marker([item.latitude, item.longitude], { icon: posIcon })
                        .bindPopup(`<b>${item.nama_pos || 'Pos Pantau'}</b><br>${item.alamat || ''}`)
                        .on('click', () => showMarkerInfo(`<b>${item.nama_pos || 'Pos Pantau'}</b><br>${item.alamat || ''}`))
                        .addTo(posLayer);
                }
            });
            posLayer.addTo(map);
        } else if (posLayer) map.removeLayer(posLayer);
    });

    // Titik Pantau
    document.getElementById('titikPantau').addEventListener('change', async e => {
        if (e.target.checked) {
            let data;
            try {
                const res = await fetch('/api/titik-pantau');
                data = await res.json();
            } catch (err) {
                console.error('Gagal memuat data titik pantau:', err);
                return;
            }
            if (tpLayer) map.removeLayer(tpLayer);
            tpLayer = L.layerGroup();

            data.forEach(item => {
                if (item.latitude && item.longitude) {
                    L.marker([item.latitude, item.longitude], { icon: titikPantauIcon })
                        .bindPopup(`<b>${item.nama_titik || 'Titik Pantau'}</b><br>${item.alamat || ''}`)
                        .on('click', () => showMarkerInfo(`<b>${item.nama_titik || 'Titik Pantau'}</b><br>${item.alamat || ''}`))
                        .addTo(tpLayer);
                }
            });
            tpLayer.addTo(map);
        } else if (tpLayer) map.removeLayer(tpLayer);
    });

    // Layer switching controls
    const layerItems = document.querySelectorAll('[data-layer]');
    const layers = {
        street: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap contributors' }),
        satellite: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', { attribution: '© OpenTopoMap contributors' }),
        terrain: L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap contributors' })
    };
    layerItems.forEach(item => {
        item.addEventListener('click', function () {
            layerItems.forEach(li => li.classList.remove('active'));
            this.classList.add('active');
            map.removeLayer(currentLayer);
            currentLayer = layers[this.getAttribute('data-layer')];
            currentLayer.addTo(map);
        });
    });

    // Update map info (coords & zoom)
    map.on('moveend zoomend', () => {
        const center = map.getCenter();
        const zoom = map.getZoom();
        const coordsEl = document.getElementById('currentCoords');
        const zoomEl = document.getElementById('currentZoom');
        if (coordsEl) coordsEl.textContent = `${decimalToDMS(center.lat)}, ${decimalToDMS(center.lng)} || ${center.lat.toFixed(5)}, ${center.lng.toFixed(5)}`;
        if (zoomEl) zoomEl.textContent = zoom;
    });

    // Home and clear-cache buttons
    document.getElementById('homeButton')?.addEventListener('click', () => location.href = '/');
    const clearCacheBtn = document.getElementById('clearCacheBtn');
    if(clearCacheBtn) clearCacheBtn.style.display = 'none';

    // Search functionality (unchanged)
    const searchInput = document.getElementById('searchInput');
    const searchResultsSection = document.getElementById('searchResultsSection');
    const searchResults = document.getElementById('searchResults');


    function renderSearchResults(results) {
        if (!searchResults) return;
        searchResults.innerHTML = '';
        if (!results.length) {
            searchResults.innerHTML = '<li class="text-gray-400">Tidak ada hasil</li>';
            return;
        }
        results.forEach(item => {
            const li = document.createElement('li');
            li.className = 'py-1 px-2 hover:bg-white hover:bg-opacity-50 hover:text-gray-800 rounded cursor-pointer';
            li.innerHTML = `<span class='text-sm'>${item.name}</span> <span class='text-xs text-gray-300'>(${item.type})</span>`;
            li.addEventListener('click', () => {
                if (item.latitude && item.longitude) {
                    map.setView([item.latitude, item.longitude], 14);
                    const tempIcon = item.type === 'Titik Pantau' ? titikPantauIcon : posIcon;
                    const tempMarker = L.marker([item.latitude, item.longitude], { icon: tempIcon }).addTo(map);
                    tempMarker.bindPopup(`<b>${item.name}</b><br>${item.description || ''}`).openPopup();
                    showMarkerInfo(`<b>${item.name}</b><br>${item.description || ''}`);
                    tempMarker.on('popupclose', () => map.removeLayer(tempMarker));
                } else if (item.geojson) {
                    try {
                        const geo = item.geojson; // Already an object
                        let layer;

                        if (geo.type === 'Feature') {
                            layer = L.geoJSON(geo);
                        } else if (geo.type === 'FeatureCollection') {
                            layer = L.geoJSON(geo);
                        }

                        if (layer) {
                            layer.addTo(map);
                            const bounds = layer.getBounds();
                            if (bounds.isValid()) {
                                map.fitBounds(bounds);
                            }
                            
                            let descriptionHtml = '';
                            try {
                                const properties = JSON.parse(item.description);
                                descriptionHtml = Object.entries(properties)
                                    .map(([key, value]) => `<b>${key}</b>: ${value}`)
                                    .join('<br>');
                            } catch (e) {
                                descriptionHtml = item.description || '';
                            }

                            const popupContent = `<b>${item.name}</b><br>${descriptionHtml}`;
                            
                            // Find a representative point for the popup
                            let popupLatLng = bounds.getCenter();
                            if (geo.type === 'Feature' && geo.geometry.type === 'Point') {
                                popupLatLng = [geo.geometry.coordinates[1], geo.geometry.coordinates[0]];
                            }

                            L.popup()
                                .setLatLng(popupLatLng)
                                .setContent(popupContent)
                                .openOn(map);
                            showMarkerInfo(popupContent);

                            // Remove the layer when the popup is closed
                            layer.on('popupclose', () => map.removeLayer(layer));
                        }
                    } catch (e) {
                        console.error("Error processing geojson:", e);
                    }
                }
            });
            searchResults.appendChild(li);
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', async () => {
            const kw = searchInput.value.trim();
            if (kw.length < 3) {
                searchResultsSection.classList.add('hidden');
                return;
            }

            try {
                const response = await fetch(`/api/geospasial/search?keyword=${kw}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const results = await response.json();
                renderSearchResults(results);
                searchResultsSection.classList.remove('hidden');
            } catch (error) {
                console.error('Search error:', error);
                if (searchResults) searchResults.innerHTML = '<li class="text-gray-400">Gagal melakukan pencarian.</li>';
                searchResultsSection.classList.remove('hidden');
            }
        });
    }

    // Filter functionality
    const filterForm = document.getElementById('filterForm');
    const provinsiSelect = document.getElementById('provinsi');
    const kabupatenSelect = document.getElementById('kabupaten');
    const kecamatanSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');

    async function populateProvinsi() {
        provinsiSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
        try {
            const response = await fetch('/api/geo-features/provinsi');
            const provinsis = await response.json();
            provinsis.forEach(prov => {
                const option = document.createElement('option');
                option.value = prov.id;
                option.textContent = prov.nama;
                provinsiSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error fetching provinsi:', error);
        }
    }

    async function populateKabupaten(provinsi_id) {
        kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
        kabupatenSelect.disabled = true;
        kecamatanSelect.disabled = true;
        desaSelect.disabled = true;
        if (!provinsi_id) return;

        try {
            const response = await fetch(`/api/geo-features/kabupaten?provinsi_id=${provinsi_id}`);
            const kabupatens = await response.json();
            kabupatens.forEach(kab => {
                const option = document.createElement('option');
                option.value = kab.id;
                option.textContent = kab.nama;
                kabupatenSelect.appendChild(option);
            });
            kabupatenSelect.disabled = false;
        } catch (error) {
            console.error('Error fetching kabupaten:', error);
        }
    }

    async function populateKecamatan(kabupaten_id) {
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
        kecamatanSelect.disabled = true;
        desaSelect.disabled = true;
        if (!kabupaten_id) return;

        try {
            const response = await fetch(`/api/geo-features/kecamatan?kabupaten_id=${kabupaten_id}`);
            const kecamatans = await response.json();
            kecamatans.forEach(kec => {
                const option = document.createElement('option');
                option.value = kec.id;
                option.textContent = kec.nama;
                kecamatanSelect.appendChild(option);
            });
            kecamatanSelect.disabled = false;
        } catch (error) {
            console.error('Error fetching kecamatan:', error);
        }
    }

    async function populateDesa(kecamatan_id) {
        desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
        desaSelect.disabled = true;
        if (!kecamatan_id) return;

        try {
            const response = await fetch(`/api/geo-features/desa?kecamatan_id=${kecamatan_id}`);
            const desas = await response.json();
            desas.forEach(desa => {
                const option = document.createElement('option');
                option.value = desa.kode;
                option.textContent = desa.nama;
                desaSelect.appendChild(option);
            });
            desaSelect.disabled = false;
        } catch (error) {
            console.error('Error fetching desa:', error);
        }
    }

    if (provinsiSelect) {
        populateProvinsi();
        provinsiSelect.addEventListener('change', (e) => {
            populateKabupaten(e.target.value);
        });
    }

    if (kabupatenSelect) {
        kabupatenSelect.addEventListener('change', (e) => {
            populateKecamatan(e.target.value);
        });
    }

    if (kecamatanSelect) {
        kecamatanSelect.addEventListener('change', (e) => {
            populateDesa(e.target.value);
        });
    }

    async function loadFilteredFeatures() {
        if (allFeatureLayer) {
            map.removeLayer(allFeatureLayer);
        }
        allFeatureLayer = L.layerGroup();

        let data;
        try {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams();
            // Only add 'desa' to params, as it's the only one used for filtering
            // const desaValue = formData.get('desa');
            // if (desaValue) {
            //     params.append('desa', desaValue);
            // }
            const provinsi = formData.get('provinsi');
            const kabupaten = formData.get('kabupaten');
            const kecamatan = formData.get('kecamatan');
            const desa = formData.get('desa');

            if (provinsi) params.append('provinsi', provinsi);
            if (kabupaten) params.append('kabupaten', kabupaten);
            if (kecamatan) params.append('kecamatan', kecamatan);
            if (desa) params.append('desa', desa);


            const res = await fetch(`/api/geo-features/filter?${params.toString()}`);
            const json = await res.json();
            data = json.features || [];

            // Fit map to bounds of the new data
            if (data.length > 0) {
                const tempLayer = L.geoJSON(data);
                map.fitBounds(tempLayer.getBounds());
            }

        } catch (err) {
            console.error('Gagal memuat geo features:', err);
            return;
        }

        data.forEach(feature => {
            if (!feature || !feature.geometry) return;

            const layer = L.geoJSON(feature, {
                onEachFeature: (feature, layer) => {
                    const name = feature.properties?.name || 'Fitur';
                    const popup = `<b>${name}</b><br>${
                        Object.entries(feature.properties?.properties || {})
                              .map(([k, v]) => `<b>${k}</b>: ${v}`)
                              .join('<br>')
                    }`;
                    layer.bindPopup(popup);
                    layer.on('click', () => showMarkerInfo(popup));
                },
                pointToLayer: (feature, latlng) => L.marker(latlng, { icon: posIcon }),
                style: { color: '#28a745', weight: 1, fillOpacity: 0.3 }
            });

            layer.addTo(allFeatureLayer);
        });

        allFeatureLayer.addTo(map);
    }

    if (filterForm) {
        filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            loadFilteredFeatures();
        });
    }
}
