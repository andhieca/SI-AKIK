@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8">
        <!-- Verification Alert -->
        @if(isset($verificationResult))
            <div x-data="{ show: true }" x-show="show"
                class="rounded-xl shadow-lg border-l-4 p-6 relative {{ $verificationResult ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500' }}">
                <button @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                @if($verificationResult)
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-green-800">Kwitansi Sah & Terverifikasi</h3>
                            <div class="mt-2 text-sm text-green-700 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1">
                                <p><span class="font-semibold">No. Bukti:</span> {{ $verificationResult->no_bukti }}</p>
                                <p><span class="font-semibold">Tanggal:</span> {{ $verificationResult->tanggal }}</p>
                                <p><span class="font-semibold">Nominal:</span> Rp
                                    {{ number_format($verificationResult->nominal, 0, ',', '.') }}</p>
                                <p><span class="font-semibold">Status:</span>
                                    {{ $verificationResult->status_cetak ? 'Sudah Dicetak' : 'Belum Dicetak' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-red-800">Peringatan Keamanan</h3>
                            <p class="text-red-700 mt-1">QR Code tidak valid atau data tidak ditemukan dalam database kami.</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Welcome Section -->
        <div
            class="bg-gradient-to-r from-bedas-600 to-bedas-800 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1/2 bg-white opacity-5 transform skew-x-12 translate-x-12"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-extrabold">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-mono border border-white/20 backdrop-blur-sm">
                        Tahun Anggaran: {{ session('tahun_anggaran', date('Y')) }}
                    </span>
                </div>
                <p class="text-bedas-100 text-lg">Sistem Informasi Administrasi Kuitansi Keuangan (SI-AKIK)</p>
            </div>
        </div>

        <!-- Stats Grid -->
        @if(auth()->user()->role === 'pptk')
            <!-- PPTK Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kuitansi Tervalidasi Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-lg text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Kuitansi Tervalidasi ({{ session('tahun_anggaran', date('Y')) }})</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $validatedCount ?? 0 }} Data</h3>
                </div>

                <!-- Kuitansi Belum Validasi Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition duration-300 relative overflow-hidden">
                    @if(($unvalidatedCount ?? 0) > 0)
                        <div class="absolute top-0 right-0 w-16 h-16 pointer-events-none">
                            <div class="absolute transform rotate-45 bg-red-500 text-white text-xs font-bold py-1 right-[-35px] top-[15px] w-[120px] text-center shadow-sm">
                                Perlu Aksi
                            </div>
                        </div>
                    @endif
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-amber-100 p-3 rounded-lg text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Kuitansi Menunggu Validasi</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $unvalidatedCount ?? 0 }} Data</h3>
                </div>
            </div>
        @else
            <!-- Admin / Global Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Anggaran Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">+100%</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Anggaran ({{ session('tahun_anggaran', date('Y')) }})</p>
                    <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</h3>
                </div>

                <!-- Realisasi Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-lg text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span
                            class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">{{ $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 1) : 0 }}%</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Realisasi</p>
                    <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</h3>
                </div>

                <!-- Sisa Kas Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-amber-100 p-3 rounded-lg text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Sisa Kas</p>
                    <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($sisaKas, 0, ',', '.') }}</h3>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Budget Chart Section -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Ringkasan Keuangan</h3>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="budgetChart"></canvas>
                </div>
            </div>

            <!-- Jenis Pencairan Chart Section / Nominal Info for PPTK -->
            @if(auth()->user()->role === 'pptk')
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col justify-center items-center relative overflow-hidden h-full">
                    <div class="absolute -right-10 -top-10 bg-amber-50 w-32 h-32 rounded-full opacity-50"></div>
                    <div class="absolute -bottom-10 -left-10 bg-red-50 w-40 h-40 rounded-full opacity-50"></div>
                    <div class="z-10 text-center w-full">
                        <div class="bg-amber-100 p-4 rounded-full inline-block mb-4 text-amber-600 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Anggaran Kegiatan</h3>
                        <p class="text-4xl font-extrabold text-red-600 tracking-tight">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 mt-4">Total nominal anggaran.</p>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Realisasi per Jenis Pencairan</h3>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="jenisPencairanChart"></canvas>
                    </div>
                </div>
            @endif
        </div>

        <!-- Rekap Pajak Section -->
        <div class="mt-8 bg-white rounded-xl shadow-sm p-6 border border-gray-100"
             x-data="{ 
                 showPajakModal: false, 
                 showCetakPajakModal: false,
                 selectedPajakTitle: '', 
                 selectedPajakList: [],
                 pajakData: {{ json_encode($detailPajak) }},
                 openDetailModal(pajakType) {
                     this.selectedPajakTitle = pajakType;
                     this.selectedPajakList = this.pajakData[pajakType] || [];
                     this.showPajakModal = true;
                 }
             }">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-800">Rekapitulasi Realisasi Pajak</h3>
                <button @click="showCetakPajakModal = true" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Laporan Pajak
                </button>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
                @foreach($rekapPajak as $jenisPajak => $totalPajak)
                    <div @click="openDetailModal('{{ $jenisPajak }}')" class="bg-blue-50 border border-blue-100 hover:bg-blue-100 cursor-pointer p-4 rounded-xl flex flex-col items-center justify-center text-center shadow-sm transition-colors duration-200">
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">{{ $jenisPajak }}</span>
                        <span class="text-base font-extrabold text-gray-800">Rp {{ number_format($totalPajak, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-blue-400 mt-1">Klik untuk detail</span>
                    </div>
                @endforeach
            </div>

            <!-- Modal Detail Pajak -->
            <div x-show="showPajakModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" id="pajakModal">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showPajakModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="fixed inset-0 transition-opacity" aria-hidden="true"
                        @click="showPajakModal = false">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showPajakModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" x-text="'Detail Transaksi - ' + selectedPajakTitle"></h3>
                                <button @click="showPajakModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none transition">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="bg-white p-0 sm:p-6 max-h-[60vh] overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Bukti</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal Pajak</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="item in selectedPajakList" :key="item.no_bukti">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="new Date(item.tanggal).toLocaleDateString('id-ID')"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="item.no_bukti"></td>
                                            <td class="px-6 py-4 text-sm text-gray-500" x-text="item.uraian"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.nominal_pajak)"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="selectedPajakList.length === 0">
                                        <td colspan="4" class="px-6 py-10 whitespace-nowrap text-sm text-center text-gray-500 italic">
                                            Tidak ada transaksi untuk jenis pajak ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-xl border-t border-gray-100">
                            <button @click="showPajakModal = false" type="button" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bedas-500 transition sm:ml-3 sm:w-auto sm:text-sm">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Cetak Laporan Pajak -->
            <div x-show="showCetakPajakModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" id="cetakPajakModal">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showCetakPajakModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="fixed inset-0 transition-opacity" aria-hidden="true"
                        @click="showCetakPajakModal = false">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showCetakPajakModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="{{ route('bku.cetak_pajak') }}" method="GET" target="_blank">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900">Cetak Laporan Rekap Pajak</h3>
                                    <button type="button" @click="showCetakPajakModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none transition">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="bg-white p-4 sm:p-6 space-y-4">
                                <input type="hidden" name="year" value="{{ session('tahun_anggaran', date('Y')) }}">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Bulan</label>
                                    <select name="bulan" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-bedas-500 focus:border-bedas-500">
                                        <option value="all">Semua Bulan</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Jenis Pencairan</label>
                                    <select name="jenis_pencairan" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-bedas-500 focus:border-bedas-500">
                                        <option value="all">Semua Jenis Pencairan</option>
                                        <option value="UP">Uang Persediaan (UP)</option>
                                        <option value="GU 1">Ganti Uang (GU) 1</option>
                                        <option value="GU 2">Ganti Uang (GU) 2</option>
                                        <option value="GU 3">Ganti Uang (GU) 3</option>
                                        <option value="GU 4">Ganti Uang (GU) 4</option>
                                        <option value="GU 5">Ganti Uang (GU) 5</option>
                                        <option value="GU 6">Ganti Uang (GU) 6</option>
                                        <option value="GU 7">Ganti Uang (GU) 7</option>
                                        <option value="GU 8">Ganti Uang (GU) 8</option>
                                        <option value="GU 9">Ganti Uang (GU) 9</option>
                                        <option value="LS">Langsung (LS)</option>
                                        <option value="TU">Tambah Uang (TU)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-xl border-t border-gray-100">
                                <button type="submit" @click="showCetakPajakModal = false" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition sm:ml-3 sm:w-auto sm:text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    Cetak PDF
                                </button>
                                <button type="button" @click="showCetakPajakModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bedas-500 transition sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->role !== 'pptk')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
            <!-- Recent Transactions -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Transaksi Terbaru</h3>
                    <a href="{{ route('bku.index') }}"
                        class="text-sm text-bedas-600 hover:text-bedas-800 font-semibold hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th
                                    class="py-3 px-4 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider rounded-tl-lg rounded-bl-lg">
                                    Tanggal</th>
                                <th
                                    class="py-3 px-4 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Uraian</th>
                                <th
                                    class="py-3 px-4 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right rounded-tr-lg rounded-br-lg">
                                    Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($latestTransaksis as $transaksi)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="py-3 px-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-800 font-medium">
                                        {{ Str::limit($transaksi->uraian, 25) }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-800 font-bold text-right">Rp
                                        {{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-gray-400 text-sm">Belum ada data transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kuitansi Chart Section -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Tren Pembuatan Kuitansi</h3>
                </div>
                <div class="relative flex-grow w-full min-h-[300px]">
                    <canvas id="kuitansiChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 mt-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Status Validasi Kuitansi per PPTK</h3>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider rounded-tl-lg rounded-bl-lg">Nama PPTK</th>
                                <th class="py-3 px-4 bg-green-50 text-xs font-semibold text-green-700 uppercase tracking-wider text-center">Sudah Validasi</th>
                                <th class="py-3 px-4 bg-red-50 text-xs font-semibold text-red-700 uppercase tracking-wider text-center">Belum Validasi</th>
                                <th class="py-3 px-4 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center rounded-tr-lg rounded-br-lg">Total Kuitansi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($statsPerPptk as $stat)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="py-4 px-4 text-sm text-gray-800 font-bold">{{ $stat->nama }}</td>
                                    <td class="py-4 px-4 text-sm font-bold text-center text-green-600">
                                        <span class="bg-green-100 px-3 py-1 rounded-full shadow-sm">{{ $stat->validated_count }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-sm font-bold text-center text-red-500">
                                        @if($stat->unvalidated_count > 0)
                                            <span class="bg-red-100 px-3 py-1 rounded-full shadow-sm border border-red-200">{{ $stat->unvalidated_count }}</span>
                                        @else
                                            <span class="text-gray-400">0</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-sm font-bold text-center text-gray-600">{{ $stat->total_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400 text-sm">Belum ada data kuitansi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Realization per Program Section (Admin Only) -->
        @if(auth()->user()->role !== 'pptk')
            <div class="mb-4 flex justify-between items-center mt-12">
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Realisasi per Program</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($realisasiPerProgram as $item)
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition duration-300 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-blue-50 text-blue-700 py-1 px-3 rounded-lg font-mono text-xs font-semibold tracking-wider border border-blue-100">{{ $item['kode'] }}</span>
                                <span class="text-xl font-extrabold {{ $item['persentase'] >= 80 ? 'text-green-500' : ($item['persentase'] >= 50 ? 'text-blue-500' : 'text-amber-500') }}">{{ number_format($item['persentase'], 1, ',', '.') }}%</span>
                            </div>
                            <h4 class="text-base font-bold text-gray-800 mb-6 leading-tight min-h-[40px] line-clamp-2" title="{{ $item['uraian'] }}">{{ $item['uraian'] }}</h4>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-50 mt-auto">
                            <div class="w-full bg-gray-100 rounded-full h-3 mb-4 overflow-hidden">
                                <div class="h-3 rounded-full {{ $item['persentase'] >= 80 ? 'bg-gradient-to-r from-green-400 to-green-500' : ($item['persentase'] >= 50 ? 'bg-gradient-to-r from-blue-400 to-blue-500' : 'bg-gradient-to-r from-amber-400 to-amber-500') }} transition-all duration-1000 ease-in-out" style="width: {{ min($item['persentase'], 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-500 mb-2">
                                <span>Realisasi</span>
                                <span class="font-bold text-gray-800">Rp {{ number_format($item['realisasi'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>Pagu</span>
                                <span class="font-bold text-gray-800">Rp {{ number_format($item['pagu'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl shadow-sm p-12 border border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400">
                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <p class="text-xl font-medium text-gray-500 mb-1">Belum ada data program</p>
                        <p class="text-sm">Silakan tambahkan data anggaran terlebih dahulu</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('budgetChart').getContext('2d');
        const isPptk = {{ auth()->user()->role === 'pptk' ? 'true' : 'false' }};
        const chartLabels = isPptk ? ['Tervalidasi', 'Belum Validasi'] : ['Terpakai (Realisasi)', 'Sisa Anggaran'];
        const chartData = isPptk ? [{{ $validatedNominal ?? 0 }}, {{ $unvalidatedNominal ?? 0 }}] : [{{ $totalRealisasi }}, {{ $sisaKas }}];
        const chartColors = isPptk ? ['#16a34a', '#f59e0b'] : ['#16a34a', '#dcfce7'];

        const centerTextPlugin = {
            id: 'centerText',
            beforeDraw: function(chart) {
                var width = chart.width,
                    height = chart.height,
                    ctx = chart.ctx;

                ctx.restore();
                var fontSize = (height / 120).toFixed(2);
                ctx.font = 'bold ' + fontSize + "em 'Inter', sans-serif";
                ctx.textBaseline = "middle";
                ctx.fillStyle = "#1f2937";

                var data = chart.data.datasets[0].data;
                var total = data.reduce((a, b) => Number(a) + Number(b), 0);
                var percentage = "0%";
                if (total > 0) {
                    percentage = (Number(data[0]) / total * 100).toFixed(1) + "%";
                }

                var text = percentage,
                    textX = Math.round((width - ctx.measureText(text).width) / 2),
                    textY = height / 2;

                ctx.fillText(text, textX, textY);
                
                ctx.font = '600 ' + (fontSize/3.5).toFixed(2) + "em 'Inter', sans-serif";
                ctx.fillStyle = "#6b7280";
                var labelText = isPptk ? "Tervalidasi" : "Realisasi";
                var labelTextX = Math.round((width - ctx.measureText(labelText).width) / 2);
                ctx.fillText(labelText, labelTextX, textY + (height/10));

                ctx.save();
            }
        };

        const budgetChart = new Chart(ctx, {
            type: 'doughnut',
            plugins: [centerTextPlugin],
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartData,
                    backgroundColor: chartColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '75%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'Inter', sans-serif" }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    const total = context.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) + '%' : '0%';
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed) + ' (' + percentage + ')';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Jenis Pencairan Chart
        const jpCtx = document.getElementById('jenisPencairanChart').getContext('2d');
        const realisasiData = @json($realisasiPerJenis);
        
        const labels = realisasiData.map(item => item.jenis);
        const data = realisasiData.map(item => item.total);

        // Generate dynamic modern colors
        const colors = [
            '#3b82f6', // blue-500
            '#8b5cf6', // violet-500
            '#ef4444', // red-500
            '#f59e0b', // amber-500
            '#10b981', // emerald-500
            '#ec4899', // pink-500
            '#06b6d4', // cyan-500
        ];

        new Chart(jpCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Realisasi (Rp)',
                    data: data,
                    backgroundColor: colors.slice(0, data.length).map(c => c + 'CC'), // Add transparency
                    borderColor: colors.slice(0, data.length),
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6', // gray-100
                            drawBorder: false,
                        },
                        ticks: {
                            callback: function(value) {
                                if(value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + 'M';
                                if(value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                                return 'Rp ' + value;
                            },
                            font: { family: "'Inter', sans-serif", size: 10 }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            font: { family: "'Inter', sans-serif" }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Kuitansi Line Chart
        const kuiCtx = document.getElementById('kuitansiChart').getContext('2d');
        
        const gradientKui = kuiCtx.createLinearGradient(0, 0, 0, 300);
        gradientKui.addColorStop(0, 'rgba(16, 185, 129, 0.5)'); // emerald-500 fading out
        gradientKui.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        new Chart(kuiCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Kuitansi',
                    data: @json($grafikKuitansi),
                    borderColor: '#10b981', // emerald-500
                    backgroundColor: gradientKui,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false,
                        },
                        ticks: {
                            stepSize: 1, // Since it's count, integer steps
                            font: { family: "'Inter', sans-serif", size: 10 }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            font: { family: "'Inter', sans-serif" }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleFont: { family: "'Inter', sans-serif", size: 13 },
                        bodyFont: { family: "'Inter', sans-serif", size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                }
            }
        });
    </script>
@endsection