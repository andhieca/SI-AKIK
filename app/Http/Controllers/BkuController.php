<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BkuTransaksi;

class BkuController extends Controller
{
    public function index(Request $request)
    {
        $selectedJenisPencairan = $request->input('jenis_pencairan', 'all');
        $selectedYear = $request->input('year', session('tahun_anggaran', date('Y')));

        $query = \App\Models\BkuTransaksi::with('pptk')
            ->whereYear('tanggal', $selectedYear);

        if (auth()->user()->role === 'pptk') {
            $query->where('pptk_id', auth()->user()->pejabat_id);
        }

        if ($selectedJenisPencairan !== 'all') {
            $query->where('jenis_pencairan', $selectedJenisPencairan);
        }

        $sortColumn = $request->input('sort', 'tanggal');
        $sortDirection = $request->input('direction', 'desc');

        // Ensure only allowed columns can be sorted to prevent SQL injection
        $allowedSortColumns = ['tanggal', 'no_bukti', 'jenis_pencairan', 'uraian', 'nominal', 'status_validasi', 'pptk_id'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'tanggal';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('uraian', 'like', "%{$search}%")
                    ->orWhere('no_bukti', 'like', "%{$search}%")
                    ->orWhere('penerima', 'like', "%{$search}%")
                    ->orWhereHas('pptk', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        // Apply sorting
        if (!$request->has('sort')) {
            // Default sort: Unvalidated first, then newest date
            $query->orderBy('status_validasi', 'asc')
                ->orderBy('tanggal', 'desc');
        } else {
            if ($sortColumn === 'pptk_id') {
                // Sort by PPTK name using a join or subquery
                $query->join('pejabats', 'bku_transaksis.pptk_id', '=', 'pejabats.id')
                    ->orderBy('pejabats.nama', $sortDirection)
                    ->select('bku_transaksis.*');
            } else {
                $query->orderBy($sortColumn, $sortDirection);
            }

            // Fallback secondary sort so items don't jump around randomly
            if ($sortColumn !== 'tanggal') {
                $query->orderBy('tanggal', 'desc');
            }
        }

        $transaksis = $query->paginate(10)
            ->withQueryString();

        $pptks = \App\Models\Pejabat::where('jabatan', 'PPTK')->get();
        $subKegiatans = \App\Models\Anggaran::where('tahun', $selectedYear)
            ->whereDoesntHave('children')
            ->orderBy('kode')
            ->get();

        return view('bku.index', compact('transaksis', 'pptks', 'subKegiatans', 'selectedJenisPencairan', 'selectedYear', 'sortColumn', 'sortDirection'));
    }

    public function store(Request $request)
    {
        // Remove dots or commas from nominal before validation
        // Helper to parse localized date d/m/Y to Y-m-d
        $parseDate = function ($date) {
            if (!$date)
                return null;
            try {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } catch (\Exception $e) {
                return $date; // Return original if parsing fails (fallback to default date handling)
            }
        };

        // Remove dots or commas from nominal before validation
        $request->merge([
            'tanggal' => str_contains($request->tanggal, '/') ? $parseDate($request->tanggal) : $request->tanggal,
            'nominal' => str_replace(['.', ','], '', $request->nominal),
            'pph21' => $request->pph21 ? str_replace(['.', ','], '', $request->pph21) : 0,
            'pph22' => $request->pph22 ? str_replace(['.', ','], '', $request->pph22) : 0,
            'pph23' => $request->pph23 ? str_replace(['.', ','], '', $request->pph23) : 0,
            'ppn' => $request->ppn ? str_replace(['.', ','], '', $request->ppn) : 0,
            'pajak_daerah' => $request->pajak_daerah ? str_replace(['.', ','], '', $request->pajak_daerah) : 0,
            'pph4_final' => $request->pph4_final ? str_replace(['.', ','], '', $request->pph4_final) : 0,
        ]);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'no_bukti' => 'required|string|max:255',
            'kode_rekening' => 'required|string|max:255',
            'kode_sub_kegiatan' => 'nullable|string|max:255',
            'nama_sub_kegiatan' => 'nullable|string|max:255',
            'uraian' => 'required|string',
            'penerima' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'jenis_pencairan' => 'required|string|in:UP,GU 1,GU 2,GU 3,GU 4,GU 5,GU 6,GU 7,GU 8,GU 9,GU 10,GU 11,GU 12',
            'pptk_id' => 'required|exists:pejabats,id',
            'pph21' => 'nullable|numeric|min:0',
            'pph22' => 'nullable|numeric|min:0',
            'pph23' => 'nullable|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0',
            'pajak_daerah' => 'nullable|numeric|min:0',
            'pph4_final' => 'nullable|numeric|min:0',
        ]);

        // Generate QR Hash (Unique string)
        $validated['qr_code_hash'] = \Illuminate\Support\Str::random(32);

        $camat = \App\Models\Pejabat::where('jabatan', 'Camat')->first();
        $bendahara = \App\Models\Pejabat::where('jabatan', 'Bendahara')->first();
        $pptk = \App\Models\Pejabat::find($request->pptk_id);

        $validated['nama_camat'] = $camat?->nama;
        $validated['nip_camat'] = $camat?->nip;
        $validated['nama_bendahara'] = $bendahara?->nama;
        $validated['nip_bendahara'] = $bendahara?->nip;
        $validated['nama_pptk'] = $pptk?->nama;
        $validated['nip_pptk'] = $pptk?->nip;

        \App\Models\BkuTransaksi::create($validated);

        return redirect()->route('bku.index', ['jenis_pencairan' => 'all'])->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(Request $request, \App\Models\BkuTransaksi $bku)
    {
        if ($bku->status_validasi) {
            return redirect()->back()->with('error', 'Transaksi yang sudah divalidasi tidak dapat diubah. Silakan minta PPTK untuk membatalkan validasi terlebih dahulu.');
        }

        // Remove dots or commas from nominal before validation
        // Helper to parse localized date d/m/Y to Y-m-d
        $parseDate = function ($date) {
            if (!$date)
                return null;
            try {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } catch (\Exception $e) {
                return $date;
            }
        };

        // Remove dots or commas from nominal before validation
        $request->merge([
            'tanggal' => str_contains($request->tanggal, '/') ? $parseDate($request->tanggal) : $request->tanggal,
            'nominal' => str_replace(['.', ','], '', $request->nominal),
            'pph21' => $request->pph21 ? str_replace(['.', ','], '', $request->pph21) : 0,
            'pph22' => $request->pph22 ? str_replace(['.', ','], '', $request->pph22) : 0,
            'pph23' => $request->pph23 ? str_replace(['.', ','], '', $request->pph23) : 0,
            'ppn' => $request->ppn ? str_replace(['.', ','], '', $request->ppn) : 0,
            'pajak_daerah' => $request->pajak_daerah ? str_replace(['.', ','], '', $request->pajak_daerah) : 0,
            'pph4_final' => $request->pph4_final ? str_replace(['.', ','], '', $request->pph4_final) : 0,
        ]);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'no_bukti' => 'required|string|max:255',
            'kode_rekening' => 'required|string|max:255',
            'kode_sub_kegiatan' => 'nullable|string|max:255',
            'nama_sub_kegiatan' => 'nullable|string|max:255',
            'uraian' => 'required|string',
            'penerima' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'jenis_pencairan' => 'required|string|in:UP,GU 1,GU 2,GU 3,GU 4,GU 5,GU 6,GU 7,GU 8,GU 9,GU 10,GU 11,GU 12',
            'pptk_id' => 'required|exists:pejabats,id',
            'pph21' => 'nullable|numeric|min:0',
            'pph22' => 'nullable|numeric|min:0',
            'pph23' => 'nullable|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0',
            'pajak_daerah' => 'nullable|numeric|min:0',
            'pph4_final' => 'nullable|numeric|min:0',
        ]);

