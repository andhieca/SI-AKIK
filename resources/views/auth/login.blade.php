<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SI-AKIK</title>
    <link rel="icon" type="image/png" href="https://siakik.kecbandungkab.com/public/images/logo-kab-bandung.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script>
        const originalWarn = console.warn;
        console.warn = (...args) => {
            if (typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com should not be used in production')) return;
            originalWarn(...args);
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
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
                        'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                        'fade-in-right': 'fadeInRight 0.8s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeInRight: {
                            '0%': { opacity: '0', transform: 'translateX(-30px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-15px)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .text-gradient {
            background: linear-gradient(135deg, #166534 0%, #22c55e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen font-sans text-gray-800 flex overflow-hidden lg:overflow-auto">

    <!-- Split Layout Container -->
    <div class="flex w-full min-h-screen relative overflow-hidden">

        <!-- Left Side: Landing Content (Hero Section) -->
        <div class="hidden lg:flex lg:w-3/5 relative flex-col justify-center p-16 xl:p-24 overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="https://siakik.kecbandungkab.com/public/images/bg-login.png" alt="Financial Background"
                    class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-tr from-bedas-900/90 via-bedas-800/40 to-transparent"></div>
            </div>

            <!-- Content Over Image -->
            <div class="relative z-10 max-w-2xl animate-fade-in-right">
                <div
                    class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-bold uppercase tracking-widest mb-8">
                    <span class="flex h-2 w-2 rounded-full bg-green-400 mr-2 animate-pulse"></span>
                    Sistem Kuitansi Keuangan Pasirjambu
                </div>

                <h1 class="text-5xl xl:text-7xl font-outfit font-extrabold text-white leading-tight mb-6">
                    Sistem <br> <span class="text-bedas-300">Pengelolaan</span> <br> Kuitansi Keuangan
                </h1>

                <p class="text-xl text-bedas-100/90 mb-10 leading-relaxed font-light">
                    Efisiensi, Transparansi, dan Akuntabilitas dalam satu genggaman. Tingkatkan produktivitas kerja
                    dengan sistem pengelolaan Kuitansi yang modern dan terintegrasi.
                </p>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
                    <div
                        class="flex items-start p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 group">
                        <div class="text-3xl mr-4 group-hover:scale-110 transition-transform duration-300">📂</div>
                        <div>
                            <h3 class="text-white font-bold text-lg mb-1">Kelola Kuitansi</h3>
                            <p class="text-bedas-200/70 text-sm">Simpan dan kelola Kuitansi secara Efektif dan Efesien.
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex items-start p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 group">
                        <div class="text-3xl mr-4 group-hover:scale-110 transition-transform duration-300">📊</div>
                        <div>
                            <h3 class="text-white font-bold text-lg mb-1">Monitoring</h3>
                            <p class="text-bedas-200/70 text-sm">Pantau penggunaan anggaran secara real-time.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Brand -->
            <div class="absolute bottom-12 left-12 xl:left-24 z-10 flex items-center space-x-3 text-white/50 text-sm">
                <div class="h-px w-8 bg-white/20"></div>
                <span>Pemerintah Kecamatan Pasirjambu © 2026</span>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-2/5 flex flex-col justify-center items-center bg-white p-8 md:p-12 relative">

            <!-- Mobile Background (Only visible on small screens) -->
            <div class="lg:hidden absolute inset-0 z-0">
                <img src="https://siakik.kecbandungkab.com/public/images/bg-login.png" alt="Financial Background"
                    class="w-full h-full object-cover opacity-10">
                <div class="absolute inset-0 bg-gradient-to-b from-white via-white/80 to-white"></div>
            </div>

            <!-- Floating Decoration Shapes (Logic Side) -->
            <div class="absolute top-0 right-0 -mr-24 -mt-24 w-64 h-64 bg-bedas-50 rounded-full blur-3xl opacity-50">
            </div>
            <div class="absolute bottom-0 left-0 -ml-24 -mb-24 w-64 h-64 bg-green-50 rounded-full blur-3xl opacity-50">
            </div>

            <div class="w-full max-w-sm relative z-10 animate-fade-in-up">
                <!-- Branding -->
                <div class="text-center mb-10">
                    <div class="flex justify-center items-center gap-6 mb-6">
                        <div class="transform transition-transform hover:scale-110">
                            <img src="https://siakik.kecbandungkab.com/public/images/logo-kab-bandung.png" alt="Logo Kab Bandung"
                                class="w-24 h-auto drop-shadow-xl">
                        </div>
                        <div class="w-1 h-16 bg-gray-200 rounded-full"></div>
                        <div class="transform transition-transform hover:scale-110 pl-2">
                            <img src="{{ asset('public/images/logo-si-akik.png') }}" alt="Logo SI-AKIK"
                                class="w-28 h-auto drop-shadow-xl">
                        </div>
                    </div>
                    <h2 class="text-4xl font-outfit font-extrabold text-gray-900 tracking-tight">SI-AKIK</h2>
                    <p class="text-gray-500 mt-2 font-medium">Sistem Informasi Administrasi Kuitansi Keuangan</p>
                </div>

                <!-- Error Alert -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg animate-fade-in-up">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 font-medium">{{ $errors->first('email') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Tahun Anggaran -->
                    <div x-data="{ focused: false }">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 ml-1"
                            :class="focused ? 'text-bedas-600' : ''">Tahun Anggaran</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-hover:text-bedas-500 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <select name="tahun" id="tahun" @focus="focused = true" @blur="focused = false"
                                class="block w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-bedas-500 focus:bg-white transition-all duration-300 appearance-none shadow-sm group-hover:shadow">
                                @for ($i = date('Y') + 1; $i >= date('Y') - 4; $i--)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div x-data="{ focused: false }">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 ml-1"
                            :class="focused ? 'text-bedas-600' : ''">Email Address</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-hover:text-bedas-500 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" @focus="focused = true" @blur="focused = false"
                                class="block w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-bedas-500 focus:bg-white transition-all duration-300 shadow-sm group-hover:shadow"
                                placeholder="nama@email.com" required>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div x-data="{ focused: false, show: false }">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 ml-1"
                            :class="focused ? 'text-bedas-600' : ''">Password</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-hover:text-bedas-500 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input :type="show ? 'text' : 'password'" name="password" id="password"
                                @focus="focused = true" @blur="focused = false"
                                class="block w-full pl-12 pr-12 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-bedas-500 focus:bg-white transition-all duration-300 shadow-sm group-hover:shadow"
                                placeholder="••••••••" required>
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-300 hover:text-bedas-500 focus:outline-none transition-colors">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full flex justify-center py-4 px-6 border border-transparent rounded-2xl shadow-xl text-lg font-bold text-white bg-gradient-to-r from-bedas-600 to-bedas-700 hover:from-bedas-700 hover:to-bedas-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bedas-500 transition-all duration-300 transform hover:-translate-y-1 active:scale-[0.98]">
                            Login &rarr;
                        </button>
                    </div>
                </form>
            </div>

            <!-- Mobile Footer Only -->
            <div class="lg:hidden mt-12 text-center relative z-10">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Pemerintah Kecamatan Pasirjambu
                    © {{ date('Y') }}</p>
            </div>
        </div>
    </div>

</body>

</html>