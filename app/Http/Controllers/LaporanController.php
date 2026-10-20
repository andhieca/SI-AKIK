<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggaran;
use App\Models\BkuTransaksi;

class LaporanController extends Controller
{
    /**
     * Display the Laporan index page with kamus kode rekening and filter options.
     */
    public function index(Request $request)
    {
        $tahun = session('tahun_anggaran', date('Y'));

        // Get all leaf-level kode rekening (yang punya transaksi)
        $kodeRekeningList = BkuTransaksi::whereYear('tanggal', $tahun)
            ->select('kode_rekening')
            ->distinct()
            ->orderBy('kode_rekening')
            ->pluck('kode_rekening');

        // Get all anggaran for the kamus
        $kamusRekening = Anggaran::where('tahun', $tahun)
            ->orderBy('kode')
            ->get(['id', 'kode', 'uraian', 'pagu', 'parent_id']);

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

            $transaksiQuery = BkuTransaksi::where('kode_rekening', $selectedKode)
                ->whereYear('tanggal', $tahun);

            if ($periodeAwal) {
                $transaksiQuery->whereDate('tanggal', '>=', $periodeAwal);
            }

            if ($periodeAkhir) {
                $transaksiQuery->whereDate('tanggal', '<=', $periodeAkhir);
            }

            $transaksis = $transaksiQuery->orderBy('tanggal', 'asc')->get();
            $totalRealisasi = $transaksis->sum('nominal');

            // Get pagu from anggaran
            $anggaran = Anggaran::where('kode', $selectedKode)
                ->where('tahun', $tahun)
                ->first();

            if ($anggaran) {
                $paguAnggaran = $anggaran->pagu;
                $uraianRekening = $anggaran->uraian;
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

        $transaksiQuery = BkuTransaksi::where('kode_rekening', $selectedKode)
            ->whereYear('tanggal', $tahun);

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

        $anggaran = Anggaran::where('kode', $selectedKode)
            ->where('tahun', $tahun)
            ->first();

        $paguAnggaran = $anggaran ? $anggaran->pagu : 0;
        $uraianRekening = $anggaran ? $anggaran->uraian : '-';
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
