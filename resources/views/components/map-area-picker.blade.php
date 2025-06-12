@props(['oldCoordinates' => [], 'name' => 'coordinates', 'style' => ''])

@push('styles')
    <style>
        #map-polygon {
            @if ($style)
                {{ $style }}
            @else
                width: 100%;
                height: 600px;                
            @endif
        }
        
        .map-controls-polygon {
            margin-bottom: 10px;
        }
        
        .btn-clear-polygon {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-clear-polygon:hover {
            background-color: #c82333;
        }
        
        .coordinates-info-polygon {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .area-info {
            margin-top: 5px;
            font-weight: bold;
            color: #28a745;
        }
    </style>
@endpush

<div>
    <div class="map-controls-polygon">
        <button type="button" class="btn-clear-polygon" onclick="clearAllPolygonPoints()">Hapus Semua Titik</button>
        <button type="button" class="btn-clear-polygon" onclick="removeLastPolygonPoint()" style="background-color: #ffc107; margin-left: 10px;">Hapus Titik Terakhir</button>
        <button type="button" class="btn-clear-polygon" onclick="closePolygon()" style="background-color: #28a745; margin-left: 10px;">Tutup Area</button>
        <span style="margin-left: 15px; font-size: 14px; color: #666;">
            Klik pada peta untuk menambah titik. Area akan terbentuk otomatis.
        </span>
    </div>
    
    <div id="map-polygon"></div>

    <!-- Hidden input untuk menyimpan koordinat -->
    <input type="hidden" name="{{ $name }}" id="coordinates-polygon" value="{{ old($name, json_encode($oldCoordinates)) }}">
    <input type="text" id="luas_area" name="luas_area" value="{{ old('luas_area', $oldArea ?? '') }}" placeholder="Luas Area" hidden>
    <div class="coordinates-info-polygon">
        <strong>Total Titik:</strong> <span id="point-count-polygon">0</span>
        <div class="area-info">
            <span id="area-info">Area akan dihitung setelah minimal 3 titik</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapPolygon = L.map('map-polygon').setView([-0.89722, 119.86627], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(mapPolygon);

        var markersPolygon = [];  // Array untuk kompatibilitas (tidak digunakan)
        var polygon = null;  // Variabel untuk menyimpan polygon
        var coordinatesPolygon = @json($oldCoordinates ?: []);  // Data koordinat yang sudah ada

        // Tidak menambahkan marker visual, hanya menyimpan koordinat
        
        // Menggambar polygon jika ada koordinat
        updatePolygon();
        updatePointCountPolygon();

        // Fungsi untuk memperbarui input hidden dengan array koordinat
        function updateCoordinatesPolygon() {
            document.getElementById('coordinates-polygon').value = JSON.stringify(coordinatesPolygon);
            updatePointCountPolygon();
        }

        // Fungsi untuk menghitung luas area (dalam meter persegi)
        function calculateArea(coordinates) {
            if (coordinates.length < 3) return 0;
            
            // Convert coordinates to leaflet format for area calculation
            var latlngs = coordinates.map(function(coord) {
                return [coord[1], coord[0]];  // [lat, lng]
            });
            
            // Simple polygon area calculation using shoelace formula
            // Note: This is approximate for geographic coordinates
            var area = 0;
            var j = latlngs.length - 1;
            
            for (var i = 0; i < latlngs.length; i++) {
                area += (latlngs[j][1] + latlngs[i][1]) * (latlngs[j][0] - latlngs[i][0]);
                j = i;
            }
            
            area = Math.abs(area / 2);
            
            // Convert to approximate square meters (very rough approximation)
            // 1 degree ≈ 111,320 meters at equator
            var areaInSqMeters = area * 111320 * 111320 * Math.cos(latlngs[0][0] * Math.PI / 180);
            
            return areaInSqMeters;
        }

        // Fungsi untuk format area
        function formatArea(area) {
            if (area < 10000) {
                return area.toFixed(2) + ' m²';
            } else if (area < 1000000) {
                return (area / 10000).toFixed(2) + ' hektar';
            } else {
                return (area / 1000000).toFixed(2) + ' km²';
            }
        }

        // Fungsi untuk memperbarui polygon
        function updatePolygon() {
            // Hapus polygon yang sudah ada
            if (polygon) {
                mapPolygon.removeLayer(polygon);
            }

            // Buat polygon baru jika ada minimal 3 titik
            if (coordinatesPolygon.length >= 3) {
                var latlngs = coordinatesPolygon.map(function(coord) {
                    return [coord[1], coord[0]];  // [lat, lng]
                });

                polygon = L.polygon(latlngs, {
                    color: '#3388ff',
                    weight: 3,
                    opacity: 0.8,
                    fillColor: '#3388ff',
                    fillOpacity: 0.2
                }).addTo(mapPolygon);

                // Hitung dan tampilkan luas area
                var area = calculateArea(coordinatesPolygon);
                document.getElementById('area-info').textContent = 'Luas Area: ' + formatArea(area);
                document.getElementById('luas_area').value = area.toFixed(2);
            } else {
                document.getElementById('area-info').textContent = 'Area akan dihitung setelah minimal 3 titik';
            }
        }

        // Fungsi untuk memperbarui jumlah titik
        function updatePointCountPolygon() {
            document.getElementById('point-count-polygon').textContent = coordinatesPolygon.length;
        }

        // Fungsi untuk menghapus titik terakhir
        function removeLastPolygonPoint() {
            if (coordinatesPolygon.length > 0) {
                coordinatesPolygon.pop();
                updatePolygon();
                updateCoordinatesPolygon();
            }
        }

        // Event listener untuk klik pada peta
        mapPolygon.on('click', function (e) {
            var lat = e.latlng.lat.toFixed(6);
            var lng = e.latlng.lng.toFixed(6);

            // Menambahkan koordinat baru ke array
            coordinatesPolygon.push([lng, lat]);

            // Update polygon dan coordinates
            updatePolygon();
            updateCoordinatesPolygon();
        });

        // Fungsi global untuk menghapus semua titik
        window.clearAllPolygonPoints = function() {
            if (coordinatesPolygon.length === 0) {
                alert('Tidak ada titik untuk dihapus');
                return;
            }

            if (confirm('Hapus semua titik? Tindakan ini tidak dapat dibatalkan.')) {
                // Hapus polygon
                if (polygon) {
                    mapPolygon.removeLayer(polygon);
                    polygon = null;
                }

                // Reset array coordinates
                coordinatesPolygon = [];

                // Update coordinates dan point count
                updateCoordinatesPolygon();
            }
        };

        // Fungsi global untuk menghapus titik terakhir
        window.removeLastPolygonPoint = function() {
            if (coordinatesPolygon.length === 0) {
                alert('Tidak ada titik untuk dihapus');
                return;
            }

            if (confirm('Hapus titik terakhir?')) {
                removeLastPolygonPoint();
            }
        };

        // Fungsi global untuk menutup polygon (menghubungkan titik terakhir dengan titik pertama)
        window.closePolygon = function() {
            if (coordinatesPolygon.length < 3) {
                alert('Minimal 3 titik diperlukan untuk membuat area');
                return;
            }

            if (coordinatesPolygon.length > 2) {
                // Cek apakah polygon sudah tertutup
                var firstPoint = coordinatesPolygon[0];
                var lastPoint = coordinatesPolygon[coordinatesPolygon.length - 1];
                
                if (firstPoint[0] !== lastPoint[0] || firstPoint[1] !== lastPoint[1]) {
                    if (confirm('Tutup area dengan menghubungkan ke titik pertama?')) {
                        coordinatesPolygon.push([firstPoint[0], firstPoint[1]]);
                        updatePolygon();
                        updateCoordinatesPolygon();
                    }
                } else {
                    alert('Area sudah tertutup');
                }
            }
        };
    });
</script>
@endpush