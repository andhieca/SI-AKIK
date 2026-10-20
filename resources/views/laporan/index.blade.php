@extends('layouts.app')

@section('title', 'Laporan Realisasi per Kode Rekening')

@section('content')
<div x-data="{
    showKamus: false,
    searchKamus: '',
    selectedKode: '{{ $selectedKode }}',
    filteredKamus: [],
    allKamus: @js($kamusRekening),
    filterKamus() {
        const s = this.searchKamus.toLowerCase();
        if (!s) {
            this.filteredKamus = this.allKamus;
        } else {
            this.filteredKamus = this.allKamus.filter(item =>
                item.kode.toLowerCase().includes(s) || item.uraian.toLowerCase().includes(s)
            );
        }
    },
    selectKode(kode) {
        this.selectedKode = kode;
        document.getElementById('kode_rekening_input').value = kode;
        this.showKamus = false;
    },
    init() {
        this.filteredKamus = this.allKamus;
    }
}">

    {{-- Flash Messages --}}
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Realisasi Anggaran</h1>
                <p class="text-gray-500 mt-1">Cetak laporan berdasarkan kode rekening dan periode realisasi</p>
            </div>
            <div class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 font-medium flex items-center gap-2 cursor-not-allowed select-none"
                title="Tahun Anggaran Terkunci">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Tahun {{ $tahun }}
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="bg-bedas-100 p-2.5 rounded-xl">
                <svg class="w-5 h-5 text-bedas-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Filter Laporan</h3>
                <p class="text-sm text-gray-500">Pilih kode rekening dan periode untuk melihat realisasi</p>
            </div>
        </div>

        <form action="{{ route('laporan.index') }}" method="GET" id="filterForm">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
                {{-- Kode Rekening Input with Kamus --}}
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Rekening</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" name="kode_rekening" id="kode_rekening_input"
                                x-model="selectedKode"
                                value="{{ $selectedKode }}"
                                placeholder="Pilih atau ketik kode rekening..."
                                list="kodeRekeningDatalist"
                                autocomplete="off"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 focus:ring-opacity-50 transition duration-200 text-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                        </div>
                        <button type="button" @click="showKamus = !showKamus"
                            class="px-3 py-2.5 bg-bedas-50 hover:bg-bedas-100 border border-bedas-300 text-bedas-700 rounded-xl transition duration-200 flex items-center gap-1.5 text-sm font-medium whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Kamus
                        </button>
                    </div>

                    <datalist id="kodeRekeningDatalist">
                        @foreach($kodeRekeningList as $kode)
                            <option value="{{ $kode }}">
                        @endforeach
                    </datalist>
                </div>

                {{-- Periode Awal --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Periode Awal</label>
                    <div class="relative">
                        <input type="date" name="periode_awal" value="{{ $periodeAwal }}"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 focus:ring-opacity-50 transition duration-200 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Periode Akhir --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Periode Akhir</label>
                    <div class="relative">
                        <input type="date" name="periode_akhir" value="{{ $periodeAkhir }}"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 focus:ring-opacity-50 transition duration-200 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit"
                    class="bg-bedas-600 hover:bg-bedas-700 text-white font-bold py-2.5 px-6 rounded-xl flex items-center gap-2 transition duration-200 shadow-md transform hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Tampilkan
                </button>

                @if($hasFilter)
                    <a href="{{ route('laporan.cetak', request()->query()) }}" target="_blank"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl flex items-center gap-2 transition duration-200 shadow-md transform hover:scale-105 active:scale-95 no-loader">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Laporan
                    </a>

                    <a href="{{ route('laporan.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 px-6 rounded-xl flex items-center gap-2 transition duration-200 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Kamus Kode Rekening Modal --}}
    <div x-show="showKamus" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showKamus = false"></div>
        <div class="flex items-start justify-center min-h-screen pt-10 px-4 pb-20">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl z-[60] overflow-hidden transform transition-all"
                x-show="showKamus"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                @click.away="showKamus = false">

                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-bedas-700 to-bedas-800 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Kamus Kode Rekening Belanja</h3>
                            <p class="text-bedas-200 text-sm">Pilih kode rekening belanja untuk laporan</p>
                        </div>
                    </div>
                    <button @click="showKamus = false" class="text-white hover:text-bedas-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Search in Kamus --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <div class="relative">
                        <input type="text" x-model="searchKamus" @input="filterKamus()" placeholder="Cari kode atau uraian rekening belanja..."
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 focus:ring-opacity-50 transition duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <span x-text="filteredKamus.length"></span> kode rekening ditemukan
                    </p>
                </div>

                {{-- Kamus Table --}}
                <div class="max-h-[60vh] overflow-y-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3">Kode Rekening</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Uraian</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-20">Pilih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="item in filteredKamus" :key="item.id">
                                <tr class="hover:bg-bedas-50 transition-colors duration-150 cursor-pointer" @click="selectKode(item.kode)">
                                    <td class="px-6 py-3">
                                        <span class="font-mono text-sm font-semibold text-bedas-700" x-text="item.kode"></span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm text-gray-700" x-text="item.uraian"></span>
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <button type="button" @click.stop="selectKode(item.kode)"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200"
                                            :class="selectedKode === item.kode ? 'bg-bedas-600 text-white shadow-md' : 'bg-gray-100 text-gray-400 hover:bg-bedas-100 hover:text-bedas-600'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div x-show="filteredKamus.length === 0" class="py-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>Tidak ada kode rekening ditemukan</p>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button @click="showKamus = false"
                        class="px-5 py-2.5 bg-bedas-600 hover:bg-bedas-700 text-white font-semibold rounded-xl transition duration-200 shadow-md">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Results Section --}}
    @if($hasFilter)
        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Kode Rekening</span>
                    <div class="bg-blue-100 p-1.5 rounded-lg">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                    </div>
                </div>
                <p class="text-lg font-bold text-gray-800 font-mono">{{ $selectedKode }}</p>
                <p class="text-xs text-gray-500 mt-1 truncate" title="{{ $uraianRekening }}">{{ Str::limit($uraianRekening, 50) }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pagu Anggaran</span>
                    <div class="bg-green-100 p-1.5 rounded-lg">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-lg font-bold text-gray-800">Rp {{ number_format($paguAnggaran, 0, ',', '.') }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Realisasi</span>
                    <div class="bg-amber-100 p-1.5 rounded-lg">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-lg font-bold text-gray-800">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</p>
                @if($paguAnggaran > 0)
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, ($totalRealisasi / $paguAnggaran) * 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format(($totalRealisasi / $paguAnggaran) * 100, 1) }}% terserap</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Sisa Anggaran</span>
                    <div class="{{ ($paguAnggaran - $totalRealisasi) >= 0 ? 'bg-emerald-100' : 'bg-red-100' }} p-1.5 rounded-lg">
                        <svg class="w-4 h-4 {{ ($paguAnggaran - $totalRealisasi) >= 0 ? 'text-emerald-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-lg font-bold {{ ($paguAnggaran - $totalRealisasi) >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                    Rp {{ number_format($paguAnggaran - $totalRealisasi, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Transaction Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Detail Transaksi</h3>
                        <p class="text-sm text-gray-500">{{ $transaksis->count() }} transaksi ditemukan</p>
                    </div>
                </div>
            </div>

            @if($transaksis->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px]">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-10">No</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Bukti</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Uraian</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Penerima</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Nominal</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($transaksis as $index => $trx)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-700 whitespace-nowrap">{{ $trx->tanggal->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3">
                                        <span class="text-sm font-mono font-medium text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{{ $trx->no_bukti }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-700 max-w-xs truncate" title="{{ $trx->uraian }}">{{ Str::limit($trx->uraian, 60) }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-700">{{ $trx->penerima }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-800 font-semibold text-right whitespace-nowrap">Rp {{ number_format($trx->nominal, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-center">
                                        @if($trx->status_validasi)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                                Valid
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="5" class="px-5 py-4 text-sm font-bold text-gray-700 text-right">Total Realisasi:</td>
                                <td class="px-5 py-4 text-sm font-bold text-bedas-700 text-right whitespace-nowrap">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="py-16 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-600 mb-1">Tidak Ada Transaksi</h3>
                    <p class="text-gray-400">Tidak ada transaksi untuk kode rekening <strong>{{ $selectedKode }}</strong> pada periode yang dipilih</p>
                </div>
            @endif
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 py-20 text-center">
            <div class="bg-bedas-50 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-bedas-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Pilih Filter Laporan</h3>
            <p class="text-gray-400 max-w-md mx-auto">Gunakan filter di atas untuk memilih kode rekening dan periode realisasi. Anda juga bisa membuka <strong>Kamus Kode Rekening</strong> untuk melihat daftar lengkap.</p>
        </div>
    @endif
</div>
@endsection
