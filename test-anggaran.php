<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('role', 'pptk')->first();
$tahun = \App\Models\BkuTransaksi::max('tanggal');
$tahun = date('Y', strtotime($tahun)); // Use actual year from data
echo "Tahun BKU: $tahun \n";
echo "PPTK ID: " . $user->pejabat_id . "\n";

$queryBku = \App\Models\BkuTransaksi::whereYear('tanggal', $tahun)->where('pptk_id', $user->pejabat_id);
$subKegiatansPPTK = (clone $queryBku)->whereNotNull('kode_sub_kegiatan')->pluck('kode_sub_kegiatan')->unique();

echo "\nSub Kegiatans in BKU for PPTK:\n";
print_r($subKegiatansPPTK->toArray());

$anggaran = \App\Models\Anggaran::where('tahun', $tahun)
    ->whereIn('kode', $subKegiatansPPTK)
    ->get()
    ->sum(function ($a) {
        return $a->getPaguRecursive(); });

echo "\nTotal Pagu PPTK (Sum of Pagu Recursive for each Sub Kegiatan): $anggaran\n";

// Let's also check if there is an Anggaran that has exactly 4.931.000 for this PPTK
$transactions = \App\Models\BkuTransaksi::whereYear('tanggal', $tahun)->where('pptk_id', $user->pejabat_id)->get();
echo "\nTransactions for this PPTK:\n";
foreach ($transactions as $t) {
    echo $t->no_bukti . " - " . $t->kode_sub_kegiatan . " - " . $t->nominal . "\n";
}

echo "\nSum of all Kuitansi for this PPTK: " . $transactions->sum('nominal') . "\n";
