@props([
    'latitude' => old('latitude'),
    'longitude' => old('longitude'),
    'label' => 'Koordinat',
    'hight' => '800px',
])

<div>
    <label for="map-point">{{ $label }}</label>
    <div id="map-point" style="height: {{ $hight }};"></div>

    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
</div>

@push('styles')
    <style>
    #map-point {
        cursor: crosshair;  /* Ganti dengan cursor tanda tambah */
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ambil nilai latitude dan longitude dari props atau default
        var initialLat = "{{ $latitude }}";
        var initialLng = "{{ $longitude }}";
        var initialLatNum = parseFloat(initialLat) || -0.89722;
        var initialLngNum = parseFloat(initialLng) || 119.86627;

        var map = L.map('map-point').setView([initialLatNum, initialLngNum], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        var marker;

        // Jika latitude dan longitude sudah ada, tampilkan marker di posisi tersebut
        if (initialLat && initialLng && !isNaN(initialLatNum) && !isNaN(initialLngNum)) {
            var awesomeIcon = L.AwesomeMarkers.icon({
                icon: 'fa fa-map-marker',
                markerColor: 'blue',
                prefix: 'fa',
                iconSize: [80, 80],
                iconAnchor: [5, 10],
                popupAnchor: [0, -24]
            });
            marker = L.marker([initialLatNum, initialLngNum], { icon: awesomeIcon }).addTo(map);
        }

        map.on('click', function (e) {
            var lat = e.latlng.lat.toFixed(6);
            var lng = e.latlng.lng.toFixed(6);

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            var awesomeIcon = L.AwesomeMarkers.icon({
                icon: 'fa fa-map-marker',
                markerColor: 'blue',
                prefix: 'fa',
                iconSize: [80, 80],
                iconAnchor: [5, 10],
                popupAnchor: [0, -24]
            });

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, { icon: awesomeIcon }).addTo(map);
            }
        });
    });
</script>
@endpush
