<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use Illuminate\Http\Request;

class AnggaranController extends Controller
{
    public function index()
    {
        $tahun = session('tahun_anggaran', date('Y'));
        $anggarans = Anggaran::where('tahun', $tahun)
            ->whereNull('parent_id')
            ->with(['childrenRecursive', 'children'])
            ->get();
        return view('anggaran.index', compact('anggarans', 'tahun'));
    }

    private function findParentId($kode, $tahun)
    {
        if (!str_contains($kode, '.')) {
            return null;
        }

        $tempKode = $kode;
        while (str_contains($tempKode, '.')) {
            $lastDotPos = strrpos($tempKode, '.');
            $tempKode = substr($tempKode, 0, $lastDotPos);

            $parent = Anggaran::where('kode', $tempKode)
                ->where('tahun', $tahun)
                ->first();

            if ($parent) {
                return $parent->id;
            }
        }

        return null;
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string',
            'uraian' => 'required|string',
            'tahun' => 'required|integer',
            'parent_id' => 'nullable'
        ]);

        $tahun = $request->tahun;
        $kode = $request->kode;
        $parentId = $request->parent_id ?: $this->findParentId($kode, $tahun);

        Anggaran::create([
            'kode' => $kode,
            'uraian' => $request->uraian,
            'pagu' => 0,
            'tahun' => $tahun,
            'parent_id' => $parentId,
            'jenis' => $parentId ? 'Detail' : 'Murni'
        ]);

        return redirect()->route('anggaran.index')->with('success', 'Data Anggaran berhasil ditambahkan.');
    }

    public function update(Request $request, Anggaran $anggaran)
    {
        $request->validate([
            'kode' => 'required|string',
            'uraian' => 'required|string',
            'pagu' => 'nullable|string',
        ]);

        $pagu = $request->pagu ? (float) str_replace(['.', ','], '', $request->pagu) : $anggaran->pagu;

        $anggaran->update([
            'kode' => $request->kode,
            'uraian' => $request->uraian,
            'pagu' => $pagu
        ]);

        return redirect()->route('anggaran.index')->with('success', 'Data Anggaran berhasil diperbarui.');
    }

    public function updatePaguInline(Request $request, Anggaran $anggaran)
    {
        $paguInput = $request->input('pagu');
        if ($paguInput === null) {
            return response()->json(['success' => false, 'message' => 'Input pagu tidak ditemukan.'], 400);
        }

        $pagu = (float) str_replace(['.', ','], '', $paguInput);
        $anggaran->update(['pagu' => $pagu]);

        return response()->json([
            'success' => true,
            'new_total' => number_format($anggaran->getPaguRecursive(), 0, ',', '.')
        ]);
    }

    public function destroy(Anggaran $anggaran)
    {
        $anggaran->delete();
        return redirect()->route('anggaran.index')->with('success', 'Data Anggaran berhasil dihapus.');
    }

    public function deleteAll()
    {
        $tahun = session('tahun_anggaran', date('Y'));
        Anggaran::where('tahun', $tahun)->delete();
        return redirect()->route('anggaran.index')->with('success', 'Seluruh data anggaran untuk tahun ' . $tahun . ' telah berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        $tahun = session('tahun_anggaran', date('Y'));

        // Optional: clear existing for the year to prevent duplicates on re-import
        // Anggaran::where('tahun', $tahun)->delete();

        $path = $request->file('file')->getRealPath();
        $importedCount = 0;
        $lines = (new \Rap2hpoutre\FastExcel\FastExcel)->import($path);

        // Sort by code length to ensure parents are created before children
        $sortedLines = $lines->sortBy(fn($line) => strlen($line['kode']));

        foreach ($sortedLines as $line) {
            if (empty($line['kode']))
                continue;

            $kode = (string) $line['kode'];
            $parentId = $this->findParentId($kode, $tahun);

            \App\Models\Anggaran::updateOrCreate(
                ['kode' => $kode, 'tahun' => $tahun],
                [
                    'uraian' => $line['uraian'] ?? '-',
                    'pagu' => (float) str_replace(['.', ','], '', ($line['anggaran'] ?? $line['pagu'] ?? 0)),
                    'parent_id' => $parentId,
                    'jenis' => $parentId ? 'Detail' : 'Murni'
                ]
            );

            $importedCount++;
        }

        return redirect()->route('anggaran.index')->with('success', "Berhasil mengimpor $importedCount data anggaran.");
    }
}
