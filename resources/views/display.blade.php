<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Monitoring SI-AKIK</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kab-bandung.png') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bedas: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .slide-enter-active,
        .slide-leave-active {
            transition: all 0.8s ease;
        }

        .slide-enter-from {
            opacity: 0;
            transform: translateY(20px);
        }

        .slide-leave-to {
            opacity: 0;
            transform: translateY(-20px);
        }

        /* Glassmorphism style */
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        /* Marquee ticker style */
        .ticker-wrap {
            width: 100%;
            overflow: hidden;
            background-color: rgba(17, 24, 39, 0.9);
            padding-left: 100%;
            box-sizing: content-box;
        }

        .ticker {
            display: inline-block;
            white-space: nowrap;
            padding-right: 100%;
            box-sizing: content-box;
            animation: ticker 25s linear infinite;
        }

        .ticker__item {
            display: inline-block;
            padding: 0 2rem;
            font-size: 1.1rem;
            color: white;
        }

        @keyframes ticker {
            0% {
                transform: translate3d(0, 0, 0);
            }

            100% {
                transform: translate3d(-100%, 0, 0);
            }
        }
    </style>
</head>

<body class="h-screen w-screen overflow-hidden text-gray-800" x-data="sliderApp()" x-init="initSlider()">

    <!-- Main Container -->
    <div class="h-full w-full flex flex-col relative bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('images/bg-monitoring.jpg') }}');">

        <!-- Overlay backdrop -->
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-[2px]"></div>

        <!-- Header -->
        <header
            class="relative z-10 glass-panel border-b border-white/20 p-4 shrink-0 flex justify-between items-center bg-white/80">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo-kab-bandung.png') }}"
                    onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Lambang_Kabupaten_Bandung%2C_Jawa_Barat%2C_Indonesia.svg/1200px-Lambang_Kabupaten_Bandung%2C_Jawa_Barat%2C_Indonesia.svg.png'"
                    alt="Logo" class="h-16 w-auto drop-shadow-md">
                <div>
                    <h1 class="text-3xl font-extrabold text-bedas-800 tracking-tight">Display Monitoring SI-AKIK</h1>
                    <p class="text-bedas-600 font-medium text-lg">Pemerintah Kecamatan Pasirjambu Kabupaten Bandung -
                        Tahun Anggaran
                        {{ session('tahun_anggaran', date('Y')) }}
                    </p>
                </div>
            </div>
            <div class="flex flex-col items-end gap-1">
                <div class="text-3xl font-extrabold text-gray-800 tabular-nums animate-pulse" x-text="currentTime">
                </div>
                <div class="text-lg font-semibold text-gray-600" x-text="currentDate"></div>
                <a href="{{ route('dashboard') }}"
                    class="text-xs text-bedas-600 mt-1 hover:underline font-semibold bg-white/50 px-3 py-1 rounded-full">
                    < Kembali ke Dashboard</a>
            </div>
        </header>

        <!-- Slides Container -->
        <main class="relative z-10 flex-grow p-8 overflow-hidden flex items-center justify-center">

            <!-- Slide 1: Ringkasan Utama -->
            <div x-show="currentSlide === 0" x-transition:enter="transition ease-out duration-1000 transform"
                x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-1000 transform"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-full"
                class="absolute inset-8 flex flex-col gap-8 justify-center">

                <h2 class="text-4xl font-extrabold text-white drop-shadow-lg text-center mb-4">Ringkasan Anggaran Utama
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 h-full items-center">
                    <!-- Total Anggaran -->
                    <div
                        class="glass-panel p-8 rounded-3xl h-80 flex flex-col justify-center items-center transform hover:scale-105 transition-transform duration-500 bg-white/90">
                        <div class="bg-blue-100 text-blue-600 p-6 rounded-2xl mb-6 shadow-inner">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-xl font-bold text-gray-500 uppercase tracking-widest mb-2">Total Anggaran</p>
                        <h3 class="text-5xl font-black text-gray-800">Rp
                            {{ number_format($totalAnggaran, 0, ',', '.') }}
                        </h3>
                    </div>

                    <!-- Total Realisasi -->
                    <div
                        class="glass-panel p-8 rounded-3xl h-80 flex flex-col justify-center items-center transform hover:scale-105 transition-transform duration-500 bg-white/90">
                        <div class="bg-green-100 text-green-600 p-6 rounded-2xl mb-6 shadow-inner relative">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div
                                class="absolute -top-3 -right-6 bg-green-500 text-white text-lg font-bold px-3 py-1 rounded-full shadow-lg">
                                {{ $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <p class="text-xl font-bold text-gray-500 uppercase tracking-widest mb-2">Total Realisasi</p>
                        <h3 class="text-5xl font-black text-gray-800">Rp
                            {{ number_format($totalRealisasi, 0, ',', '.') }}
                        </h3>
                    </div>

                    <!-- Sisa Kas -->
                    <div
                        class="glass-panel p-8 rounded-3xl h-80 flex flex-col justify-center items-center transform hover:scale-105 transition-transform duration-500 bg-white/90">
                        <div class="bg-amber-100 text-amber-600 p-6 rounded-2xl mb-6 shadow-inner">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3">
                                </path>
                            </svg>
                        </div>
                        <p class="text-xl font-bold text-gray-500 uppercase tracking-widest mb-2">Sisa Kas</p>
                        <h3 class="text-5xl font-black text-gray-800">Rp {{ number_format($sisaKas, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            <!-- Slide 2: Realisasi by Program -->
            <div x-show="currentSlide === 1" x-transition:enter="transition ease-out duration-1000 transform"
                x-transition:enter-start="opacity-0 translate-y-full flex"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-1000 transform"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-full" class="absolute inset-8 flex flex-col">

                <h2 class="text-4xl font-extrabold text-white drop-shadow-lg text-center mb-8">Realisasi per Program
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 auto-rows-max overflow-hidden h-full">
                    @forelse($realisasiPerProgram->take(6) as $item)
                        <div
                            class="glass-panel rounded-2xl p-6 flex flex-col justify-between bg-white/95 border-b-4 {{ $item['persentase'] >= 80 ? 'border-b-green-500' : ($item['persentase'] >= 50 ? 'border-b-blue-500' : 'border-b-amber-500') }}">
                            <div>
                                <div class="flex justify-between items-start mb-3">
                                    <span
                                        class="bg-gray-100 text-gray-600 py-1 px-2 rounded-md font-mono text-sm font-semibold tracking-wider">{{ $item['kode'] }}</span>
                                    <span
                                        class="text-2xl font-black {{ $item['persentase'] >= 80 ? 'text-green-500' : ($item['persentase'] >= 50 ? 'text-blue-500' : 'text-amber-500') }}">{{ number_format($item['persentase'], 1, ',', '.') }}%</span>
                                </div>
                                <h4 class="text-xl font-bold text-gray-800 mb-4 line-clamp-3 overflow-hidden text-ellipsis">
                                    {{ $item['uraian'] }}
                                </h4>
                            </div>

                            <div class="pt-4 border-t border-gray-100 mt-auto">
                                <div class="w-full bg-gray-200 rounded-full h-4 mb-4 overflow-hidden shadow-inner">
                                    <div class="h-4 rounded-full {{ $item['persentase'] >= 80 ? 'bg-gradient-to-r from-green-400 to-green-600' : ($item['persentase'] >= 50 ? 'bg-gradient-to-r from-blue-400 to-blue-600' : 'bg-gradient-to-r from-amber-400 to-amber-500') }} transition-all duration-1000 relative overflow-hidden"
                                        style="width: {{ min($item['persentase'], 100) }}%">
                                        <div class="absolute top-0 left-0 bottom-0 right-0"
                                            style="background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%); animation: shimmer 2s infinite;">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1 text-sm text-gray-500">
                                    <div class="flex justify-between">
                                        <span>Realisasi</span>
                                        <span class="font-bold text-gray-800 text-lg">Rp
                                            {{ number_format($item['realisasi'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Pagu</span>
                                        <span class="font-bold text-gray-800">Rp
                                            {{ number_format($item['pagu'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-white text-2xl text-center py-20 font-light">Tidak ada data program.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Slide 3: Charts (Pertumbuhan & Jenis) -->
            <div x-show="currentSlide === 2" x-transition:enter="transition ease-out duration-1000 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-1000 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-105"
                class="absolute inset-8 flex flex-col gap-6">

                <h2 class="text-4xl font-extrabold text-white drop-shadow-lg text-center mb-2">Statistik Kuitansi &
                    Pencairan</h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 h-full">
                    <!-- Chart 1 -->
                    <div class="glass-panel bg-white/95 rounded-3xl p-6 flex flex-col h-[500px]">
                        <h3 class="text-2xl font-bold text-gray-700 mb-6 flex items-center gap-2">
                            <svg class="w-8 h-8 text-bedas-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                                </path>
                            </svg>
                            Tren Jumlah Kuitansi per Bulan
                        </h3>
                        <div class="relative flex-grow pointer-events-none">
                            <canvas id="displayKuitansiChart"></canvas>
                        </div>
                    </div>

                    <!-- Chart 2 -->
                    <div class="glass-panel bg-white/95 rounded-3xl p-6 flex flex-col h-[500px]">
                        <h3 class="text-2xl font-bold text-gray-700 mb-6 flex items-center gap-2">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Realisasi per Jenis Pencairan
                        </h3>
                        <div class="relative flex-grow pointer-events-none">
                            <canvas id="displayJenisChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4: Realisasi Pajak -->
            <div x-show="currentSlide === 3" x-transition:enter="transition ease-out duration-1000 transform"
                x-transition:enter-start="opacity-0 translate-x-full rotate-y-12"
                x-transition:enter-end="opacity-100 translate-x-0 rotate-y-0"
                x-transition:leave="transition ease-in duration-1000 transform"
                x-transition:leave-start="opacity-100 translate-x-0 rotate-y-0"
                x-transition:leave-end="opacity-0 -translate-x-full -rotate-y-12"
                class="absolute inset-8 flex flex-col gap-8 justify-center items-center">

                <h2 class="text-4xl font-extrabold text-white drop-shadow-lg text-center mb-4">Rekapitulasi Realisasi
                    Pajak</h2>

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-8 w-full max-w-6xl">
                    @foreach($rekapPajak as $jenisPajak => $totalPajak)
                        <div
                            class="glass-panel p-8 rounded-3xl flex flex-col items-center justify-center text-center transform hover:scale-110 transition-all duration-300 bg-white/95 border-t-8 border-bedas-500 shadow-2xl">
                            <span
                                class="text-2xl font-bold text-gray-500 uppercase tracking-widest mb-4">{{ $jenisPajak }}</span>
                            <span class="text-4xl font-black text-gray-800 tracking-tight">Rp
                                {{ number_format($totalPajak, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Indicators / Controls -->
            <div class="absolute bottom-8 left-0 right-0 flex justify-center gap-3 z-20">
                <template x-for="i in 4" :key="i">
                    <button @click="changeSlide(i-1); resetTimer()" class="h-3 rounded-full transition-all duration-500"
                        :class="currentSlide === (i-1) ? 'w-12 bg-bedas-500 shadow-lg' : 'w-3 bg-white/50 hover:bg-white/80'">
                    </button>
                </template>
            </div>
        </main>

        <!-- Footer / Ticker -->
        <footer class="relative z-20 mt-auto shrink-0 border-t border-white/20 shadow-2xl">
            <div class="ticker-wrap h-14 flex items-center">
                <div class="ticker text-2xl font-semibold tracking-wide">
                    <span class="ticker__item">SI-AKIK (Sistem Informasi Administrasi Kuitansi Keuangan)</span>
                    <span class="ticker__item">|</span>
                    <span class="ticker__item text-bedas-300">Total Anggaran: Rp
                        {{ number_format($totalAnggaran, 0, ',', '.') }}</span>
                    <span class="ticker__item">|</span>
                    <span class="ticker__item text-green-300">Realisasi: Rp
                        {{ number_format($totalRealisasi, 0, ',', '.') }}
                        ({{ $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 1) : 0 }}%)</span>
                    <span class="ticker__item">|</span>
                    <span class="ticker__item text-amber-300">Sisa Kas: Rp
                        {{ number_format($sisaKas, 0, ',', '.') }}</span>
                    <span class="ticker__item">|</span>
                    <span class="ticker__item">Pemerintah Kecamatan Pasirjambu Kabupaten Bandung BEDAS!</span>
                </div>
            </div>
        </footer>

    </div>

    <style>
        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }
    </style>

    <script>
        function sliderApp() {
            return {
                currentSlide: 0,
                totalSlides: 4,
                currentTime: '',
                currentDate: '',
                timer: null,

                initSlider() {
                    // Start clock
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);

                    // Render charts after a small delay to ensure DOM is ready
                    setTimeout(() => {
                        this.initCharts();
                    }, 500);

                    // Start auto slider
                    this.startTimer();
                },

                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
                    this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                },

                startTimer() {
                    this.timer = setInterval(() => {
                        this.nextSlide();
                    }, 12000); // 12 seconds per slide
                },

                resetTimer() {
                    clearInterval(this.timer);
                    this.startTimer();
                },

                changeSlide(index) {
                    this.currentSlide = index;
                },

                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                },

                initCharts() {
                    // Chart 1: Kuitansi Line
                    const kCtx = document.getElementById('displayKuitansiChart');
                    if (kCtx) {
                        const ctx = kCtx.getContext('2d');
                        const grad = ctx.createLinearGradient(0, 0, 0, 400);
                        grad.addColorStop(0, 'rgba(34, 197, 94, 0.4)');
                        grad.addColorStop(1, 'rgba(34, 197, 94, 0.0)');

                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                                datasets: [{
                                    label: 'Kuitansi',
                                    data: @json($grafikKuitansi),
                                    borderColor: '#22c55e',
                                    backgroundColor: grad,
                                    borderWidth: 4,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: '#22c55e',
                                    pointRadius: 6,
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: { duration: 2000, easing: 'easeOutQuart' },
                                scales: {
                                    y: { ticks: { stepSize: 1, font: { size: 14 } } },
                                    x: { ticks: { font: { size: 14 } } }
                                },
                                plugins: { legend: { display: false } }
                            }
                        });
                    }

                    // Chart 2: Jenis Pencairan
                    const jCtxElement = document.getElementById('displayJenisChart');
                    if (jCtxElement) {
                        const jCtx = jCtxElement.getContext('2d');
                        const rData = @json($realisasiPerJenis);
                        const dataVals = rData.map(item => item.total);
                        const colors = ['#3b82f6', '#8b5cf6', '#ef4444', '#f59e0b', '#10b981', '#ec4899'];

                        new Chart(jCtx, {
                            type: 'bar',
                            data: {
                                labels: rData.map(item => item.jenis),
                                datasets: [{
                                    data: dataVals,
                                    backgroundColor: colors.slice(0, dataVals.length).map(c => c + 'E6'),
                                    borderRadius: 8,
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: { duration: 2000, easing: 'easeOutBounce' },
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function (val) {
                                                if (val >= 1000000000) return 'Rp ' + (val / 1000000000).toFixed(1) + 'M';
                                                if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'Jt';
                                                return val;
                                            },
                                            font: { size: 14 }
                                        }
                                    },
                                    x: { ticks: { font: { size: 14 } } }
                                },
                                plugins: { legend: { display: false } }
                            }
                        });
                    }
                }
            }
        }
    </script>
</body>

</html>