@props(['oldCoordinates' => [],'class'=>'', 'name' => 'coordinates', 'style' => '', 'height' => '800px'])

@push('styles')
    <style>
        #map-polygon {
            @if ($style)
                {{ $style }}
            @else
                width: 100%;
                height: {{ $height }};
            @endif
            cursor: crosshair;
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
        <button type="button" class="btn-clear-polygon" onclick="removeLastPolygonPoint()"
            style="background-color: #ffc107; margin-left: 10px;">Hapus Titik Terakhir</button>
        <button type="button" class="btn-clear-polygon" onclick="closePolygon()"
            style="background-color: #28a745; margin-left: 10px;">Tutup Area</button>
        <span style="margin-left: 15px; font-size: 14px; color: #666;">
            Klik pada peta untuk menambah titik. Area akan terbentuk otomatis.
        </span>
    </div>

    <div id="map-polygon" height="{{ $height }}"></div>

    <!-- Hidden input untuk menyimpan koordinat -->
    <input type="hidden" class="{{ $class }}" name="{{ $name }}" id="coordinates-polygon"
        value="{{ old($name, json_encode($oldCoordinates)) }}">
    <input type="text" id="luas_area" name="luas_area" value="{{ old('luas_area', $oldArea ?? '') }}"
        placeholder="Luas Area" hidden>
    <input type="text" id="keliling_area" name="keliling_area" value="{{ old('keliling_area', $oldArea ?? '') }}"
        placeholder="Keliling Area" hidden>
    <div class="coordinates-info-polygon">
        <strong>Total Titik:</strong> <span id="point-count-polygon">0</span>
        <div class="area-info">
            <span id="area-info">Area akan dihitung setelah minimal 3 titik</span>
        </div>
        <div class="area-info" style="color:#007bff;">
            <span id="keliling-info">Keliling akan dihitung setelah minimal 3 titik</span>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        // Global variables
        var mapPolygon, markersPolygon = [], polygon = null, coordinatesPolygon = @json($oldCoordinates ?: []);
        var cursorMarker = null, cursorLine = null;

        // Global functions
        function calculateDistance(lat1, lng1, lat2, lng2) {
            var R = 6371000; // Radius bumi dalam meter
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLng = (lng2 - lng1) * Math.PI / 180;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Jarak dalam meter
        }

        function reload(){
            coordinatesPolygon = document.getElementById('coordinates-polygon').value;
            console.log(typeof coordinatesPolygon);
            
            updatePolygon();
            updatePointCountPolygon();

        }

        function calculateArea(coordinates) {
            if (coordinates.length < 3) return 0;

            // Konversi ke format [lat, lng]
            var latlngs = coordinates.map(function(coord) {
                return [parseFloat(coord[1]), parseFloat(coord[0])];
            });

            // Validasi latlngs
            if (!latlngs[0] || isNaN(latlngs[0][0])) return 0;

            // Shoelace formula (untuk koordinat geografis kasar)
            var area = 0;
            var j = latlngs.length - 1;

            for (var i = 0; i < latlngs.length; i++) {
                area += (latlngs[j][1] + latlngs[i][1]) * (latlngs[j][0] - latlngs[i][0]);
                j = i;
            }

            area = Math.abs(area / 2);

            // Konversi kasar derajat ke meter (1 derajat ≈ 111320 m)
            const DEGREE_TO_METER = 111320;
            const latFactor = Math.cos(latlngs[0][0] * Math.PI / 180);
            var areaInSqMeters = area * DEGREE_TO_METER * DEGREE_TO_METER * latFactor;

            return areaInSqMeters;
        }

        function formatArea(area) {
            if (area < 10000) {
                return area.toFixed(2) + ' m²';
            } else if (area < 1000000) {
                return (area / 10000).toFixed(2) + ' hektar';
            } else {
                return (area / 1000000).toFixed(2) + ' km²';
            }
        }

        function formatDistance(distance) {
            if (distance < 1000) {
                return distance.toFixed(2) + ' m';
            } else {
                return (distance / 1000).toFixed(2) + ' km';
            }
        }

        function calculateKeliling(coordinates) {
            if (coordinates.length < 3) return 0;
            var keliling = 0;
            for (var i = 0; i < coordinates.length; i++) {
                var next = (i + 1) % coordinates.length;
                var lat1 = parseFloat(coordinates[i][1]);
                var lng1 = parseFloat(coordinates[i][0]);
                var lat2 = parseFloat(coordinates[next][1]);
                var lng2 = parseFloat(coordinates[next][0]);
                keliling += calculateDistance(lat1, lng1, lat2, lng2);
            }
            return keliling;
        }

        function updatePolygon() {
            if (polygon) {
                mapPolygon.removeLayer(polygon);
            }

            if (coordinatesPolygon.length >= 3) {
                var latlngs = coordinatesPolygon.map(function(coord) {
                    return [coord[1], coord[0]];
                });

                polygon = L.polygon(latlngs, {
                    color: '#3388ff',
                    weight: 3,
                    opacity: 0.8,
                    fillColor: '#3388ff',
                    fillOpacity: 0.2
                }).addTo(mapPolygon);

                var area = calculateArea(coordinatesPolygon);
                var keliling = calculateKeliling(coordinatesPolygon);

                document.getElementById('area-info').textContent = 'Luas Area: ' + formatArea(area);
                document.getElementById('luas_area').value = area.toFixed(2);
                document.getElementById('keliling_area').value = keliling.toFixed(2);
                document.getElementById('keliling-info').textContent = 'Keliling Area: ' + formatDistance(keliling);
            } else {
                document.getElementById('area-info').textContent = 'Area akan dihitung setelah minimal 3 titik';
                document.getElementById('keliling-info').textContent =
                    'Keliling akan dihitung setelah minimal 3 titik';
            }
        }

        function updatePointCountPolygon() {
            document.getElementById('point-count-polygon').textContent = coordinatesPolygon.length;
        }

        function updateCoordinatesPolygon() {
            document.getElementById('coordinates-polygon').value = JSON.stringify(coordinatesPolygon);
            updatePointCountPolygon();
        }

        function removeLastPolygonPoint() {
            if (coordinatesPolygon.length > 0) {
                coordinatesPolygon.pop();
                updatePolygon();
                updateCoordinatesPolygon();
            }
        }

        window.clearAllPolygonPoints = function() {
            if (coordinatesPolygon.length === 0) {
                alert('Tidak ada titik untuk dihapus');
                return;
            }

            if (confirm('Hapus semua titik? Tindakan ini tidak dapat dibatalkan.')) {
                if (polygon) {
                    mapPolygon.removeLayer(polygon);
                    polygon = null;
                }

                coordinatesPolygon = [];
                updateCoordinatesPolygon();
            }
        };

        window.removeLastPolygonPoint = function() {
            if (coordinatesPolygon.length === 0) {
                alert('Tidak ada titik untuk dihapus');
                return;
            }

            if (confirm('Hapus titik terakhir?')) {
                removeLastPolygonPoint();
            }
        };

        window.closePolygon = function() {
            if (coordinatesPolygon.length < 3) {
                alert('Minimal 3 titik diperlukan untuk membuat area');
                return;
            }

            if (coordinatesPolygon.length > 2) {
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

        // Initialize map
        document.addEventListener('DOMContentLoaded', function() {
            mapPolygon = L.map('map-polygon').setView([-0.89722, 119.86627], 8);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(mapPolygon);

            updatePolygon();
            updatePointCountPolygon();

            mapPolygon.on('click', function(e) {
                var lat = e.latlng.lat.toFixed(6);
                var lng = e.latlng.lng.toFixed(6);

                coordinatesPolygon.push([lng, lat]);
                updatePolygon();
                updateCoordinatesPolygon();
            });

            // Event untuk mengikuti kursor
            mapPolygon.on('mousemove', function(e) {
                var latlng = e.latlng;

                // Marker mengikuti kursor
                if (!cursorMarker) {
                    cursorMarker = L.circleMarker(latlng, {
                        radius: 5,
                        color: 'red',
                        fillColor: 'red',
                        fillOpacity: 1,
                    }).addTo(mapPolygon);
                } else {
                    cursorMarker.setLatLng(latlng);
                }

                // Garis dari titik terakhir ke kursor
                if (coordinatesPolygon.length > 0) {
                    var lastCoord = coordinatesPolygon[coordinatesPolygon.length - 1];
                    var lastLatLng = L.latLng(lastCoord[1], lastCoord[0]);

                    if (!cursorLine) {
                        cursorLine = L.polyline([lastLatLng, latlng], {
                            color: 'red',
                            dashArray: '5, 5'
                        }).addTo(mapPolygon);
                    } else {
                        cursorLine.setLatLngs([lastLatLng, latlng]);
                    }
                } else {
                    if (cursorLine) {
                        mapPolygon.removeLayer(cursorLine);
                        cursorLine = null;
                    }
                }
            });

            // Hapus marker/garis saat mouse keluar dari peta
            mapPolygon.on('mouseout', function() {
                if (cursorMarker) {
                    mapPolygon.removeLayer(cursorMarker);
                    cursorMarker = null;
                }
                if (cursorLine) {
                    mapPolygon.removeLayer(cursorLine);
                    cursorLine = null;
                }
            });
        });
    </script>
@endpush
