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
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/pages/geospasial-map.css', 'resources/js/pages/geospasial-map.js'])

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
                <div class="sidebar-section">
                    <h3>Cari</h3>
                    <input type="text" class="search-input px-2 py-1 text-xs rounded w-32" placeholder="Cari tempat..." id="searchInput">

                    <div class="sidebar-section hidden" id="searchResultsSection">
                        <small>Hasil pencarian</small>
                        <div class="bg-white bg-opacity-10 p-4 rounded-lg">
                            <ul id="searchResults" class="list-none p-0 m-0">
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="sidebar-section">
                    <h3 class="text-sm">Pilih Data</h3>
                    <div class="sidebar-item flex items-center py-1">
                        <input type="checkbox" id="wilayahsungai" name="wilayahsungai"
                            class="mr-2 w-3 h-3 align-middle">
                        <label for="wilayahsungai" class="text-xs align-middle">Wilayah Sungai</label>
                    </div>
                    <div class="sidebar-item flex items-center py-1">
                        <input type="checkbox" id="pospantau" name="pospantau" class="mr-2 w-3 h-3 align-middle">
                        <label for="pospantau" class="text-xs align-middle">Pos Pemantauan</label>
                    </div>
                    <div class="sidebar-item flex items-center py-1">
                        <input type="checkbox" id="titikPantau" name="titikPantau" class="mr-2 w-3 h-3 align-middle">
                        <label for="titikPantau" class="text-xs align-middle">Titik Pantau</label>
                    </div>
                </div>

                <!-- Layers Section -->
                {{-- <div class="sidebar-section">
            <h3>Map Layers</h3>
            <div class="sidebar-item flex items-center py-1 active" data-layer="street">
                📍 Street Map
            </div>
            <div class="sidebar-item flex items-center py-1" data-layer="satellite">
                🛰️ Satellite View
            </div>
            <div class="sidebar-item flex items-center py-1" data-layer="terrain">
                🏔️ Terrain Map
            </div>
        </div> --}}

                <!-- Tools Section -->
                {{-- <div class="sidebar-section">
            <h3>Map Tools</h3>
            <div class="sidebar-item flex items-center py-1" data-tool="measure">
                📏 Measure Distance
            </div>
            <div class="sidebar-item flex items-center py-1" data-tool="marker">
                📌 Add Marker
            </div>
            <div class="sidebar-item flex items-center py-1" data-tool="polygon">
                🔺 Draw Polygon
            </div>
        </div> --}}

                <!-- Filters Section -->
                {{-- <div class="sidebar-section">
            <h3>Data Filters</h3>
            <div class="sidebar-item flex items-center py-1" data-filter="all">
                🔍 Show All
            </div>
            <div class="sidebar-item flex items-center py-1" data-filter="schools">
                🏫 Schools
            </div>
            <div class="sidebar-item flex items-center py-1" data-filter="hospitals">
                🏥 Hospitals
            </div>
            <div class="sidebar-item flex items-center py-1" data-filter="parks">
                🌳 Parks
            </div>
        </div> --}}

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
                    </div>
                    <div class="divider"></div>

                    <div id="markerInfo" class="bg-white bg-opacity-10 p-2 rounded-lg text-xs">
                    </div>
                </div>
                <!-- Clear Cache Button -->
                <div class="py-5 text-center">
                    <button id="clearCacheBtn"
                        class="btn btn-error btn-sm text-white font-semibold shadow-md transition">🗑️ Bersihkan
                        Cache</button>
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

    {{-- <script>
        ...seluruh script JS dipindahkan ke resources/js/pages/geospasial-map.js --}}
</body>

</html>
