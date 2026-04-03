<?php

namespace App\Http\Controllers;

use App\Models\Pejabat;
use Illuminate\Http\Request;

class PejabatController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:255|unique:pejabats,nip',
            'jabatan' => 'required|string|in:PPTK',
        ]);

        Pejabat::create($request->all());

        return redirect()->route('settings.index')->with('success', 'Data ' . $request->jabatan . ' berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:255',
        ]);

        $pejabat = Pejabat::findOrFail($id);
        $pejabat->update($request->only('nama', 'nip'));

        return redirect()->route('settings.index')->with('success', 'Data ' . $pejabat->jabatan . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pejabat = Pejabat::findOrFail($id);
        $jabatan = $pejabat->jabatan;
        $pejabat->delete();

        return redirect()->route('settings.index')->with('success', 'Data ' . $jabatan . ' berhasil dihapus.');
    }
}
