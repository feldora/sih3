// Import UniversalMapManager
import '../components/UniversalMapManager.js';

// Keep track of initialization status with module scope
let isMapInitializing = false;
let initializationPromise = null;

// Create initialization function
async function initializeMap() {
    if (isMapInitializing || initializationPromise) {
        console.debug('Map initialization already in progress...');
        return initializationPromise;
    }

    const loader = document.getElementById('loader');
    const hideLoader = () => loader?.classList.add('hidden');
    const showLoader = () => loader?.classList.remove('hidden');

    showLoader();
    isMapInitializing = true;

    initializationPromise = (async () => {
        try {
            // Cleanup existing instance
            if (window.currentMapManager) {
                console.debug('Cleaning up existing map instance...');
                window.currentMapManager.destroy();
                window.currentMapManager = null;
            }

            window.currentMapManager = new UniversalMapManager({
                mapId: 'map',
                center: [-0.8917, 119.8707],
                zoom: 8,
                endpoints: {
                    wilayahsungai: '/api/wilayah-sungai',
                    pospantau: '/api/pos-pantau',
                    titikpantau: '/api/titik-pantau'
                },
                icons: {
                    pospantau: {
                        iconUrl: '/images/icons/pos-pantau.png',
                        iconSize: [32, 32],
                        iconAnchor: [16, 32]
                    },
                    titikpantau: {
                        iconUrl: '/images/icons/titik-pantau.png', 
                        iconSize: [24, 24],
                        iconAnchor: [12, 24]
                    }
                },
                callbacks: {
                    onInit: function(manager) {
                        console.debug('Map initialization complete');
                        hideLoader();
                        setupCheckboxHandlers(manager);
                        setupSearchHandlers(manager);
                        setupMapEventHandlers(manager);
                    },
                    onError: function(context, error) {
                        console.error('Map initialization failed:', error);
                        hideLoader();
                        isMapInitializing = false;
                        initializationPromise = null;
                    }
                }
            });

            // Register layers after initialization
            await window.currentMapManager
                .registerLayer('wilayahsungai', {
                    type: 'geojson',
                    endpoint: '/api/wilayah-sungai',
                    style: { color: '#3388ff', weight: 2, fillOpacity: 0.1 },
                    popup: (feature) => `<b>${feature.properties.nama}</b><br>Wilayah: ${feature.properties.wilayah}`
                })
                .registerLayer('pospantau', {
                    type: 'marker',
                    endpoint: '/api/pos-pantau',
                    icon: 'pospantau',
                    popup: (data) => `<b>${data.nama}</b><br>Status: ${data.status}`
                })
                .registerLayer('titikpantau', {
                    type: 'marker', 
                    endpoint: '/api/titik-pantau',
                    icon: 'titikpantau',
                    popup: (data) => `<b>${data.nama}</b>`
                });

        } catch (error) {
            console.error('Map initialization error:', error);
            hideLoader();
            throw error;
        } finally {
            isMapInitializing = false;
        }
    })();

    return initializationPromise;
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    initializeMap().catch(error => {
        console.error('Failed to initialize map:', error);
    });
});

// Handle cleanup on hot module replacement
if (import.meta.hot) {
    import.meta.hot.dispose(() => {
        if (window.currentMapManager) {
            console.debug('Hot module replacement: cleaning up map...');
            window.currentMapManager.destroy();
            window.currentMapManager = null;
        }
        isMapInitializing = false;
        initializationPromise = null;
    });
}

// Clean up on page unload
window.addEventListener('unload', function() {
    if (window.currentMapManager) {
        window.currentMapManager.destroy();
        window.currentMapManager = null;
    }
    isInitialized = false;
});

// Fungsi untuk menghubungkan dengan UI yang sudah ada
function setupCheckboxHandlers(mapManager) {
    // All features checkbox
    document.getElementById('all_features').addEventListener('change', function() {
        const checked = this.checked;
        ['wilayahsungai', 'pospantau', 'titikPantau'].forEach(layerName => {
            document.getElementById(layerName).checked = checked;
            mapManager.toggleLayer(layerName, checked);
        });
    });
    
    // Individual layer checkboxes
    ['wilayahsungai', 'pospantau', 'titikPantau'].forEach(layerName => {
        document.getElementById(layerName).addEventListener('change', function() {
            mapManager.toggleLayer(layerName, this.checked);
        });
    });
}

function setupSearchHandlers(mapManager) {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const searchResultsSection = document.getElementById('searchResultsSection');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', async function(e) {
            const keyword = e.target.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Hide results if empty keyword
            if (!keyword) {
                searchResultsSection.classList.add('hidden');
                return;
            }

            // Debounce search requests
            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/geospasial/search?keyword=${encodeURIComponent(keyword)}`);
                    if (!response.ok) throw new Error('Search failed');
                    
                    const results = await response.json();
                    
                    // Show results section
                    searchResultsSection.classList.remove('hidden');
                    
                    // Clear previous results
                    searchResults.innerHTML = '';
                    
                    // Display results
                    if (results.length > 0) {
                        results.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'p-1 hover:bg-blue-100 cursor-pointer text-xs';
                            li.textContent = item.name;
                            li.addEventListener('click', () => {
                                // Pan to location if coordinates available
                                if (item.type === "Geo Feature" && item.geojson && item.geojson.geometry) {
                                    // Create GeoJSON layer and add to map
                                    const geoLayer = L.geoJSON(item.geojson, {
                                        style: { 
                                            color: '#ff0000',
                                            weight: 2,
                                            fillOpacity: 0.2
                                        }
                                    });
                                    
                                    // Fit map to geometry bounds
                                    mapManager.getMap().fitBounds(geoLayer.getBounds());
                                    
                                    // Add layer to map temporarily
                                    geoLayer.addTo(mapManager.getMap());
                                    
                                    // Remove layer after 3 seconds
                                    setTimeout(() => {
                                        mapManager.getMap().removeLayer(geoLayer);
                                    }, 3000);
                                }
                                searchInput.value = item.name;
                                searchResultsSection.classList.add('hidden');
                            });
                            searchResults.appendChild(li);
                        });
                    } else {
                        searchResults.innerHTML = '<li class="p-1 text-xs text-gray-500">Tidak ada hasil</li>';
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<li class="p-1 text-xs text-red-500">Error mencari data</li>';
                }
            }, 300); // Delay 300ms before sending request
        });

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchResultsSection.contains(e.target) && !searchInput.contains(e.target)) {
                searchResultsSection.classList.add('hidden');
            }
        });
    }
}

function setupMapEventHandlers(mapManager) {
    // Update coordinates display
    mapManager.on('mapUpdate', function(data) {
        document.getElementById('currentCoords').textContent = 
            `${data.center.lat.toFixed(4)}, ${data.center.lng.toFixed(4)}`;
        document.getElementById('currentZoom').textContent = data.zoom;
    });
}