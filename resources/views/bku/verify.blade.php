@extends('layouts.app')

@section('title', 'Verifikasi Kwitansi - ' . ($bku->no_bukti ?? 'Tidak Ditemukan'))

@section('content')
<div class="max-w-3xl mx-auto">
    @if($bku)
        {{-- Verification Status Banner --}}
        <div class="mb-6 rounded-2xl overflow-hidden shadow-lg border {{ $bku->status_validasi ? 'border-green-200' : 'border-yellow-200' }}">
            <div class="px-6 py-5 {{ $bku->status_validasi ? 'bg-gradient-to-r from-green-500 to-emerald-600' : 'bg-gradient-to-r from-yellow-500 to-amber-600' }} text-white">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        @if($bku->status_validasi)
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        @else
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">
                            {{ $bku->status_validasi ? '✓ Kwitansi Terverifikasi' : '⏳ Menunggu Validasi' }}
                        </h2>
                        <p class="text-white/80 text-sm mt-1">
                            {{ $bku->status_validasi ? 'Kwitansi ini telah divalidasi oleh PPTK dan sah untuk digunakan.' : 'Kwitansi ini belum divalidasi oleh PPTK.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction Detail Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-bedas-100 text-bedas-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Detail Kwitansi</h3>
                        <p class="text-xs text-gray-500">{{ $bku->no_bukti }}</p>
                    </div>
                </div>
                <span class="bg-blue-50 text-blue-700 py-1 px-3 rounded-lg text-xs font-semibold border border-blue-200">
                    {{ $bku->jenis_pencairan ?? '-' }}
                </span>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Tanggal --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Tanggal</p>
                        <p class="text-base font-semibold text-gray-800">{{ \Carbon\Carbon::parse($bku->tanggal)->isoFormat('D MMMM Y') }}</p>
                    </div>

                    {{-- Nominal --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Nominal</p>
                        <p class="text-xl font-bold text-green-700">Rp {{ number_format($bku->nominal, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Kode Rekening --}}
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Kode Rekening</p>
                    <p class="text-base font-mono font-semibold text-gray-800">{{ $bku->kode_rekening }}</p>
                </div>

                @if($bku->kode_sub_kegiatan)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Kode Sub Kegiatan</p>
                        <p class="text-sm font-mono font-semibold text-gray-800">{{ $bku->kode_sub_kegiatan }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Nama Sub Kegiatan</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $bku->nama_sub_kegiatan ?? '-' }}</p>
                    </div>
                </div>
                @endif

                {{-- Uraian --}}
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Uraian</p>
                    <p class="text-base text-gray-800">{{ $bku->uraian }}</p>
                </div>

                {{-- Penerima --}}
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Penerima</p>
                    <p class="text-base font-semibold text-gray-800">{{ $bku->penerima }}</p>
                </div>

                {{-- PPTK --}}
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">PPTK Penanggung Jawab</p>
                    <p class="text-base font-semibold text-gray-800">{{ $bku->nama_pptk ?? ($bku->pptk ? $bku->pptk->nama : '-') }}</p>
                </div>

                {{-- Pajak Section --}}
                @php
                    $taxes = [
                        'PPh 21' => $bku->pph21,
                        'PPh 22' => $bku->pph22,
                        'PPh 23' => $bku->pph23,
                        'PPh Pasal 4 (Final)' => $bku->pph4_final,
                        'PPN' => $bku->ppn,
                        'Pajak Daerah' => $bku->pajak_daerah,
                    ];
                    $totalPajak = array_sum(array_filter($taxes));
                @endphp

                @if($totalPajak > 0)
                <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                    <p class="text-xs text-red-500 font-medium uppercase tracking-wider mb-2">Potongan Pajak</p>
                    <div class="space-y-1">
                        @foreach($taxes as $label => $amount)
                            @if($amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ $label }}</span>
                                <span class="font-semibold text-red-700">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        @endforeach
                        <div class="border-t border-red-200 pt-2 mt-2 flex justify-between">
                            <span class="font-bold text-gray-700">Jumlah Diterima</span>
                            <span class="font-bold text-green-700 text-lg">Rp {{ number_format($bku->nominal - $totalPajak, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Pejabat Signatures --}}
                <div class="border-t border-gray-100 pt-4 mt-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-3">Pejabat Terkait</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="bg-blue-50 rounded-xl p-3 text-center border border-blue-100">
                            <p class="text-xs text-blue-500 font-medium">Camat</p>
                            <p class="text-sm font-bold text-gray-800 mt-1">{{ $bku->nama_camat ?? '-' }}</p>
                            @if($bku->nip_camat)
                            <p class="text-xs text-gray-500">NIP. {{ $bku->nip_camat }}</p>
                            @endif
                        </div>
                        <div class="bg-purple-50 rounded-xl p-3 text-center border border-purple-100">
                            <p class="text-xs text-purple-500 font-medium">PPTK</p>
                            <p class="text-sm font-bold text-gray-800 mt-1">{{ $bku->nama_pptk ?? '-' }}</p>
                            @if($bku->nip_pptk)
                            <p class="text-xs text-gray-500">NIP. {{ $bku->nip_pptk }}</p>
                            @endif
                        </div>
                        <div class="bg-emerald-50 rounded-xl p-3 text-center border border-emerald-100">
                            <p class="text-xs text-emerald-500 font-medium">Bendahara</p>
                            <p class="text-sm font-bold text-gray-800 mt-1">{{ $bku->nama_bendahara ?? '-' }}</p>
                            @if($bku->nip_bendahara)
                            <p class="text-xs text-gray-500">NIP. {{ $bku->nip_bendahara }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-3 items-center justify-between">
                <div class="text-xs text-gray-400 font-mono">
                    QR ID: {{ $bku->qr_code_hash }}
                </div>
                <div class="flex gap-2">
                    @if($bku->status_validasi)
                    <a href="{{ route('bku.print', $bku->id) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition transform hover:scale-105 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak Kwitansi
                    </a>
                    @endif
                    <a href="{{ route('bku.index', ['search' => $bku->no_bukti]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-bedas-600 hover:bg-bedas-700 text-white font-semibold rounded-lg shadow-md transition transform hover:scale-105 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Buka di BKU
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Not Found --}}
        <div class="bg-white rounded-2xl shadow-lg border border-red-100 overflow-hidden">
            <div class="p-10 text-center">
                <div class="w-20 h-20 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Kwitansi Tidak Ditemukan</h3>
                <p class="text-gray-500 mb-6">QR Code ini tidak cocok dengan kwitansi manapun di sistem.</p>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-bedas-600 hover:bg-bedas-700 text-white font-semibold rounded-lg shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
