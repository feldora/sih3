@extends('layouts.app-lain')

@section('title', 'Beranda')

@section('content')
    {{-- <div class="min-h-screen mb-20"> --}}


        <section class="relative h-screen overflow-hidden carousel-modern">
            <div x-data="{
                    active: 0,
                    slides: [
                        { img: '/images/sungai_palu.webp', gradient: 'from-blue-900/60 to-cyan-600/40' },
                        { img: '/images/saluopa.jpg', gradient: 'from-emerald-900/60 to-blue-600/40' },
                        { img: '/images/paisupok.webp', gradient: 'from-purple-900/60 to-pink-600/40' }
                    ],
                    interval: null,
                    start() {
                        this.interval = setInterval(() => {
                            this.active = (this.active + 1) % this.slides.length;
                        }, 4000);
                    },
                    stop() {
                        clearInterval(this.interval);
                    }
                }"
                x-init="start()"
                @mouseenter="stop()" @mouseleave="start()"
                class="relative w-full h-full"
            >
                <template x-for="(slide, idx) in slides" :key="idx">
                    <div
                        x-show="active === idx"
                        x-transition:enter="transition-opacity duration-700"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity duration-700"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute inset-0 w-full h-full"
                        style="z-index: 1;"
                    >
                        <img :src="slide.img" class="w-full h-full object-cover" alt="">
                        <div class="absolute inset-0" :class="'bg-gradient-to-r ' + slide.gradient"></div>
                    </div>
                </template>
            </div>
            <!-- Hero Content -->
            <div class="absolute inset-0 flex items-center justify-center z-10">
                <div class="text-center text-white hero-content max-w-4xl px-6">
                    <div class="mb-6">
                        <div class="water-drop mx-auto mb-4 float"></div>
                        <h1 class="text-6xl font-bold mb-6 hero-title">
                            Sistem Informasi H3
                        </h1>
                        <p class="text-xl mb-8 leading-relaxed opacity-90">
                            Portal terintegrasi untuk pengelolaan Hidrologi, Hidrometeorologi, dan Hidrogeologi 
                            di Provinsi Sulawesi Tengah
                        </p>
                    </div>
                </div>
            </div>

            <!-- Carousel Indicators -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                <a href="#slide1" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white smooth-transition"></a>
                <a href="#slide2" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white smooth-transition"></a>
                <a href="#slide3" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white smooth-transition"></a>
            </div>
        </section>

        <!-- Enhanced H3 Categories -->
        <section class="py-20 gradient-light">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">Bidang Keahlian H3</h2>
                    <p class="text-xl text-gray-600">Tiga pilar utama pengelolaan sumber daya air</p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="modern-card rounded-2xl p-8 text-center group">
                        <div class="text-6xl mb-6 group-hover:scale-110 smooth-transition">💧</div>
                        <h3 class="text-2xl font-bold mb-4 text-primary">Hidrologi</h3>
                        <p class="text-gray-600 leading-relaxed">Mempelajari pergerakan, distribusi, dan kualitas air di bumi untuk pengelolaan sumber daya air yang berkelanjutan.</p>
                        <div class="mt-6">
                            <a href="/artikel?category=hidrologi" class="btn btn-primary btn-sm hover:scale-105 smooth-transition">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Pelajari
                            </a>
                        </div>
                    </div>
                    
                    <div class="modern-card rounded-2xl p-8 text-center group">
                        <div class="text-6xl mb-6 group-hover:scale-110 smooth-transition">🌦️</div>
                        <h3 class="text-2xl font-bold mb-4 text-secondary">Hidrometeorologi</h3>
                        <p class="text-gray-600 leading-relaxed">Mengkaji hubungan antara proses atmosfer dan air di permukaan bumi untuk prediksi cuaca dan iklim.</p>
                        <div class="mt-6">
                            <a href="/artikel?category=hidrometeorologi" class="btn btn-secondary btn-sm hover:scale-105 smooth-transition">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Pelajari
                            </a>
                        </div>
                    </div>
                    
                    <div class="modern-card rounded-2xl p-8 text-center group">
                        <div class="text-6xl mb-6 group-hover:scale-110 smooth-transition">🌊</div>
                        <h3 class="text-2xl font-bold mb-4 text-accent">Hidrogeologi</h3>
                        <p class="text-gray-600 leading-relaxed">Fokus pada distribusi dan pergerakan air tanah di dalam tanah dan batuan untuk eksplorasi air bawah tanah.</p>
                        <div class="mt-6">
                            <a href="/artikel?category=hidrogeologi" class="btn btn-accent btn-sm hover:scale-105 smooth-transition">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Pelajari
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-white">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">Wilayah Sungai</h2>
                    <p class="text-xl text-gray-600">Sistem sungai utama yang dimonitor di Sulawesi Tengah</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 river-grid">
                    @foreach ($wilayahSungai as $ws)
                    <div class="river-card rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-2xl">
                                🏞️
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $ws->name }}</h3>
                                <p class="text-gray-500">{{ $ws->description }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between items-center">
                            <div class="flex space-x-2">
                                <span class="badge badge-primary badge-sm">{{ $ws->status }}</span>
                                <span class="badge badge-outline badge-sm">{{ $ws->titikPantau->count() }} Stasiun</span>
                            </div>
                            <button class="btn btn-ghost btn-sm text-primary hover:bg-primary hover:text-white smooth-transition">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-20 gradient-water">
            <div class="container mx-auto px-6">
                <div class="glass-dark rounded-3xl overflow-hidden shadow-2xl">
                    <div class="grid md:grid-cols-2 gap-0">
                        <div class="relative">
                            <img src="/images/sih3.png" class="w-full h-full object-cover" alt="SIH3 Illustration">
                            <div class="absolute inset-0 bg-gradient-to-br from-transparent to-black/20"></div>
                        </div>
                        <div class="p-12 text-white flex flex-col justify-center">
                            <h2 class="text-4xl font-bold mb-6">Apa Itu SIH3?</h2>
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
@endsection
