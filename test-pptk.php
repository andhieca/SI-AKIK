<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pptks = \App\Models\Pejabat::where('jabatan', 'PPTK')->get();

foreach ($pptks as $pptk) {
    if (!\App\Models\User::where('pejabat_id', $pptk->id)->exists()) {
        continue;
    }
    $tahun = '2026';
    $queryBku = \App\Models\BkuTransaksi::whereYear('tanggal', $tahun)->where('pptk_id', $pptk->id);
    $subKegiatansPPTK = (clone $queryBku)->whereNotNull('kode_sub_kegiatan')->pluck('kode_sub_kegiatan')->unique();

    $uangPagu = \App\Models\Anggaran::where('tahun', $tahun)
        ->whereIn('kode', $subKegiatansPPTK)
        ->get()
        ->sum(function ($a) {
            return $a->getPaguRecursive(); });

    echo "PPTK: " . $pptk->nama . " - Total Kuitansi: " . $queryBku->sum('nominal') . " - Total Pagu: " . $uangPagu . "\n";
    echo "  Tervalidasi: " . (clone $queryBku)->where('status_validasi', 1)->sum('nominal') . "\n";
    echo "  Belum: " . (clone $queryBku)->where('status_validasi', 0)->sum('nominal') . "\n";
}
