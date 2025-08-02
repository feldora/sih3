{{-- resources/views/sih3/about.blade.php --}}

@extends('layouts.app-lain')

@section('title', 'Tentang SIH³ Provinsi Sulawesi Tengah')

@section('content')
    <div class="">
        {{-- Hero Section --}}
        <section class="hero min-h-[60vh] w-screen relative overflow-hidden text-white bg-base-200">
            {{-- Gradient Overlay --}}
            <div class="hero-overlay bg-opacity-60"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-primary/80 to-secondary/80"></div>

            {{-- Background Image --}}
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
                style="background-image: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=2070&q=80');">
            </div>

            {{-- Full-width Hero-content --}}
            <div
                class="hero-content w-full max-w-none
                flex flex-col items-center justify-center
                px-6 text-center relative z-10">

                {{-- Logo/Icon --}}
                <div class="mt-12">
                    <div
                        class="w-20 h-20 mx-auto bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z">
                            </path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-6xl font-bold mb-4 drop-shadow-lg">SIH³</h1>
                <h2 class="text-3xl font-semibold drop-shadow-md">Provinsi Sulawesi Tengah</h2>
                <p class="text-xl opacity-95 leading-relaxed drop-shadow-sm">
                    Sistem Informasi Hidrologi, Hidrometeorologi & Hidrogeologi
                </p>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                        <div class="text-2xl font-bold">24/7</div>
                        <div class="text-sm opacity-90">Monitoring</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                        <div class="text-2xl font-bold">Real-time</div>
                        <div class="text-sm opacity-90">Data</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4">
                        <div class="text-2xl font-bold">Sulteng</div>
                        <div class="text-sm opacity-90">Coverage</div>
                    </div>
                </div>

                {{-- <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a href="http://sulteng.hidromet.sih3.bmkg.go.id/" 
             class="btn btn-accent btn-lg shadow-lg hover:shadow-xl transition-all" 
             target="_blank">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            Akses Portal
          </a>
          <button class="btn btn-outline btn-lg border-white text-white hover:bg-white hover:text-primary shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Pelajari Lebih
          </button>
        </div> --}}
            </div>
    </div>

    {{-- Floating Animation Elements --}}
    <div class="absolute top-10 left-10 w-4 h-4 bg-white/30 rounded-full animate-pulse"></div>
    <div class="absolute top-32 right-20 w-6 h-6 bg-white/20 rounded-full animate-bounce"></div>
    <div class="absolute bottom-20 left-20 w-3 h-3 bg-white/40 rounded-full animate-ping"></div>
    </section>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 py-12 space-y-16">

        {{-- What is SIH3 Section --}}
        <section class="card mt-2 bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-3xl mb-6 text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Apa Itu SIH³?
                </h2>
                <div class="prose max-w-none">
                    <p class="text-lg leading-relaxed">
                        <strong>SIH³</strong> adalah singkatan dari <em>Sistem Informasi Hidrologi, Hidrometeorologi dan
                            Hidrogeologi</em>.
                        Sistem ini merupakan platform nasional hasil kerja sama antara BMKG, Kementerian PUPR, dan
                        Kementerian ESDM
                        untuk menyediakan data dan layanan terkait aliran air, curah hujan, kondisi iklim, dan hydro‐geologi
                        yang terintegrasi.
                    </p>
                </div>
            </div>
        </section>

        {{-- Implementation Section --}}
        <section class="card mt-2 bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-3xl mb-6 text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Implementasi di Provinsi Sulawesi Tengah
                </h2>
                <div class="prose max-w-none mb-6">
                    <p class="text-lg leading-relaxed">
                        Pemerintah Provinsi Sulteng resmi mengadopsi SIH³ sebagai bagian dari strategi mitigasi dan
                        keterbukaan informasi publik.
                        Portal SIH³ Sulteng mempermudah masyarakat mengakses informasi cuaca, kualitas udara, curah hujan,
                        dan kejadian ekstrem lainnya.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="alert alert-info">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m5 0v-9a2 2 0 00-2-2H8a2 2 0 00-2 2v9m8 0V9a2 2 0 012-2h2a2 2 0 012 2v12m-6 0h2m0 0h2">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Pilot Project</h3>
                            <div class="text-xs">Kota Palu - 25 September 2019</div>
                        </div>
                    </div>

                    <div class="alert alert-success">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Koordinasi Teknis</h3>
                            <div class="text-xs">BWS Sulawesi III Palu</div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Transformasi Digital</h3>
                            <div class="text-xs">Perpres No. 82 Tahun 2023</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Benefits Section --}}
        <section class="card mt-2 bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-3xl mb-6 text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                        </path>
                    </svg>
                    Manfaat Utama SIH³
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="card bg-error text-error-content shadow-lg">
                        <div class="card-body">
                            <div class="card-title">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                Mitigasi Bencana Dini
                            </div>
                            <p class="text-sm opacity-90">
                                Mempercepat respons terhadap potensi bencana hidrometeorologi seperti banjir dan tanah
                                longsor
                                yang rentan terjadi di hampir seluruh wilayah Sulawesi Tengah.
                            </p>
                        </div>
                    </div>

                    <div class="card bg-info text-info-content shadow-lg">
                        <div class="card-body">
                            <div class="card-title">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                Pelayanan Informasi Publik
                            </div>
                            <p class="text-sm opacity-90">
                                Memastikan informasi H3 selalu tersedia dan mudah diakses oleh masyarakat melalui portal
                                digital
                                dan PPID setiap OPD sesuai UU 14/2018.
                            </p>
                        </div>
                    </div>

                    <div class="card bg-success text-success-content shadow-lg">
                        <div class="card-body">
                            <div class="card-title">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4">
                                    </path>
                                </svg>
                                Sinkronisasi Data
                            </div>
                            <p class="text-sm opacity-90">
                                Menjadi jembatan integrasi berbagai aplikasi pengelolaan sumber daya air yang dikelola pusat
                                dan daerah,
                                mendukung program <em>Satu Data Indonesia</em>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Stakeholders Section --}}
        <section class="card mt-2 bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-3xl mb-6 text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m5 0v-9a2 2 0 00-2-2H8a2 2 0 00-2 2v9m8 0V9a2 2 0 012-2h2a2 2 0 012 2v12m-6 0h2m0 0h2">
                        </path>
                    </svg>
                    Pengelola dan Stakeholder
                </h2>

                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Institusi</th>
                                <th>Peran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-primary text-primary-content rounded-full w-8">
                                                <span class="text-xs">B</span>
                                            </div>
                                        </div>
                                        <div class="font-bold">BMKG Pusat</div>
                                    </div>
                                </td>
                                <td>Penyelenggara teknis H3 nasional</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-secondary text-secondary-content rounded-full w-8">
                                                <span class="text-xs">P</span>
                                            </div>
                                        </div>
                                        <div class="font-bold">Badan Litbang PUPR & Kementerian ESDM</div>
                                    </div>
                                </td>
                                <td>Penyedia data hidrologi & hidrogeologi</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-accent text-accent-content rounded-full w-8">
                                                <span class="text-xs">S</span>
                                            </div>
                                        </div>
                                        <div class="font-bold">Pemerintah Provinsi Sulteng</div>
                                    </div>
                                </td>
                                <td>Kebijakan pemanfaatan sektor publik dan pelayanan informasi</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-info text-info-content rounded-full w-8">
                                                <span class="text-xs">B</span>
                                            </div>
                                        </div>
                                        <div class="font-bold">Balai Wilayah Sungai Sulawesi III Palu</div>
                                    </div>
                                </td>
                                <td>Pusat koordinasi teknis pengumpulan dan manajemen data</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-warning text-warning-content rounded-full w-8">
                                                <span class="text-xs">G</span>
                                            </div>
                                        </div>
                                        <div class="font-bold">GAW Lore Lindu Bariri & Meteorologi Palu</div>
                                    </div>
                                </td>
                                <td>Operasional data lokal Sulteng dan pelatihan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Legal Basis Section --}}
        <section class="card mt-2 bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-3xl mb-6 text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Dasar Hukum
                </h2>

                <div class="steps steps-vertical lg:steps-horizontal">
                    <div class="step step-primary">
                        <div class="text-left">
                            <div class="font-bold">UU No. 14 Tahun 2008</div>
                            <div class="text-sm opacity-70">Keterbukaan Informasi Publik</div>
                        </div>
                    </div>
                    <div class="step step-primary">
                        <div class="text-left">
                            <div class="font-bold">Pergub No. 52 Tahun 2017</div>
                            <div class="text-sm opacity-70">Kebijakan Pengelolaan SIH³ Sulteng</div>
                        </div>
                    </div>
                    <div class="step step-primary">
                        <div class="text-left">
                            <div class="font-bold">Perpres No. 82 Tahun 2023</div>
                            <div class="text-sm opacity-70">Interoperabilitas layanan digital SPBE</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact & Access Section --}}
        <section class="card mt-2 bg-gradient-to-r from-primary to-secondary text-primary-content shadow-xl">
            <div class="card-body text-center">
                <h2 class="card-title text-3xl mb-6 justify-center text-black">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                        </path>
                    </svg>
                    Akses Portal & Kontak
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl mx-auto">
                    <div class="card bg-base-100 text-base-content shadow-lg">
                        <div class="card-body">
                            <h3 class="card-title justify-center text-primary">Portal Online</h3>
                            <p class="opacity-70 mb-4">Akses SIH³ Prov. Sulteng secara online</p>
                            <div class="card-actions justify-center">
                                <a href="http://sulteng.hidromet.sih3.bmkg.go.id/" class="btn btn-primary"
                                    target="_blank">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                        </path>
                                    </svg>
                                    Kunjungi Portal
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-base-100 text-base-content shadow-lg">
                        <div class="card-body">
                            <h3 class="card-title justify-center text-secondary">Kontak Informasi</h3>
                            <p class="opacity-70 mb-4">Hubungi kami untuk informasi lebih lanjut</p>
                            <div class="space-y-2">
                                <div class="badge badge-outline">UPT BMKG Lore Lindu Bariri</div>
                                <div class="badge badge-outline">BWS Sulawesi III Palu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
