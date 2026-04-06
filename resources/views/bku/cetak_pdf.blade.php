<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kas Umum - {{ $selectedJenisPencairan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
            padding: 5px;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .header-title {
            font-size: 16px;
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
                <th width="8%">Tanggal</th>
                <th width="15%">No. Bukti</th>
                <th width="10%">Pencairan</th>
                <th width="34%">Uraian</th>
                <th width="15%">PPTK</th>
                <th width="15%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse($transaksis as $index => $t)
                @php $total += $t->nominal; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $t->no_bukti }}</td>
                    <td class="text-center">{{ $t->jenis_pencairan }}</td>
                    <td>{{ $t->uraian }}<br><small><i>Penerima: {{ $t->penerima }}</i></small></td>
                    <td>{{ $t->nama_pptk ?? ($t->pptk ? $t->pptk->nama : '-') }}</td>
                    <td class="text-right">{{ number_format($t->nominal, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right font-bold">TOTAL</th>
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
