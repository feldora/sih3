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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        .main-container {
            display: flex;
            height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 320px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-content {
            padding: 20px;
        }
        
        .sidebar-section {
            margin-bottom: 30px;
        }
        
        .sidebar-section h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .sidebar-item {
            padding: 12px 15px;
            margin-bottom: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }
        
        .sidebar-item.active {
            background: rgba(255, 255, 255, 0.25);
            border-left: 4px solid #fff;
        }
        
        .search-input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            placeholder-color: rgba(255, 255, 255, 0.7);
        }
        
        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
        }
        
        /* Toggle Button */
        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            display: none;
        }
        
        /* Map Container */
        .map-container {
            flex: 1;
            position: relative;
        }
        
        .overlay-container {
            position: relative;
            width: 100%;
            height: 100vh;
        }
        
        #map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .overlay-content {
            position: absolute;
            z-index: 2;
            pointer-events: none;
        }
        
        /* Posisi overlay yang sudah ada */
        .top-left {
            top: 20px;
            left: 20px;
        }
        
        .top-right {
            top: 20px;
            right: 20px;
        }
        
        .bottom-left {
            bottom: 20px;
            left: 20px;
        }
        
        .bottom-right {
            bottom: 20px;
            right: 20px;
        }
        
        .center-top {
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .control-panel {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            pointer-events: auto;
        }
        
        .search-box {
            width: 300px;
        }
        
        .map-legend {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 5px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            border: 1px solid #ccc;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh;
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .sidebar-toggle {
                display: block;
            }
            
            .map-container {
                width: 100%;
            }
        }
        
        /* Custom Scrollbar untuk Sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Loader -->
    <div id="loader" role="status" aria-live="polite" class="fixed inset-0 flex items-center justify-center bg-white z-50">
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

                <div class="sidebar-item" id="homeButton">
                    <span class="mr-2">🏠</span> SIH3 SULTENG
                </div>
                <!-- Search Section -->
                {{-- <div class="sidebar-section">
                    <h3>Cari Lokasi</h3>
                    <input type="text" class="search-input" placeholder="Cari tempat..." id="searchInput">
                </div> --}}
                
                <div class="sidebar-section">
                    <h3>Pilih Data</h3>
                    <div class="sidebar-item">
                        <input type="checkbox" id="wilayahsungai" name="wilayahsungai" class="mr-2">
                        <label for="wilayahsungai">Wulayah Sungai</label>
                    </div>
                    <div class="sidebar-item">
                        <input type="checkbox" id="pospantau" name="pospantau" class="mr-2">
                        <label for="pospantau">Pos Pemantauan</label>
                    </div>
                    <div class="sidebar-item">
                        <input type="checkbox" id="titikPantau" name="titikPantau" class="mr-2">
                        <label for="titikPantau">Titik Pantau</label>
                    </div>
                </div>
                
                <!-- Layers Section -->
                {{-- <div class="sidebar-section">
                    <h3>Map Layers</h3>
                    <div class="sidebar-item active" data-layer="street">
                        📍 Street Map
                    </div>
                    <div class="sidebar-item" data-layer="satellite">
                        🛰️ Satellite View
                    </div>
                    <div class="sidebar-item" data-layer="terrain">
                        🏔️ Terrain Map
                    </div>
                </div> --}}
                
                <!-- Tools Section -->
                {{-- <div class="sidebar-section">
                    <h3>Map Tools</h3>
                    <div class="sidebar-item" data-tool="measure">
                        📏 Measure Distance
                    </div>
                    <div class="sidebar-item" data-tool="marker">
                        📌 Add Marker
                    </div>
                    <div class="sidebar-item" data-tool="polygon">
                        🔺 Draw Polygon
                    </div>
                </div> --}}
                
                <!-- Filters Section -->
                {{-- <div class="sidebar-section">
                    <h3>Data Filters</h3>
                    <div class="sidebar-item" data-filter="all">
                        🔍 Show All
                    </div>
                    <div class="sidebar-item" data-filter="schools">
                        🏫 Schools
                    </div>
                    <div class="sidebar-item" data-filter="hospitals">
                        🏥 Hospitals
                    </div>
                    <div class="sidebar-item" data-filter="parks">
                        🌳 Parks
                    </div>
                </div> --}}
                
                <!-- Info Section -->
                <div class="sidebar-section">
                    <h3>Map Information</h3>
                    <div style="background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 8px;">
                        <div style="margin-bottom: 8px;">
                            <strong>Coordinates:</strong><br>
                            <span id="currentCoords">-0.8917, 119.8707</span>
                        </div>
                        <div>
                            <strong>Zoom Level:</strong><br>
                            <span id="currentZoom">8</span>
                        </div>
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
        
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('map').setView([-0.8917, 119.8707], 8);

            // Default tile layer
            let currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Sidebar toggle functionality
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
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
                    
                    // Remove active class from all layer items
                    layerItems.forEach(li => li.classList.remove('active'));
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Switch map layer
                    map.removeLayer(currentLayer);
                    currentLayer = layers[layerType];
                    currentLayer.addTo(map);
                });
            });

            // Update coordinates and zoom level
            function updateMapInfo() {
                const center = map.getCenter();
                const zoom = map.getZoom();
                
                document.getElementById('currentCoords').textContent = 
                    `${center.lat.toFixed(4)}, ${center.lng.toFixed(4)}`;
                document.getElementById('currentZoom').textContent = zoom;
            }

            // Listen for map events
            map.on('moveend zoomend', updateMapInfo);

            // Tool functionality (basic examples)
            const toolItems = document.querySelectorAll('[data-tool]');
            toolItems.forEach(item => {
                item.addEventListener('click', function() {
                    const tool = this.getAttribute('data-tool');
                    
                    // Remove active from all tools
                    toolItems.forEach(ti => ti.classList.remove('active'));
                    this.classList.add('active');
                    
                    switch(tool) {
                        case 'marker':
                            map.once('click', function(e) {
                                L.marker([e.latlng.lat, e.latlng.lng]).addTo(map)
                                    .bindPopup('Custom Marker')
                                    .openPopup();
                            });
                            break;
                        case 'measure':
                            alert('Measure tool activated. Click on map to start measuring.');
                            break;
                        case 'polygon':
                            alert('Polygon tool activated. Click on map to draw polygon.');
                            break;
                    }
                });
            });

            // Filter functionality
            const filterItems = document.querySelectorAll('[data-filter]');
            filterItems.forEach(item => {
                item.addEventListener('click', function() {
                    filterItems.forEach(fi => fi.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    console.log('Filter applied:', filter);
                    // Implement your filtering logic here
                });
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const query = this.value;
                    if (query) {
                        // Simple search implementation - you can integrate with a geocoding service
                        console.log('Searching for:', query);
                        alert(`Searching for: ${query}\nIntegrate with geocoding service for real functionality.`);
                    }
                }
            });
        });

        // Home button functionality
        document.getElementById('homeButton').addEventListener('click', function() {
            location.href = '/';
        });
    </script>
</body>

</html>