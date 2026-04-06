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
                <th width="23%">Uraian</th>
                <th width="10%">Penerima</th>
                <th width="8%">PPTK</th>
                <th width="8%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse($transaksis as $index => $t)
                @php 
                    $total += $t->nominal; 
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $t->no_bukti }}</td>
                    <td class="text-center">{{ $t->jenis_pencairan }}</td>
                    <td>{{ $t->kode_rekening }}</td>
                    <td>{{ $t->kode_sub_kegiatan }}</td>
                    <td>{{ $t->nama_sub_kegiatan }}</td>
                    <td>
                        {{ $t->uraian }}
                        @php
                            $taxes = [];
                            if($t->pph21 > 0) $taxes[] = 'PPh 21: Rp ' . number_format($t->pph21, 2, ',', '.');
                            if($t->pph22 > 0) $taxes[] = 'PPh 22: Rp ' . number_format($t->pph22, 2, ',', '.');
                            if($t->pph23 > 0) $taxes[] = 'PPh 23: Rp ' . number_format($t->pph23, 2, ',', '.');
                            if($t->ppn > 0) $taxes[] = 'PPN: Rp ' . number_format($t->ppn, 2, ',', '.');
                            if($t->pajak_daerah > 0) $taxes[] = 'Pjk Daerah: Rp ' . number_format($t->pajak_daerah, 2, ',', '.');
                            if($t->pph4_final > 0) $taxes[] = 'PPh 4 Final: Rp ' . number_format($t->pph4_final, 2, ',', '.');
                        @endphp
                        @if(count($taxes) > 0)
                            <br><br><small><i>Pajak:<br>{!! implode('<br>', $taxes) !!}</i></small>
                        @endif
                    </td>
                    <td>{{ $t->penerima }}</td>
                    <td>{{ $t->nama_pptk ?? ($t->pptk ? $t->pptk->nama : '-') }}</td>
                    <td class="text-right">{{ number_format($t->nominal, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="10" class="text-right font-bold">TOTAL</th>
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
