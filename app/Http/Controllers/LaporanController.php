<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggaran;
use App\Models\BkuTransaksi;
use App\Helpers\KamusRekening;

class LaporanController extends Controller
{
    /**
     * Display the Laporan index page with kamus kode rekening and filter options.
     */
    public function index(Request $request)
    {
        $tahun = session('tahun_anggaran', date('Y'));

        // Kamus resmi kode rekening belanja dari standar
        $kamusRekening = KamusRekening::all();

        // Get all distinct kode rekening belanja yang ada di transaksi BKU untuk autocomplete
        $bkuKodes = BkuTransaksi::whereYear('tanggal', $tahun)
            ->whereNotNull('kode_rekening')
            ->select('kode_rekening')
            ->distinct()
            ->orderBy('kode_rekening')
            ->pluck('kode_rekening');

        // Gabungkan list kode rekening untuk datalist autocomplete (kamus + BKU)
        $kodeRekeningList = $kamusRekening->pluck('kode')
            ->concat($bkuKodes)
            ->unique()
            ->values();

        // Filter parameters
        $selectedKode = $request->input('kode_rekening', '');
        $periodeAwal = $request->input('periode_awal', '');
        $periodeAkhir = $request->input('periode_akhir', '');

        // Build query for report data
        $transaksiQuery = null;
        $transaksis = collect();
        $totalRealisasi = 0;
        $paguAnggaran = 0;
        $uraianRekening = '';
        $hasFilter = false;

        if ($selectedKode) {
            $hasFilter = true;

            $transaksiQuery = BkuTransaksi::whereYear('tanggal', $tahun);

            // Coba exact match dulu
            $countExact = (clone $transaksiQuery)->where('kode_rekening', $selectedKode)->count();
            if ($countExact > 0) {
                $transaksiQuery->where('kode_rekening', $selectedKode);
            } else {
                // Jika tidak ada exact match, coba cocokkan normalized kode (toleran zero-padding)
                $normSelected = KamusRekening::normalize($selectedKode);
                $matchingBkuKodes = BkuTransaksi::whereYear('tanggal', $tahun)
                    ->whereNotNull('kode_rekening')
                    ->distinct()
                    ->pluck('kode_rekening')
                    ->filter(fn($k) => KamusRekening::normalize($k) === $normSelected)
                    ->values();

                if ($matchingBkuKodes->isNotEmpty()) {
                    $transaksiQuery->whereIn('kode_rekening', $matchingBkuKodes);
                } else {
                    $transaksiQuery->where('kode_rekening', $selectedKode);
                }
            }

            if ($periodeAwal) {
                $transaksiQuery->whereDate('tanggal', '>=', $periodeAwal);
            }

            if ($periodeAkhir) {
                $transaksiQuery->whereDate('tanggal', '<=', $periodeAkhir);
            }

            $transaksis = $transaksiQuery->orderBy('tanggal', 'asc')->get();
            $totalRealisasi = $transaksis->sum('nominal');

            // Ambil Uraian Standar dari Kamus Kode Rekening Belanja
            $uraianRekening = KamusRekening::getUraian($selectedKode);
            if ($uraianRekening === '-') {
                $anggaran = Anggaran::where('kode', $selectedKode)->where('tahun', $tahun)->first();
                if ($anggaran) {
                    $uraianRekening = $anggaran->uraian;
                }
            }

            // Get pagu from anggaran if exists
            $anggaran = Anggaran::where('kode', $selectedKode)
                ->where('tahun', $tahun)
                ->first();

            if ($anggaran) {
                $paguAnggaran = $anggaran->pagu;
            }
        }

        return view('laporan.index', compact(
            'tahun',
            'kodeRekeningList',
            'kamusRekening',
            'selectedKode',
            'periodeAwal',
            'periodeAkhir',
            'transaksis',
            'totalRealisasi',
            'paguAnggaran',
            'uraianRekening',
            'hasFilter'
        ));
    }

    /**
     * Print/Cetak the report as a printable HTML page.
     */
    public function cetak(Request $request)
    {
        $tahun = session('tahun_anggaran', date('Y'));
        $selectedKode = $request->input('kode_rekening', '');
        $periodeAwal = $request->input('periode_awal', '');
        $periodeAkhir = $request->input('periode_akhir', '');

        if (!$selectedKode) {
            return redirect()->route('laporan.index')->with('error', 'Pilih kode rekening terlebih dahulu.');
        }

        $transaksiQuery = BkuTransaksi::whereYear('tanggal', $tahun);

        // Coba exact match dulu
        $countExact = (clone $transaksiQuery)->where('kode_rekening', $selectedKode)->count();
        if ($countExact > 0) {
            $transaksiQuery->where('kode_rekening', $selectedKode);
        } else {
            // Normalized matching toleran zero padding
            $normSelected = KamusRekening::normalize($selectedKode);
            $matchingBkuKodes = BkuTransaksi::whereYear('tanggal', $tahun)
                ->whereNotNull('kode_rekening')
                ->distinct()
                ->pluck('kode_rekening')
                ->filter(fn($k) => KamusRekening::normalize($k) === $normSelected)
                ->values();

            if ($matchingBkuKodes->isNotEmpty()) {
                $transaksiQuery->whereIn('kode_rekening', $matchingBkuKodes);
            } else {
                $transaksiQuery->where('kode_rekening', $selectedKode);
            }
        }

        if ($periodeAwal) {
            $transaksiQuery->whereDate('tanggal', '>=', $periodeAwal);
        }

        if ($periodeAkhir) {
            $transaksiQuery->whereDate('tanggal', '<=', $periodeAkhir);
        }

        $transaksis = $transaksiQuery->orderBy('tanggal', 'asc')->get();
        $totalRealisasi = $transaksis->sum('nominal');
        $totalPajak = $transaksis->sum(function ($t) {
            return ($t->pph21 ?? 0) + ($t->pph22 ?? 0) + ($t->pph23 ?? 0)
                + ($t->ppn ?? 0) + ($t->pajak_daerah ?? 0) + ($t->pph4_final ?? 0);
        });

        // Ambil Uraian Standar dari Kamus Kode Rekening Belanja
        $uraianRekening = KamusRekening::getUraian($selectedKode);
        if ($uraianRekening === '-') {
            $anggaran = Anggaran::where('kode', $selectedKode)->where('tahun', $tahun)->first();
            $uraianRekening = $anggaran ? $anggaran->uraian : '-';
        }

        $anggaran = Anggaran::where('kode', $selectedKode)
            ->where('tahun', $tahun)
            ->first();

        $paguAnggaran = $anggaran ? $anggaran->pagu : 0;
        $sisaAnggaran = $paguAnggaran - $totalRealisasi;

        // Get pejabat for signature
        $pejabats = \App\Models\Pejabat::all();
        $camat = $pejabats->where('jabatan', 'Camat')->first();
        $bendahara = $pejabats->where('jabatan', 'Bendahara')->first();

        return view('laporan.cetak', compact(
            'tahun',
            'selectedKode',
            'periodeAwal',
            'periodeAkhir',
            'transaksis',
            'totalRealisasi',
            'totalPajak',
            'paguAnggaran',
            'uraianRekening',
            'sisaAnggaran',
            'camat',
            'bendahara'
        ));
    }
}

