<?php

namespace App\Http\Controllers;

use App\Models\Pejabat;
use App\Models\Anggaran;

class SettingController extends Controller
{
    public function index()
    {
        $camat = Pejabat::firstOrCreate(
            ['jabatan' => 'Camat'],
            ['nama' => 'Nama Camat', 'nip' => 'NIP Camat']
        );

        $pptks = Pejabat::where('jabatan', 'PPTK')->get();

        $bendahara = Pejabat::firstOrCreate(
            ['jabatan' => 'Bendahara'],
            ['nama' => 'Nama Bendahara', 'nip' => 'NIP Bendahara']
        );

        return view('settings.index', compact('camat', 'pptks', 'bendahara'));
    }
}