        if (!$bku->nama_camat) {
            $camat = \App\Models\Pejabat::where('jabatan', 'Camat')->first();
            $validated['nama_camat'] = $camat?->nama;
            $validated['nip_camat'] = $camat?->nip;
        }

        if (!$bku->nama_bendahara) {
            $bendahara = \App\Models\Pejabat::where('jabatan', 'Bendahara')->first();
            $validated['nama_bendahara'] = $bendahara?->nama;
            $validated['nip_bendahara'] = $bendahara?->nip;
        }

        // Always update PPTK snapshots if the ID changes, or if they were missing
        if ($bku->pptk_id != $request->pptk_id || !$bku->nama_pptk) {
            $pptk = \App\Models\Pejabat::find($request->pptk_id);
            $validated['nama_pptk'] = $pptk?->nama;
            $validated['nip_pptk'] = $pptk?->nip;
        }

        $bku->update($validated);

        return redirect()->route('bku.index', ['jenis_pencairan' => 'all'])->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(BkuTransaksi $bku)
    {
        if ($bku->status_validasi) {
            return redirect()->back()->with('error', 'Transaksi yang sudah divalidasi tidak dapat dihapus. Silakan minta PPTK untuk membatalkan validasi terlebih dahulu.');
        }

        $bku->delete();
        return redirect()->route('bku.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function validasi(BkuTransaksi $bku)
    {
        if (auth()->user()->role !== 'pptk') {
            return redirect()->back()->with('error', 'Hanya PPTK yang dapat memvalidasi transaksi.');
        }

        if (auth()->user()->pejabat_id !== $bku->pptk_id) {
            return redirect()->back()->with('error', 'Anda tidak berwenang memvalidasi transaksi ini.');
        }

        $bku->update(['status_validasi' => true]);

        return redirect()->route('bku.index')->with('success', 'Transaksi berhasil divalidasi.');
    }

    public function batalValidasi(BkuTransaksi $bku)
    {
        if (auth()->user()->role !== 'pptk') {
            return redirect()->back()->with('error', 'Hanya PPTK yang dapat membatalkan validasi.');
        }

        if (auth()->user()->pejabat_id !== $bku->pptk_id) {
            return redirect()->back()->with('error', 'Anda tidak berwenang membatalkan validasi transaksi ini.');
        }

        $bku->update(['status_validasi' => false]);

        return redirect()->route('bku.index')->with('success', 'Validasi transaksi berhasil dibatalkan.');
    }
    public function cetak(Request $request)
    {
        $selectedJenisPencairan = $request->input('jenis_pencairan', 'all');
        $selectedYear = $request->input('year', session('tahun_anggaran', date('Y')));

        $query = \App\Models\BkuTransaksi::with('pptk')
            ->whereYear('tanggal', $selectedYear);

        if (auth()->user()->role === 'pptk') {
            $query->where('pptk_id', auth()->user()->pejabat_id);
        }

        if ($selectedJenisPencairan !== 'all') {
            $query->where('jenis_pencairan', $selectedJenisPencairan);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('uraian', 'like', "%{$search}%")
                    ->orWhere('no_bukti', 'like', "%{$search}%")
                    ->orWhere('penerima', 'like', "%{$search}%")
                    ->orWhereHas('pptk', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $sortColumn = $request->input('sort', 'tanggal');
        $sortDirection = $request->input('direction', 'desc');

        $allowedSortColumns = ['tanggal', 'no_bukti', 'jenis_pencairan', 'uraian', 'nominal', 'status_validasi', 'pptk_id'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'tanggal';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        if (!$request->has('sort')) {
            $query->orderBy('status_validasi', 'asc')
                ->orderBy('tanggal', 'asc');
        } else {
            if ($sortColumn === 'pptk_id') {
                $query->join('pejabats', 'bku_transaksis.pptk_id', '=', 'pejabats.id')
                    ->orderBy('pejabats.nama', $sortDirection)
                    ->select('bku_transaksis.*');
            } else {
                $query->orderBy($sortColumn, $sortDirection);
            }
            if ($sortColumn !== 'tanggal') {
                $query->orderBy('tanggal', 'asc');
            }
        }

        $transaksis = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('bku.cetak_pdf', compact('transaksis', 'selectedJenisPencairan', 'selectedYear'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('BKU_' . $selectedJenisPencairan . '_' . $selectedYear . '.pdf');
    }

    public function print(BkuTransaksi $bku)
    {
        $pptk = (object)[
            'nama' => $bku->nama_pptk ?? $bku->pptk?->nama,
            'nip' => $bku->nip_pptk ?? $bku->pptk?->nip,
            'jabatan' => 'PPTK'
        ];

        $camat_db = \App\Models\Pejabat::where('jabatan', 'Camat')->first();
        $camat = (object)[
            'nama' => $bku->nama_camat ?? $camat_db?->nama,
            'nip' => $bku->nip_camat ?? $camat_db?->nip,
            'jabatan' => 'Camat'
        ];

        $bendahara_db = \App\Models\Pejabat::where('jabatan', 'Bendahara')->first();
        $bendahara = (object)[
            'nama' => $bku->nama_bendahara ?? $bendahara_db?->nama,
            'nip' => $bku->nip_bendahara ?? $bendahara_db?->nip,
            'jabatan' => 'Bendahara'
        ];

        $terbilang = $this->terbilang($bku->nominal) . ' Rupiah';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('bku.receipt', compact('bku', 'pptk', 'camat', 'bendahara', 'terbilang'));
        $pdf->setPaper('f4', 'landscape');

        $filename = 'Kwitansi_' . str_replace(['/', '\\'], '_', $bku->no_bukti) . '.pdf';
        return $pdf->stream($filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        $path = $request->file('file')->getRealPath();
        $importedCount = 0;
        $errors = [];

        $pptks = \App\Models\Pejabat::where('jabatan', 'PPTK')->get();
        $tahunAnggaran = session('tahun_anggaran', date('Y'));
        $subKegiatans = \App\Models\Anggaran::where('tahun', $tahunAnggaran)
            ->whereDoesntHave('children')
            ->get();

        $camat = \App\Models\Pejabat::where('jabatan', 'Camat')->first();
        $bendahara = \App\Models\Pejabat::where('jabatan', 'Bendahara')->first();

        $rows = (new \Rap2hpoutre\FastExcel\FastExcel)->import($path);

        foreach ($rows as $index => $row) {
            try {
                // Determine PPTK ID
                $pptkName = $row['pptk'] ?? $row['nama_pptk'] ?? null;
                $pptkId = null;
                $pptkModel = null;
                if ($pptkName) {
                    $pptkModel = $pptks->first(fn($p) => str_contains(strtolower($p->nama), strtolower($pptkName)));
                    $pptkId = $pptkModel ? $pptkModel->id : null;
                }

                if (!$pptkId && $pptks->isNotEmpty()) {
                    $pptkModel = $pptks->first();
                    $pptkId = $pptkModel->id; // Fallback to first PPTK if not found
                }

                // Determine Sub Kegiatan Name if only code is provided
                $kodeSub = $row['kode_sub_kegiatan'] ?? null;
                $namaSub = $row['nama_sub_kegiatan'] ?? null;
                if ($kodeSub && !$namaSub) {
                    $sub = $subKegiatans->firstWhere('kode', $kodeSub);
                    $namaSub = $sub ? $sub->uraian : '-';
                }

                // Parse Date
                $tanggalRaw = $row['tanggal'] ?? null;
                $tanggal = null;
                if ($tanggalRaw) {
                    if ($tanggalRaw instanceof \DateTime) {
                        $tanggal = $tanggalRaw->format('Y-m-d');
                    } else {
                        try {
                            $tanggal = \Carbon\Carbon::parse($tanggalRaw)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $tanggal = date('Y-m-d');
                        }
                    }
                }

                if (!$tanggal || empty($row['no_bukti']) || empty($row['uraian']) || empty($row['jenis_pencairan']) || empty($row['kode_rekening']) || empty($row['penerima'])) {
                    $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap (Tanggal, No Bukti, Kode Rekening, Jenis Pencairan, Uraian, atau Penerima kosong).";
                    continue;
                }

                // Clean nominal
                $cleanNominal = function ($val) {
                    if (!$val)
                        return 0;
                    return (float) str_replace(['.', ','], '', $val);
                };

                \App\Models\BkuTransaksi::create([
                    'tanggal' => $tanggal,
                    'no_bukti' => $row['no_bukti'],
                    'kode_rekening' => $row['kode_rekening'],
                    'kode_sub_kegiatan' => $kodeSub,
                    'nama_sub_kegiatan' => $namaSub,
                    'uraian' => $row['uraian'],
                    'penerima' => $row['penerima'],
                    'nominal' => $cleanNominal($row['nominal'] ?? 0),
                    'jenis_pencairan' => $row['jenis_pencairan'],
                    'pptk_id' => $pptkId,
                    'pph21' => $cleanNominal($row['pph21'] ?? 0),
                    'pph22' => $cleanNominal($row['pph22'] ?? 0),
                    'pph23' => $cleanNominal($row['pph23'] ?? 0),
                    'ppn' => $cleanNominal($row['ppn'] ?? 0),
                    'pajak_daerah' => $cleanNominal($row['pajak_daerah'] ?? 0),
                    'pph4_final' => $cleanNominal($row['pph4_final'] ?? 0),
                    'qr_code_hash' => \Illuminate\Support\Str::random(32),
                    'nama_camat' => $camat?->nama,
                    'nip_camat' => $camat?->nip,
                    'nama_bendahara' => $bendahara?->nama,
                    'nip_bendahara' => $bendahara?->nip,
                    'nama_pptk' => $pptkModel?->nama,
                    'nip_pptk' => $pptkModel?->nip,
                ]);

                $importedCount++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            return redirect()->route('bku.index')->with('import_errors', $errors)->with('success', "Berhasil mengimpor $importedCount data, namun ada beberapa kesalahan.");
        }

        return redirect()->route('bku.index')->with('success', "Berhasil mengimpor $importedCount data transaksi.");
    }

    private function terbilang($nilai)
    {
        $nilai = abs($nilai);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp = '';
        if ($nilai < 12) {
            $temp = ' ' . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->terbilang($nilai - 10) . ' Belas';
        } else if ($nilai < 100) {
            $temp = $this->terbilang($nilai / 10) . ' Puluh' . $this->terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = ' Seratus' . $this->terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->terbilang($nilai / 100) . ' Ratus' . $this->terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = ' Seribu' . $this->terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->terbilang($nilai / 1000) . ' Ribu' . $this->terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->terbilang($nilai / 1000000) . ' Juta' . $this->terbilang($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->terbilang($nilai / 1000000000) . ' Milyar' . $this->terbilang($nilai % 1000000000);
        } else if ($nilai < 1000000000000000) {
            $temp = $this->terbilang($nilai / 1000000000000) . ' Trilyun' . $this->terbilang($nilai % 1000000000000);
        }
        return $temp;
    }

    public function downloadTemplate()
    {
        $data = [
            [
                'tanggal' => date('d/m/Y'),
                'jenis_pencairan' => 'UP',
                'no_bukti' => '001/BKU/' . date('Y'),
                'kode_rekening' => '5.1.02.01.01.0001',
                'kode_sub_kegiatan' => '1.01.01.2.01.01',
                'uraian' => 'Contoh Belanja Alat Tulis Kantor',
                'penerima' => 'Toko Buku Maju',
                'nominal' => 1500000,
                'pptk' => 'Nama PPTK (Sesuai Data Pejabat)',
                'pph21' => 0,
                'pph22' => 0,
                'pph23' => 0,
                'ppn' => 150000,
                'pajak_daerah' => 0,
                'pph4_final' => 0,
            ]
        ];

        return (new \Rap2hpoutre\FastExcel\FastExcel(collect($data)))->download('Format_Import_BKU.xlsx');
    }

    public function generateNoBukti(Request $request)
    {
        $year = $request->input('year', date('Y'));
        if ($request->has('tanggal') && !empty($request->tanggal)) {
            try {
                $year = \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y');
            } catch (\Exception $e) {
                try {
                    $year = \Carbon\Carbon::parse($request->tanggal)->format('Y');
                } catch (\Exception $ex) {
                    // Fallback to current year if parsing fails
                }
            }
        }
        $jenisPencairan = $request->input('jenis_pencairan', '');

        // Generate base number
        $query = \App\Models\BkuTransaksi::whereYear('tanggal', $year)
            ->where('no_bukti', 'like', 'KW/%');
            
        if (!empty($jenisPencairan)) {
            $query->where('jenis_pencairan', $jenisPencairan);
        }
        
        $latest = $query->get();

        $maxUrutan = 0;
        foreach($latest as $item) {
            if (preg_match('/KW\/(\d+)\/Kec Psjb\//', $item->no_bukti, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxUrutan) {
                    $maxUrutan = $num;
                }
            } else if (preg_match('/^KW\/(\d+)\//', $item->no_bukti, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxUrutan) {
                    $maxUrutan = $num;
                }
            }
        }

        $nextUrutan = str_pad($maxUrutan + 1, 3, '0', STR_PAD_LEFT);
        
        $noBukti = "KW/{$nextUrutan}/Kec Psjb";
        if (!empty($jenisPencairan)) {
            $noBukti .= "/{$jenisPencairan}";
        }
        $noBukti .= "/{$year}";

        return response()->json(['no_bukti' => $noBukti]);
    }
}
