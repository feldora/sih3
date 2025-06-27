@props(['oldCoordinates' => [], 'name' => 'coordinates', 'style' => ''])

@push('styles')
    <style>
        #map {
            @if ($style)
                {{ $style }}
            @else
                width: 100%;
                height: 600px;                
            @endif
            cursor: crosshair;  /* Ganti dengan cursor tanda tambah */
        }
        
        .map-controls {
            margin-bottom: 10px;
        }
        
        .btn-clear {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-clear:hover {
            background-color: #c82333;
        }
        
        .coordinates-info {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .distance-info {
            margin-top: 5px;
            font-weight: bold;
            color: #28a745;
        }
    </style>
@endpush

<div>
    <div class="map-controls">
        <button type="button" class="btn-clear" onclick="clearAllMarkers()">Hapus Semua Titik</button>
        <button type="button" class="btn-clear" onclick="removeLastPoint()" style="background-color: #ffc107; margin-left: 10px;">Hapus Titik Terakhir</button>
        <span style="margin-left: 15px; font-size: 14px; color: #666;">
            Klik pada peta untuk menambah titik. Hanya garis yang akan ditampilkan.
        </span>
    </div>
    
    <div id="map"></div>

    <!-- Hidden input untuk menyimpan koordinat -->
    <input type="hidden" name="{{ $name }}" id="coordinates" value="{{ old($name, json_encode($oldCoordinates)) }}">
    <input type="text" id="jarak" name="jarak" value="{{ old('jarak', 0) }}" hidden>
    <div class="coordinates-info">
        <strong>Total Titik:</strong> <span id="point-count">0</span>
        <div class="distance-info">
            <span id="distance-info">Panjang garis akan dihitung setelah minimal 2 titik</span>
        </div>
        <div class="distance-info" style="color:#007bff;">
            <span id="keliling-info"></span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('map').setView([-0.89722, 119.86627], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        var markers = [];  // Array untuk kompatibilitas (tidak digunakan)
        var polyline = null;  // Variabel untuk menyimpan polyline
        var coordinates = @json($oldCoordinates ?: []);  // Data koordinat yang sudah ada
        var marker = null;  // Variabel untuk menyimpan marker titik pertama

        // Tidak menambahkan marker visual, hanya menyimpan koordinat

        // Menggambar garis jika ada koordinat
        updatePolyline();
        updatePointCount();
        updateDistanceInfo();

        // Fungsi untuk menghitung jarak antar titik menggunakan Haversine formula
        function calculateDistance(lat1, lng1, lat2, lng2) {
            var R = 6371000; // Radius bumi dalam meter
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLng = (lng2 - lng1) * Math.PI / 180;
            var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLng/2) * Math.sin(dLng/2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c; // Jarak dalam meter
        }

        // Fungsi untuk menghitung total panjang garis
        function calculateTotalDistance(coordinates) {
            if (coordinates.length < 2) return 0;
            
            var totalDistance = 0;
            for (var i = 1; i < coordinates.length; i++) {
                var lat1 = coordinates[i-1][1];
                var lng1 = coordinates[i-1][0];
                var lat2 = coordinates[i][1];
                var lng2 = coordinates[i][0];
                
                totalDistance += calculateDistance(lat1, lng1, lat2, lng2);
            }
            
            return totalDistance;
        }

        // Fungsi untuk format jarak
        function formatDistance(distance) {
            if (distance < 1000) {
                return distance.toFixed(2) + ' m';
            } else {
                return (distance / 1000).toFixed(2) + ' km';
            }
        }
        // Fungsi untuk memperbarui input hidden dengan array koordinat
        function updateCoordinates() {
            document.getElementById('{{ $name }}').value = JSON.stringify(coordinates);
            updatePointCount();
            updateDistanceInfo();
        }

        // Fungsi untuk memperbarui polyline
        function updatePolyline() {
            // Hapus polyline yang sudah ada
            if (polyline) {
                map.removeLayer(polyline);
            }

            // Buat polyline baru jika ada minimal 2 titik
            if (coordinates.length >= 2) {
                var latlngs = coordinates.map(function(coord) {
                    return [coord[1], coord[0]];  // [lat, lng]
                });

                polyline = L.polyline(latlngs, {
                    color: '#3388ff',
                    weight: 3,
                    opacity: 0.8
                }).addTo(map);

                // Hitung dan tampilkan panjang garis
                updateDistanceInfo();
            } else {
                document.getElementById('distance-info').textContent = 'Panjang garis akan dihitung setelah minimal 2 titik';
            }
        }

        // Fungsi untuk memperbarui jumlah titik
        function updatePointCount() {
            document.getElementById('point-count').textContent = coordinates.length;
        }

        // Fungsi untuk menghitung keliling polygon
        function calculateKeliling(coordinates) {
            if (coordinates.length < 3) return 0;
            var keliling = 0;
            for (var i = 0; i < coordinates.length; i++) {
                var next = (i + 1) % coordinates.length;
                var lat1 = coordinates[i][1];
                var lng1 = coordinates[i][0];
                var lat2 = coordinates[next][1];
                var lng2 = coordinates[next][0];
                keliling += calculateDistance(lat1, lng1, lat2, lng2);
            }
            return keliling;
        }

        // Fungsi untuk memperbarui informasi jarak
        function updateDistanceInfo() {
            if (coordinates.length >= 2) {
                var totalDistance = calculateTotalDistance(coordinates);
                document.getElementById('distance-info').textContent = 'Panjang Garis: ' + formatDistance(totalDistance);
                document.getElementById('jarak').value = totalDistance; // Update input jarak
            } else {
                document.getElementById('distance-info').textContent = 'Panjang garis akan dihitung setelah minimal 2 titik';
            }
            // Keliling area
            if (coordinates.length >= 3) {
                var keliling = calculateKeliling(coordinates);
                document.getElementById('keliling-info').textContent = 'Keliling Area: ' + formatDistance(keliling);
            } else {
                document.getElementById('keliling-info').textContent = '';
            }
        }

        // Fungsi untuk menghapus titik terakhir
        function removeLastPoint() {
            if (coordinates.length > 0) {
                coordinates.pop();
                updatePolyline();
                updateCoordinates();
            }
        }

        map.on('click', function (e) {
            var lat = e.latlng.lat.toFixed(6);
            var lng = e.latlng.lng.toFixed(6);

            // Menambahkan koordinat baru ke array
            coordinates.push([lng, lat]);

            // Jika ini adalah titik pertama, tambahkan marker
            if (coordinates.length === 1) {
                marker = L.marker([lat, lng]).addTo(map);
            }

            // Update polyline dan coordinates
            updatePolyline();
            updateCoordinates();
        });

        // Fungsi global untuk menghapus semua titik
        window.clearAllMarkers = function() {
            if (coordinates.length === 0) {
                alert('Tidak ada titik untuk dihapus');
                return;
            }

            if (confirm('Hapus semua titik? Tindakan ini tidak dapat dibatalkan.')) {
                // Hapus polyline
                if (polyline) {
                    map.removeLayer(polyline);
                    polyline = null;
                }

                // Hapus marker pertama
                if (marker) {
                    map.removeLayer(marker);
                    marker = null;
                }

                // Reset array coordinates
                coordinates = [];

                // Update coordinates dan point count
                updateCoordinates();
            }
        };

        // Fungsi global untuk menghapus titik terakhir
        window.removeLastPoint = function() {
            if (coordinates.length === 0) {
                alert('Tidak ada titik untuk dihapus');
                return;
            }

            if (confirm('Hapus titik terakhir?')) {
                // Jika titik pertama dihapus, juga hapus marker
                if (coordinates.length === 1) {
                    if (marker) {
                        map.removeLayer(marker);
                        marker = null;
                    }
                }

                removeLastPoint();
            }
        };
    });
</script>
@endpush
