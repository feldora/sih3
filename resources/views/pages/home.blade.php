@extends('layouts.app-lain')

@section('title', 'Beranda')

@section('content')
    <div class="min-h-screen mb-20">


        <section class="carousel md:w-full max-h-screen">
            <div id="slide1" class="carousel-item relative w-full">
                <img src="/images/sungai_palu.webp" class="w-full h-screen md:h-[66vh] object-cover" />
            </div>
            <div id="slide2" class="carousel-item relative w-full">
                <img src="/images/saluopa.jpg" class="w-full h-screen md:h-[66vh] object-cover" />
            </div>
            <div id="slide3" class="carousel-item relative w-full">
                <img src="/images/paisupok.webp" class="w-full h-screen md:h-[66vh] object-cover" />
            </div>
        </section>

        <section class="flex-1 py-16 bg-gray-100 text-gray-800 overflow-auto">
            <div class="container mx-auto grid md:grid-cols-3 gap-8 text-center p-10">
                <div
                    class="bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200 rounded-xl shadow p-8 transition hover:scale-105">
                    <a href="#">
                        <div class="text-4xl mb-4">💧</div>
                        <h3 class="text-xl font-semibold mb-2 text-blue-700">
                            Hidrologi
                        </h3>
                    </a>
                    <p class="text-blue-900">Mempelajari pergerakan, distribusi, dan kualitas air di bumi.</p>
                </div>
                <div
                    class="bg-gradient-to-br from-yellow-100 via-yellow-50 to-yellow-200 rounded-xl shadow p-8 transition hover:scale-105">
                    <a href="#">
                        <div class="text-4xl mb-4">🌦️</div>
                        <h3 class="text-xl font-semibold mb-2 text-yellow-700">
                            Hidrometeorologi
                        </h3>
                    </a>
                    <p class="text-yellow-900">Mengkaji hubungan antara proses atmosfer dan air di permukaan bumi.</p>
                </div>
                <div
                    class="bg-gradient-to-br from-teal-100 via-teal-50 to-teal-200 rounded-xl shadow p-8 transition hover:scale-105">
                    <a href="#">
                        <div class="text-4xl mb-4">🌊</div>
                        <h3 class="text-xl font-semibold mb-2 text-teal-700">
                            Hidrogeologi
                        </h3>
                    </a>
                    <p class="text-teal-900">Fokus pada distribusi dan pergerakan air tanah di dalam tanah dan batuan.</p>
                </div>
            </div>

            <div class="flex w-full flex-col mt-10 mb-10">
                <div class="flex flex-col items-center justify-center w-full">
                    <h1 class="text-4xl font-bold mb-4">Wilayah Sungai</h1>
                    <div class="flex flex-wrap justify-center gap-6 w-full max-w-screen">
                        @php
                            $sungai = ['Sungai Bongka', 'Sungai Laa', 'Sungai Lariang', 'Sungai Palu', 'Sungai Poso'];
                        @endphp

                        @foreach ($sungai as $index => $nama)
                            <a href="#"
                                class="bg-white shadow rounded-lg p-6 flex items-center opacity-0 translate-y-8 transition-all duration-700 ease-out hover:scale-105 hover:shadow-xl hover:-translate-y-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                style="transition-delay: {{ $index * 100 }}ms" x-data x-init="$el.classList.remove('opacity-0', 'translate-y-8')"
                                tabindex="0">
                                <img src="/images/ilustrasi/river_icon_126879.svg" alt="River Icon" class="w-12 h-12 mr-4">
                                <div class="flex flex-col items-start">
                                    <h2 class="text-xl font-semibold mb-1">{{ $nama }}</h2>
                                </div>
                            </a>
                        @endforeach

                        @push('scripts')
                            <script src="https://unpkg.com/alpinejs" defer></script>
                        @endpush
                    </div>
                </div>
            </div>
        </section>

        <section class="flex w-full flex-col mt-10 mb-10">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center justify-center">
                    <div
                        class="bg-gray-800 text-white rounded-lg shadow-lg flex flex-col md:flex-row w-full overflow-hidden">
                        <div class="md:w-1/2 w-full">
                            <img src="/images/sih3.png" class="w-full h-auto object-cover" alt="about img">
                        </div>
                        <div class="md:w-1/2 w-full p-8 flex flex-col justify-center">
                            <h2 class="text-2xl font-bold mb-4">Apa Itu SIH3?</h2>
                            <p class="mb-3">Portal SIH3 merupakan portal informasi pengelolaan hidrologi,
                                hidrometeorologi, dan hidrogeologi di Provinsi Sulawesi Tengah hasil kolaborasi BMKG, Dinas
                                Pekerjaan Umum Sumber Daya Air Provinsi Sulawesi Tengah, BBWS Sulawesi III, dan Dinas ESDM
                                Provinsi Sulawesi Tengah.</p>
                            <p class="mb-5">Keseluruhan informasi terkait sumber daya air akan dipublikasikan di portal
                                ini, sebagai transparansi data dan berita di Provinsi Sulawesi Tengah.</p>
                            <a href="#"
                                class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Kontak
                                Kami</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{-- <div id="map"></div> --}}
    </div>

    {{-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map').setView([-1.430025, 121.445617], 6); // Centered on Sulawesi Tengah

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Example marker (Palu)
            L.marker([-0.8917, 119.8707]).addTo(map)
                .bindPopup('Kota Palu')
                .openPopup();

            // Set map container height
            document.getElementById('map').style.height = '400px';
        });
    </script> --}}
@endsection
