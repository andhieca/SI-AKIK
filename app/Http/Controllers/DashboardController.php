<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function getDashboardData(\Illuminate\Http\Request $request)
    {
        $tahun = session('tahun_anggaran', date('Y'));

        $totalAnggaran = \App\Models\Anggaran::where('tahun', $tahun)
            ->whereNull('parent_id')
            ->get()
            ->sum(fn($item) => $item->getPaguRecursive());
        $queryBku = \App\Models\BkuTransaksi::whereYear('tanggal', $tahun);

        if (auth()->user()->role === 'pptk') {
            $queryBku->where('pptk_id', auth()->user()->pejabat_id);
            $totalAnggaran = (clone $queryBku)->sum('nominal');
        }

        $totalRealisasi = (clone $queryBku)->sum('nominal');
        $sisaKas = $totalAnggaran - $totalRealisasi;

        $latestTransaksis = (clone $queryBku)
            ->latest()
            ->take(5)
            ->get();

        $realisasiPerProgram = collect();
        $validatedCount = 0;
        $unvalidatedCount = 0;
        $unvalidatedNominal = 0;
        $validatedNominal = 0;

        $statsPerPptk = collect();

        if (auth()->user()->role === 'pptk') {
            $pejabat_id = auth()->user()->pejabat_id;
            $validatedCount = clone $queryBku;
            $validatedCount = $validatedCount->where('status_validasi', true)->count();

            $unvalidatedCount = clone $queryBku;
            $unvalidatedCount = $unvalidatedCount->where('status_validasi', false)->count();

            $unvalidatedNominal = clone $queryBku;
            $unvalidatedNominal = $unvalidatedNominal->where('status_validasi', false)->sum('nominal');

            $validatedNominal = clone $queryBku;
            $validatedNominal = $validatedNominal->where('status_validasi', true)->sum('nominal');
        } else {
            $programs = \App\Models\Anggaran::where('tahun', $tahun)
                ->whereNull('parent_id')
                ->get();

            $realisasiPerProgram = $programs->map(function ($program) {
                $pagu = $program->getPaguRecursive();
                $realisasi = $program->getRealisasi();
                $persentase = $pagu > 0 ? ($realisasi / $pagu) * 100 : 0;
                return [
                    'kode' => $program->kode,
                    'uraian' => $program->uraian,
                    'pagu' => $pagu,
                    'realisasi' => $realisasi,
                    'persentase' => round($persentase, 2)
                ];
            });

            $statsPerPptk = \App\Models\Pejabat::where('jabatan', 'PPTK')->get()->map(function($pptk) use ($tahun) {
                $validated = \App\Models\BkuTransaksi::where('pptk_id', $pptk->id)
                    ->whereYear('tanggal', $tahun)
                    ->where('status_validasi', true)
                    ->count();
                $unvalidated = \App\Models\BkuTransaksi::where('pptk_id', $pptk->id)
                    ->whereYear('tanggal', $tahun)
                    ->where('status_validasi', false)
                    ->count();
                return (object)[
                    'nama' => $pptk->nama,
                    'validated_count' => $validated,
                    'unvalidated_count' => $unvalidated,
                    'total_count' => $validated + $unvalidated
                ];
            })->filter(function($pptk) {
                return $pptk->total_count > 0;
            })->sortByDesc('unvalidated_count')->values();
        }

        $realisasiPerJenis = \App\Models\BkuTransaksi::whereYear('tanggal', $tahun)
            ->select('jenis_pencairan', \DB::raw('SUM(nominal) as total'))
            ->groupBy('jenis_pencairan')
            ->get()
            ->map(function ($item) {
                return [
                    'jenis' => $item->jenis_pencairan ?: 'Lainnya',
                    'total' => $item->total
                ];
            });

        $queryKuitansi = \App\Models\BkuTransaksi::whereYear('tanggal', $tahun);
        if (auth()->user()->role === 'pptk') {
            $queryKuitansi->where('pptk_id', auth()->user()->pejabat_id);
        }

        $kuitansiPerBulan = $queryKuitansi
            ->selectRaw(
                \DB::getDriverName() === 'sqlite'
                    ? "CAST(strftime('%m', tanggal) AS INTEGER) as bulan, COUNT(*) as jumlah"
                    : "MONTH(tanggal) as bulan, COUNT(*) as jumlah"
            )
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->pluck('jumlah', 'bulan');

        $grafikKuitansi = [];
        for ($i = 1; $i <= 12; $i++) {
            $grafikKuitansi[] = $kuitansiPerBulan->get($i, 0);
        }

        $verificationResult = null;
        if ($request->has('verify')) {
            $verificationResult = \App\Models\BkuTransaksi::where('qr_code_hash', $request->query('verify'))->first();
        }

        $rekapPajak = [
            'PPh 21' => (clone $queryBku)->sum('pph21'),
            'PPh 22' => (clone $queryBku)->sum('pph22'),
            'PPh 23' => (clone $queryBku)->sum('pph23'),
            'PPN' => (clone $queryBku)->sum('ppn'),
            'Pajak Daerah' => (clone $queryBku)->sum('pajak_daerah'),
            'PPh Pasal 4 (Final)' => (clone $queryBku)->sum('pph4_final'),
        ];

        $detailPajak = [
            'PPh 21' => (clone $queryBku)->where('pph21', '>', 0)->get(['tanggal', 'no_bukti', 'uraian', 'pph21 as nominal_pajak']),
            'PPh 22' => (clone $queryBku)->where('pph22', '>', 0)->get(['tanggal', 'no_bukti', 'uraian', 'pph22 as nominal_pajak']),
            'PPh 23' => (clone $queryBku)->where('pph23', '>', 0)->get(['tanggal', 'no_bukti', 'uraian', 'pph23 as nominal_pajak']),
            'PPN' => (clone $queryBku)->where('ppn', '>', 0)->get(['tanggal', 'no_bukti', 'uraian', 'ppn as nominal_pajak']),
            'Pajak Daerah' => (clone $queryBku)->where('pajak_daerah', '>', 0)->get(['tanggal', 'no_bukti', 'uraian', 'pajak_daerah as nominal_pajak']),
            'PPh Pasal 4 (Final)' => (clone $queryBku)->where('pph4_final', '>', 0)->get(['tanggal', 'no_bukti', 'uraian', 'pph4_final as nominal_pajak']),
        ];

        return compact('totalAnggaran', 'totalRealisasi', 'sisaKas', 'latestTransaksis', 'verificationResult', 'realisasiPerProgram', 'realisasiPerJenis', 'grafikKuitansi', 'validatedCount', 'unvalidatedCount', 'unvalidatedNominal', 'validatedNominal', 'statsPerPptk', 'rekapPajak', 'detailPajak');
    }

    public function index(\Illuminate\Http\Request $request)
    {
        return view('dashboard', $this->getDashboardData($request));
    }

    public function display(\Illuminate\Http\Request $request)
    {
        if (auth()->user()->role !== 'camat') {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Camat.');
        }
        return view('display', $this->getDashboardData($request));
    }
}
