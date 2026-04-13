@extends('layouts.app')

@section('title', 'Data Buku Kas Umum')

@section('content')
    <div x-data="bkuData()" x-init="initData()">
        <div class="mb-6">
            <form action="{{ route('bku.index') }}" method="GET"
                class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <select name="jenis_pencairan" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200">
                        <option value="all" {{ $selectedJenisPencairan == 'all' ? 'selected' : '' }}>Semua Jenis Pencairan
                        </option>
                        <option value="UP" {{ $selectedJenisPencairan == 'UP' ? 'selected' : '' }}>UP</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="GU {{ $i }}" {{ $selectedJenisPencairan == 'GU ' . $i ? 'selected' : '' }}>GU {{ $i }}
                            </option>
                        @endfor
                    </select>
                    <span class="text-gray-400 mx-1">|</span>

                    <!-- Locked Year Display -->
                    <div class="relative">
                        <div class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 font-medium flex items-center gap-2 cursor-not-allowed select-none"
                            title="Tahun Anggaran Terkunci">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            {{ $selectedYear }}
                        </div>
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto justify-between md:justify-end">
                    <div class="relative w-full md:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari transaksi..."
                            x-on:input.debounce.500ms="$el.form.submit()"
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 focus:ring-opacity-50 transition duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <a href="{{ route('bku.cetak', request()->query()) }}" target="_blank"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-200 shadow-md transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <button type="button" @click="openCreateModal()"
                            class="bg-bedas-600 hover:bg-bedas-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-200 shadow-md transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Input Transaksi
                        </button>

                        <button type="button" @click="openImportModal = true; showModal = false"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-200 shadow-md transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Import Excel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <hr class="border-gray-200 mb-6">

        <!-- Error/Success Alert -->
        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Import Errors/Warnings -->
        @if (session('import_errors'))
            <div class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm" role="alert">
                <p class="font-bold">Perhatian: Ada kesalahan pada beberapa baris data Excel</p>
                <ul class="list-disc pl-5 mt-2 text-sm">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.duration.300ms
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm flex justify-between items-center">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
            </div>
        @endif

        <!-- Card Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        @php
                            $renderSortIcon = function ($columnName) {
                                if (request('sort', 'tanggal') === $columnName) {
                                    if (request('direction', 'desc') === 'asc') {
                                        return '<svg class="w-3 h-3 text-bedas-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>';
                                    } else {
                                        return '<svg class="w-3 h-3 text-bedas-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                                    }
                                }
                                return '<svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg>';
                            };
                        @endphp
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'tanggal', 'direction' => request('sort', 'tanggal') === 'tanggal' && request('direction', 'desc') === 'desc' ? 'asc' : 'desc']) }}" class="flex items-center gap-1 hover:text-gray-800 transition">
                                    Tanggal {!! $renderSortIcon('tanggal') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'no_bukti', 'direction' => request('sort') === 'no_bukti' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-800 transition">
                                    No. Bukti {!! $renderSortIcon('no_bukti') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'jenis_pencairan', 'direction' => request('sort') === 'jenis_pencairan' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-800 transition">
                                    Jenis Pencairan {!! $renderSortIcon('jenis_pencairan') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'uraian', 'direction' => request('sort') === 'uraian' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-800 transition">
                                    Uraian {!! $renderSortIcon('uraian') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'pptk_id', 'direction' => request('sort') === 'pptk_id' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-gray-800 transition">
                                    PPTK {!! $renderSortIcon('pptk_id') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nominal', 'direction' => request('sort') === 'nominal' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-end gap-1 hover:text-gray-800 transition">
                                    Nominal {!! $renderSortIcon('nominal') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'status_validasi', 'direction' => request('sort') === 'status_validasi' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-center gap-1 hover:text-gray-800 transition">
                                    Status {!! $renderSortIcon('status_validasi') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transaksis as $transaksi)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $transaksi->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="bg-gray-100 text-gray-600 py-1 px-2 rounded text-xs font-mono border">
                                        {{ $transaksi->no_bukti }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                    <span
                                        class="bg-blue-50 text-blue-700 py-1 px-2 rounded-lg text-xs font-semibold border border-blue-200">
                                        {{ $transaksi->jenis_pencairan ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 font-medium">
                                    {{ $transaksi->uraian }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 font-medium whitespace-nowrap">
                                    {{ $transaksi->nama_pptk ?? ($transaksi->pptk ? $transaksi->pptk->nama : '-') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800 text-right">
                                    Rp {{ number_format($transaksi->nominal, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if ($transaksi->status_validasi)
                                        <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs font-semibold inline-flex items-center gap-1 border border-green-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Tervalidasi
                                        </span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold inline-flex items-center gap-1 border border-yellow-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Belum Validasi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        @if(auth()->user()->role === 'admin')
                                            <!-- Edit (Admin Only) -->
                                            @if(!$transaksi->status_validasi)
                                                <button
                                                    @click="openEditModal({{ $transaksi->setAttribute('tanggal_formatted', $transaksi->tanggal->format('d/m/Y')) }})"
                                                    class="text-amber-500 hover:text-amber-700 transition" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button" class="text-gray-300 cursor-not-allowed" title="Transaksi sudah divalidasi. Minta PPTK batalkan validasi untuk mengedit.">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif

                                        @if(auth()->user()->role === 'pptk')
                                            @if(!$transaksi->status_validasi)
                                                <!-- Preview & Validasi (PPTK) -->
                                                <button type="button" @click="openPreviewModal('{{ route('bku.print', $transaksi->id) }}', '{{ route('bku.validasi', $transaksi->id) }}', 'Kuitansi {{ $transaksi->no_bukti }}')"
                                                    class="text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 p-1.5 rounded-lg transition" title="Validasi">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <!-- Preview Only (PPTK, sudah divalidasi) -->
                                                <button type="button" @click="openPreviewModal('{{ route('bku.print', $transaksi->id) }}', '', 'Kuitansi {{ $transaksi->no_bukti }}')"
                                                    class="text-blue-500 hover:text-blue-700 transition inline-block mr-1" title="Preview Kuitansi">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>

                                                <!-- Batalkan Validasi -->
                                                <button type="button" @click="$dispatch('open-unvalidate-modal', { action: '{{ route('bku.batal_validasi', $transaksi->id) }}', title: 'Batalkan Validasi?', message: 'Apakah Anda yakin ingin membatalkan validasi transaksi ini? Data akan dikembalikan ke status Belum Validasi.' })" 
                                                    class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded-lg transition inline-block" title="Batalkan Validasi">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        @else
                                            <!-- Print (Admin) -->
                                            @if($transaksi->status_validasi)
                                                <a href="{{ route('bku.print', $transaksi->id) }}" target="_blank"
                                                    class="text-blue-500 hover:text-blue-700 transition" title="Cetak Kwitansi">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                        </path>
                                                    </svg>
                                                </a>
                                            @else
                                                <button type="button" class="text-gray-300 cursor-not-allowed" title="Belum Tervalidasi">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif

                                        @if(auth()->user()->role === 'admin')
                                            <!-- Delete (Admin Only) -->
                                            @if(!$transaksi->status_validasi)
                                                <button type="button"
                                                    @click="$dispatch('open-delete-modal', { action: '{{ route('bku.destroy', $transaksi->id) }}', title: 'Hapus Transaksi?', message: 'Data transaksi ini akan dihapus secara permanen.' })"
                                                    class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button" class="text-gray-300 cursor-not-allowed" title="Transaksi sudah divalidasi. Minta PPTK batalkan validasi untuk menghapus.">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                    <p class="text-lg">Belum ada data transaksi.</p>
                                    <p class="text-sm">Silakan tambah transaksi baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $transaksis->links() }}
            </div>
        </div>



        <!-- Create/Edit Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" x-show="showModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showModal = false"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl z-50 overflow-hidden transform transition-all"
                    x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h2 class="text-xl font-bold text-gray-800"
                            x-text="isEdit ? 'Edit Transaksi' : 'Input Transaksi Baru'"></h2>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6 mb-0 rounded-r">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada inputan Anda:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" :action="formAction" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                        <input type="hidden" name="id" x-model="form.id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <div class="relative flex items-center">
                                    <input type="text" name="tanggal" id="tanggal" x-model="form.tanggal"
                                        placeholder="dd/mm/yyyy"
                                        class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                        required>

                                    <!-- Date Picker Overlay -->
                                    <style>
                                        .date-overlay::-webkit-calendar-picker-indicator {
                                            position: absolute;
                                            top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;
                                            padding: 0; margin: 0;
                                        }
                                    </style>
                                    <input type="date" class="date-overlay absolute inset-y-0 right-0 w-12 h-full opacity-0 cursor-pointer z-20"
                                        @input="
                                            let d = new Date($event.target.value);
                                            if(!isNaN(d.getTime())) {
                                                let day = ('0' + d.getDate()).slice(-2);
                                                let month = ('0' + (d.getMonth() + 1)).slice(-2);
                                                let year = d.getFullYear();
                                                form.tanggal = `${day}/${month}/${year}`;
                                            }
                                        ">

                                    <!-- Calendar Icon -->
                                    <div class="absolute inset-y-0 right-0 px-3 flex items-center pointer-events-none text-gray-500 z-10">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="jenis_pencairan" class="block text-sm font-medium text-gray-700 mb-1">Jenis
                                    Pencairan</label>
                                <select name="jenis_pencairan" id="jenis_pencairan" x-model="form.jenis_pencairan"
                                    class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                    required>
                                    <option value="">-- Pilih Jenis Pencairan --</option>
                                    <option value="UP">UP</option>
                                    <option value="GU 1">GU 1</option>
                                    <option value="GU 2">GU 2</option>
                                    <option value="GU 3">GU 3</option>
                                    <option value="GU 4">GU 4</option>
                                    <option value="GU 5">GU 5</option>
                                    <option value="GU 6">GU 6</option>
                                    <option value="GU 7">GU 7</option>
                                    <option value="GU 8">GU 8</option>
                                    <option value="GU 9">GU 9</option>
                                    <option value="GU 10">GU 10</option>
                                    <option value="GU 11">GU 11</option>
                                    <option value="GU 12">GU 12</option>
                                </select>
                            </div>

                            <div>
                                <label for="no_bukti" class="block text-sm font-medium text-gray-700 mb-1">No. Bukti</label>
                                <input type="text" name="no_bukti" id="no_bukti" x-model="form.no_bukti"
                                    class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200 bg-gray-50 text-gray-500 cursor-not-allowed"
                                    placeholder="Contoh: 001/BKU/2026" required>
                            </div>
                        </div>

                        <div>
                            <label for="pptk_id" class="block text-sm font-medium text-gray-700 mb-1">PPTK Penanggung
                                Jawab</label>
                            <select name="pptk_id" id="pptk_id" x-model="form.pptk_id"
                                class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                required>
                                <option value="">-- Pilih PPTK --</option>
                                @foreach($pptks as $pptk)
                                    <option value="{{ $pptk->id }}">{{ $pptk->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="kode_rekening" class="block text-sm font-medium text-gray-700 mb-1">Kode
                                Rekening</label>
                            <input type="text" name="kode_rekening" id="kode_rekening" x-model="form.kode_rekening"
                                class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                placeholder="Contoh: 5.1.02.01.01.0001" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="kode_sub_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Kode Sub
                                    Kegiatan</label>
                                <input list="sub_kegiatan_list" name="kode_sub_kegiatan" id="kode_sub_kegiatan"
                                    x-model="form.kode_sub_kegiatan" @input="updateSubKegiatanNama()"
                                    class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                    placeholder="Ketik atau pilih sub kegiatan...">
                                <datalist id="sub_kegiatan_list">
                                    @foreach($subKegiatans as $sub)
                                        <option value="{{ $sub->kode }}">{{ $sub->uraian }}</option>
                                    @endforeach
                                </datalist>
                            </div>

                            <div>
                                <label for="nama_sub_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Nama Sub
                                    Kegiatan</label>
                                <input type="text" name="nama_sub_kegiatan" id="nama_sub_kegiatan"
                                    x-model="form.nama_sub_kegiatan"
                                    class="w-full rounded-lg border-gray-100 bg-gray-50 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200 cursor-not-allowed"
                                    placeholder="Nama sub kegiatan" readonly>
                            </div>
                        </div>

                        <div>
                            <label for="uraian" class="block text-sm font-medium text-gray-700 mb-1">Uraian</label>
                            <textarea name="uraian" id="uraian" rows="3" x-model="form.uraian"
                                class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                placeholder="Jelaskan detail transaksi..." required></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="penerima" class="block text-sm font-medium text-gray-700 mb-1">Nama
                                    Penerima</label>
                                <input type="text" name="penerima" id="penerima" x-model="form.penerima"
                                    class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                    placeholder="Nama lengkap penerima" required>
                            </div>

                            <div>
                                <label for="nominal" class="block text-sm font-medium text-gray-700 mb-1">Nominal
                                    (Rp)</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="text" name="nominal" id="nominal" x-model="form.nominal"
                                        x-on:input="form.nominal = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                        class="w-full rounded-lg border-gray-300 pl-10 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                        placeholder="0" required>
                                </div>
                            </div>
                        </div>

                        <!-- Tax Fields (Optional) -->
                        <div class="pt-4 border-t border-gray-100">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pajak</label>
                            <select x-model="selectedTaxType"
                                x-on:change="if (selectedTaxType === 'none') { form.pph21 = ''; form.pph22 = ''; form.pph23 = ''; form.pph4_final = ''; form.ppn = ''; form.pajak_daerah = ''; }"
                                class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200 mb-4">
                                <option value="none">Tidak Ada Pajak</option>
                                <option value="pph21">PPh 21</option>
                                <option value="pph22">PPh 22</option>
                                <option value="pph23">PPh 23</option>
                                <option value="pph4_final">PPh Pasal 4 (Final)</option>
                                <option value="ppn">PPN</option>
                                <option value="pajak_daerah">Pajak Daerah</option>
                                <option value="all">Manual / Lebih dari satu</option>
                            </select>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="selectedTaxType !== 'none'"
                                x-transition>
                                <!-- PPh 21 -->
                                <div x-show="selectedTaxType === 'pph21' || selectedTaxType === 'all'">
                                    <label for="pph21" class="block text-sm font-medium text-gray-700 mb-1">PPh 21
                                        (Rp)</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-xs">Rp</span>
                                        </div>
                                        <input type="text" name="pph21" id="pph21" x-model="form.pph21"
                                            x-on:input="form.pph21 = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                            class="w-full rounded-lg border-gray-300 pl-8 text-sm focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                            placeholder="0">
                                    </div>
                                </div>
                                <!-- PPh 22 -->
                                <div x-show="selectedTaxType === 'pph22' || selectedTaxType === 'all'">
                                    <label for="pph22" class="block text-sm font-medium text-gray-700 mb-1">PPh 22
                                        (Rp)</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-xs">Rp</span>
                                        </div>
                                        <input type="text" name="pph22" id="pph22" x-model="form.pph22"
                                            x-on:input="form.pph22 = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                            class="w-full rounded-lg border-gray-300 pl-8 text-sm focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                            placeholder="0">
                                    </div>
                                </div>
                                <!-- PPh 23 -->
                                <div x-show="selectedTaxType === 'pph23' || selectedTaxType === 'all'">
                                    <label for="pph23" class="block text-sm font-medium text-gray-700 mb-1">PPh 23
                                        (Rp)</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-xs">Rp</span>
                                        </div>
                                        <input type="text" name="pph23" id="pph23" x-model="form.pph23"
                                            x-on:input="form.pph23 = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                            class="w-full rounded-lg border-gray-300 pl-8 text-sm focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                            placeholder="0">
                                    </div>
                                </div>
                                <!-- PPh 4 Final -->
                                <div x-show="selectedTaxType === 'pph4_final' || selectedTaxType === 'all'">
                                    <label for="pph4_final" class="block text-sm font-medium text-gray-700 mb-1">PPh 4
                                        (Final) (Rp)</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-xs">Rp</span>
                                        </div>
                                        <input type="text" name="pph4_final" id="pph4_final" x-model="form.pph4_final"
                                            x-on:input="form.pph4_final = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                            class="w-full rounded-lg border-gray-300 pl-8 text-sm focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                            placeholder="0">
                                    </div>
                                </div>
                                <!-- PPN -->
                                <div x-show="selectedTaxType === 'ppn' || selectedTaxType === 'all'">
                                    <label for="ppn" class="block text-sm font-medium text-gray-700 mb-1">PPN (Rp)</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-xs">Rp</span>
                                        </div>
                                        <input type="text" name="ppn" id="ppn" x-model="form.ppn"
                                            x-on:input="form.ppn = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                            class="w-full rounded-lg border-gray-300 pl-8 text-sm focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                            placeholder="0">
                                    </div>
                                </div>
                                <!-- Pajak Daerah -->
                                <div x-show="selectedTaxType === 'pajak_daerah' || selectedTaxType === 'all'">
                                    <label for="pajak_daerah" class="block text-sm font-medium text-gray-700 mb-1">Pajak
                                        Daerah (Rp)</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-xs">Rp</span>
                                        </div>
                                        <input type="text" name="pajak_daerah" id="pajak_daerah" x-model="form.pajak_daerah"
                                            x-on:input="form.pajak_daerah = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                            class="w-full rounded-lg border-gray-300 pl-8 text-sm focus:border-bedas-500 focus:ring focus:ring-bedas-200 transition duration-200"
                                            placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 flex justify-end gap-3 border-t border-gray-100">
                            <button type="button" @click="showModal = false"
                                class="px-4 py-2 text-gray-700 font-semibold hover:bg-gray-100 rounded-lg transition">Batal</button>
                            <button type="submit"
                                class="px-6 py-2 bg-bedas-600 text-white font-bold rounded-lg hover:bg-bedas-700 shadow-md transition transform hover:-translate-y-0.5">
                                <span x-text="isEdit ? 'Update Transaksi' : 'Simpan Transaksi'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Import Modal -->
        <div x-show="openImportModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" x-show="openImportModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="openImportModal = false">
            </div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md z-50 overflow-hidden transform transition-all"
                    x-show="openImportModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h2 class="text-xl font-bold text-gray-800">Import Data BKU</h2>
                        <button @click="openImportModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('bku.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx, .xls,
                                .csv)</label>
                            <input type="file" name="file" required
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-bedas-50 file:text-bedas-700 hover:file:bg-bedas-100">
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg mb-6 relative">
                            <a href="{{ route('bku.template') }}"
                                class="no-loader absolute top-4 right-4 text-xs bg-blue-600 hover:bg-blue-700 text-white py-1.5 px-3 rounded-md shadow-sm transition inline-flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download Template
                            </a>
                            <h4 class="text-sm font-bold text-blue-800 mb-2">Panduan Kolom Excel:</h4>
                            <ul class="text-xs text-blue-700 list-disc list-inside space-y-1">
                                <li><b>tanggal</b> (format: DD/MM/YYYY atau YYYY-MM-DD)</li>
                                <li><b>jenis_pencairan</b> (contoh: UP, GU 1, GU 2, dsb)</li>
                                <li><b>no_bukti</b></li>
                                <li><b>kode_rekening</b></li>
                                <li><b>kode_sub_kegiatan</b> (harus sesuai dengan data anggaran)</li>
                                <li><b>uraian</b></li>
                                <li><b>penerima</b></li>
                                <li><b>nominal</b></li>
                                <li><b>pptk</b> (nama PPTK sesuai data pejabat)</li>
                                <li>Optional: <b>pph21, pph22, pph23, ppn, pajak_daerah, pph4_final</b></li>
                            </ul>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="openImportModal = false"
                                class="px-4 py-2 text-gray-700 font-semibold hover:bg-gray-100 rounded-lg transition">Batal</button>
                            <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5">Mulai
                                Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <div x-show="showPreviewModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" x-show="showPreviewModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showPreviewModal = false">
            </div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl z-50 overflow-hidden flex flex-col h-[90vh] transform transition-all"
                    x-show="showPreviewModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 flex-shrink-0">
                        <h2 class="text-xl font-bold text-gray-800" x-text="previewTitle">Preview Kuitansi</h2>
                        <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-hidden bg-gray-200 p-2">
                        <iframe :src="previewUrl" class="w-full h-full border-0 rounded bg-white shadow-inner"></iframe>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 bg-white flex justify-end gap-3 flex-shrink-0" x-show="validasiUrl">
                        <form :action="validasiUrl" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-md transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Validasi Kuitansi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unvalidate Confirmation Modal -->
        <div x-data="{ 
            openUnvalidate: false, 
            action: '', 
            title: '', 
            message: '' 
        }" x-on:open-unvalidate-modal.window="openUnvalidate = true; action = $event.detail.action; title = $event.detail.title; message = $event.detail.message;"
            x-show="openUnvalidate" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
            
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" x-show="openUnvalidate"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="openUnvalidate = false">
            </div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[110] overflow-hidden transform transition-all"
                    x-show="openUnvalidate" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                    <div class="p-6 text-center">
                        <div class="w-20 h-20 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="title"></h3>
                        <p class="text-gray-500 mb-6" x-text="message"></p>

                        <div class="flex flex-col gap-3">
                            <form :action="action" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-bold rounded-xl shadow-lg shadow-yellow-200 transition transform hover:-translate-y-0.5 active:scale-95">
                                    Ya, Batalkan Validasi
                                </button>
                            </form>
                            <button @click="openUnvalidate = false"
                                class="w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition active:scale-95">
                                Kembali
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function bkuData() {
                return {
                    showPreviewModal: false,
                    previewUrl: '',
                    validasiUrl: '',
                    previewTitle: 'Preview',
                    openPreviewModal(url, validasiUrl, title) {
                        this.previewUrl = url;
                        this.validasiUrl = validasiUrl;
                        this.previewTitle = title;
                        this.showPreviewModal = true;
                    },
                    showModal: {{ $errors->any() && !session('import_errors') ? 'true' : 'false' }},
                    openImportModal: {{ session('import_errors') ? 'true' : 'false' }},
                    isEdit: {{ old('_method') === 'PUT' ? 'true' : 'false' }},
                    selectedTaxType: 'none',
                    form: {
                        id: '{{ old('id') }}',
                        tanggal: '{{ old('tanggal', date('Y-m-d')) }}',
                        no_bukti: '{{ old('no_bukti') }}',
                        kode_rekening: '{{ old('kode_rekening') }}',
                        kode_sub_kegiatan: '{{ old('kode_sub_kegiatan') }}',
                        nama_sub_kegiatan: '{{ old('nama_sub_kegiatan') }}',
                        uraian: '{{ old('uraian') }}',
                        penerima: '{{ old('penerima') }}',
                        nominal: '{{ old('nominal') }}',
                        jenis_pencairan: '{{ old('jenis_pencairan') }}',
                        pptk_id: '{{ old('pptk_id') }}',
                        jenis_anggaran: 'Murni',
                        pph21: '{{ old('pph21', 0) }}',
                        pph22: '{{ old('pph22', 0) }}',
                        pph23: '{{ old('pph23', 0) }}',
                        pph4_final: '{{ old('pph4_final', 0) }}',
                        ppn: '{{ old('ppn', 0) }}',
                        pajak_daerah: '{{ old('pajak_daerah', 0) }}'
                    },
                    get formAction() {
                        if (this.isEdit) {
                            return '{{ url('bku') }}/' + this.form.id;
                        } else {
                            return '{{ route('bku.store') }}';
                        }
                    },
                    // Helper to format currency for display
                    formatCurrency(value) {
                        if (!value) return '';
                        return Math.floor(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    },
                    openCreateModal() {
                        this.openImportModal = false;
                        this.isEdit = false;
                        this.selectedTaxType = 'none'; // Reset tax selection
                        this.form = {
                            id: '',
                            tanggal: '{{ date('d/m/Y') }}',
                            no_bukti: '',
                            kode_rekening: '',
                            kode_sub_kegiatan: '',
                            nama_sub_kegiatan: '',
                            uraian: '',
                            penerima: '',
                            nominal: '',
                            jenis_pencairan: '',
                            pptk_id: '',
                            jenis_anggaran: 'Murni',
                            pph21: '',
                            pph22: '',
                            pph23: '',
                            ppn: '',
                            pajak_daerah: '',
                            pph4_final: ''
                        };
                        this.showModal = true;
                        this.fetchNoBukti();
                    },
                    openEditModal(data) {
                        this.isEdit = true;
                        // Format nominal and taxes with dots for display
                        let nominalFormatted = this.formatCurrency(data.nominal);
                        let pph21Formatted = this.formatCurrency(data.pph21);
                        let pph22Formatted = this.formatCurrency(data.pph22);
                        let pph23Formatted = this.formatCurrency(data.pph23);
                        let ppnFormatted = this.formatCurrency(data.ppn);
                        let pajakDaerahFormatted = this.formatCurrency(data.pajak_daerah);
                        let pph4FinalFormatted = this.formatCurrency(data.pph4_final);

                        // Determine selected tax type
                        let taxCount = 0;
                        if (data.pph21 > 0) taxCount++;
                        if (data.pph22 > 0) taxCount++;
                        if (data.pph23 > 0) taxCount++;
                        if (data.ppn > 0) taxCount++;
                        if (data.pajak_daerah > 0) taxCount++;
                        if (data.pph4_final > 0) taxCount++;

                        if (taxCount > 1) {
                            this.selectedTaxType = 'all';
                        } else if (data.pph21 > 0) {
                            this.selectedTaxType = 'pph21';
                        } else if (data.pph22 > 0) {
                            this.selectedTaxType = 'pph22';
                        } else if (data.pph23 > 0) {
                            this.selectedTaxType = 'pph23';
                        } else if (data.ppn > 0) {
                            this.selectedTaxType = 'ppn';
                        } else if (data.pajak_daerah > 0) {
                            this.selectedTaxType = 'pajak_daerah';
                        } else if (data.pph4_final > 0) {
                            this.selectedTaxType = 'pph4_final';
                        } else {
                            this.selectedTaxType = 'none';
                        }

                        this.form = {
                            id: data.id,
                            tanggal: data.tanggal_formatted,
                            no_bukti: data.no_bukti,
                            kode_rekening: data.kode_rekening,
                            kode_sub_kegiatan: data.kode_sub_kegiatan,
                            nama_sub_kegiatan: data.nama_sub_kegiatan,
                            uraian: data.uraian,
                            penerima: data.penerima,
                            nominal: nominalFormatted,
                            jenis_pencairan: data.jenis_pencairan,
                            pptk_id: data.pptk_id,
                            pph21: pph21Formatted,
                            pph22: pph22Formatted,
                            pph23: pph23Formatted,
                            ppn: ppnFormatted,
                            pajak_daerah: pajakDaerahFormatted,
                            pph4_final: pph4FinalFormatted
                        };
                        this.openImportModal = false;
                        this.showModal = true;
                    },
                    updateSubKegiatanNama() {
                        const input = document.getElementById('kode_sub_kegiatan');
                        const datalist = document.getElementById('sub_kegiatan_list');
                        const options = datalist.options;

                        let found = false;
                        for (let i = 0; i < options.length; i++) {
                            if (options[i].value === this.form.kode_sub_kegiatan) {
                                this.form.nama_sub_kegiatan = options[i].text;
                                found = true;
                                break;
                            }
                        }

                        if (!found) {
                            // Optional: clear name if code doesn't match any option
                            // this.form.nama_sub_kegiatan = '';
                        }
                    },
                    initData() {
                        @if (old('uraian'))
                            this.form.uraian = @json(old('uraian'));
                        @endif
                        
                        this.$watch('form.tanggal', (value) => {
                            if (!this.isEdit) this.fetchNoBukti();
                        });
                        this.$watch('form.jenis_pencairan', (value) => {
                            if (!this.isEdit) this.fetchNoBukti();
                        });
                    },
                    fetchNoBukti() {
                        if (this.isEdit) return; // Don't auto-generate when editing
                        
                        let url = '{{ route('bku.generate_no_bukti') }}';
                        let params = new URLSearchParams({
                            tanggal: this.form.tanggal || '{{ date('d/m/Y') }}',
                            jenis_pencairan: this.form.jenis_pencairan || ''
                        });
                        
                        fetch(url + '?' + params.toString())
                            .then(response => response.json())
                            .then(data => {
                                this.form.no_bukti = data.no_bukti;
                            })
                            .catch(error => console.error('Error fetching no bukti:', error));
                    }
                }
            }
        </script>
@endsection