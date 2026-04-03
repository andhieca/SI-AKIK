<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Look for Anggaran matching 4931000
$allAnggaran = \App\Models\Anggaran::all();
echo "Anggaran with exact or close pagu:\n";
foreach ($allAnggaran as $a) {
    if ($a->pagu >= 4000000 && $a->pagu <= 5000000) {
        echo $a->kode . " - " . $a->uraian . " - Pagu: " . $a->pagu . "\n";
    }
}

// Find unvalidated/validated sum for PPTK 2
$transactions = \App\Models\BkuTransaksi::where('pptk_id', 2)->get();
echo "\nTotal Unvalidated nominal PPTK 2: " . $transactions->where('status_validasi', false)->sum('nominal');
echo "\nTotal Validated nominal PPTK 2: " . $transactions->where('status_validasi', true)->sum('nominal');
