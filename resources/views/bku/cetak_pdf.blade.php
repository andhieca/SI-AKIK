<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kas Umum - {{ $selectedJenisPencairan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .w-full { width: 100%; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .header-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="text-center mb-4">
        <div class="header-title">BUKU KAS UMUM</div>
        <div class="mb-2">Tahun Anggaran {{ $selectedYear }}</div>
        @if($selectedJenisPencairan !== 'all')
            <div class="font-bold">Jenis Pencairan: {{ $selectedJenisPencairan }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="6%">Tanggal</th>
                <th width="8%">No. Bukti</th>
                <th width="6%">Pencairan</th>
                <th width="9%">Kode Rek. Belanja</th>
                <th width="7%">Kode Sub Kegiatan</th>
                <th width="12%">Nama Sub Kegiatan</th>
                <th width="15%">Uraian</th>
                <th width="10%">Penerima</th>
                <th width="8%">PPTK</th>
                <th width="8%">Pajak</th>
                <th width="8%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; $totalPajak = 0; @endphp
            @forelse($transaksis as $index => $t)
                @php 
                    $total += $t->nominal; 
                    $pajak = $t->pph21 + $t->pph22 + $t->pph23 + $t->ppn + $t->pajak_daerah + $t->pph4_final;
                    $totalPajak += $pajak;
                @endphp
                @php
                    $pajakItems = [
                        'PPh 21' => $t->pph21,
                        'PPh 22' => $t->pph22,
                        'PPh 23' => $t->pph23,
                        'PPh Pasal 4 (Final)' => $t->pph4_final,
                        'PPN' => $t->ppn,
                        'Pajak Daerah' => $t->pajak_daerah,
                    ];
                    $activePajak = array_filter($pajakItems, fn($v) => $v > 0);
                    $pajakCount = count($activePajak);
                @endphp
                <tr>
                    <td class="text-center" @if($pajakCount > 0) rowspan="{{ $pajakCount + 1 }}" @endif>{{ $index + 1 }}</td>
                    <td class="text-center" @if($pajakCount > 0) rowspan="{{ $pajakCount + 1 }}" @endif>{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td @if($pajakCount > 0) rowspan="{{ $pajakCount + 1 }}" @endif>{{ $t->no_bukti }}</td>
                    <td class="text-center" @if($pajakCount > 0) rowspan="{{ $pajakCount + 1 }}" @endif>{{ $t->jenis_pencairan }}</td>
                    <td @if($pajakCount > 0) rowspan="{{ $pajakCount + 1 }}" @endif>{{ $t->kode_rekening }}</td>
                    <td @if($pajakCount > 0) rowspan="{{ $pajakCount + 1 }}" @endif>{{ $t->kode_sub_kegiatan }}</td>
                    <td @if($pajakCount > 0) rowspan="{{ $pajakCount + 1 }}" @endif>{{ $t->nama_sub_kegiatan }}</td>
                    <td>{{ $t->uraian }}</td>
                    <td @if($pajakCount > 0) rowspan="{{ $pajakCount + 1 }}" @endif>{{ $t->penerima }}</td>
                    <td @if($pajakCount > 0) rowspan="{{ $pajakCount + 1 }}" @endif>{{ $t->nama_pptk ?? ($t->pptk ? $t->pptk->nama : '-') }}</td>
                    <td class="text-center">-</td>
                    <td class="text-right">{{ number_format($t->nominal, 2, ',', '.') }}</td>
                </tr>
                @foreach($activePajak as $pajakLabel => $pajakNominal)
                    <tr>
                        <td style="font-style: italic; font-size: 8px; padding-left: 8px;">↳ Potongan {{ $pajakLabel }}</td>
                        <td class="text-right">{{ number_format($pajakNominal, 2, ',', '.') }}</td>
                        <td></td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="12" class="text-center">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="10" class="text-right font-bold">TOTAL</th>
                <th class="text-right font-bold">{{ number_format($totalPajak, 2, ',', '.') }}</th>
                <th class="text-right font-bold">{{ number_format($total, 2, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <table style="border: none; margin-top: 30px;">
        <tr style="border: none;">
            <td style="border: none; width: 33%; text-align: center;">
                <br>
                Mengetahui,<br>
                Camat<br>
                <br><br><br><br>
                <b><u>{{ $transaksis->first()->nama_camat ?? '............................' }}</u></b><br>
                NIP. {{ $transaksis->first()->nip_camat ?? '............................' }}
            </td>
            <td style="border: none; width: 34%; text-align: center;">
            </td>
            <td style="border: none; width: 33%; text-align: center;">
                Pasirjambu, {{ date('d F Y') }}<br>
                Bendahara Pengeluaran<br>
                <br><br><br><br>
                <b><u>{{ $transaksis->first()->nama_bendahara ?? '............................' }}</u></b><br>
                NIP. {{ $transaksis->first()->nip_bendahara ?? '............................' }}
            </td>
        </tr>
    </table>
</body>
</html>
