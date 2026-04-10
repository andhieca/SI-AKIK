<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Pajak - {{ $selectedYear }}</title>
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
        .sub-title {
            font-size: 11px;
            font-weight: bold;
            margin-top: 2px;
        }
        .filter-info {
            font-size: 10px;
            margin-top: 4px;
            color: #555;
        }
        .summary-table {
            width: auto;
            margin: 15px auto 0;
        }
        .summary-table td, .summary-table th {
            padding: 5px 15px;
        }
    </style>
</head>
<body>
    <div class="text-center mb-4">
        <div class="header-title">LAPORAN REKAPITULASI PAJAK</div>
        <div class="sub-title">Kecamatan Pasirjambu - Tahun Anggaran {{ $selectedYear }}</div>
        <div class="filter-info">
            Periode: {{ $filterBulan }} | Jenis Pencairan: {{ $filterPencairan }}
        </div>
    </div>

    {{-- Summary Table --}}
    <table class="summary-table">
        <tr>
            <th>PPh 21</th>
            <th>PPh 22</th>
            <th>PPh 23</th>
            <th>PPh Pasal 4(2)</th>
            <th>PPN</th>
            <th>Pajak Daerah</th>
            <th>Total Pajak</th>
        </tr>
        <tr>
            <td class="text-right font-bold">{{ number_format($totalPajak['pph21'], 0, ',', '.') }}</td>
            <td class="text-right font-bold">{{ number_format($totalPajak['pph22'], 0, ',', '.') }}</td>
            <td class="text-right font-bold">{{ number_format($totalPajak['pph23'], 0, ',', '.') }}</td>
            <td class="text-right font-bold">{{ number_format($totalPajak['pph4_final'], 0, ',', '.') }}</td>
            <td class="text-right font-bold">{{ number_format($totalPajak['ppn'], 0, ',', '.') }}</td>
            <td class="text-right font-bold">{{ number_format($totalPajak['pajak_daerah'], 0, ',', '.') }}</td>
            <td class="text-right font-bold" style="background: #e8f5e9;">{{ number_format($grandTotalPajak, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Detail Table --}}
    <table style="margin-top: 15px;">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="6%">Tanggal</th>
                <th width="9%">No. Bukti</th>
                <th width="6%">Pencairan</th>
                <th width="20%">Uraian</th>
                <th width="9%">Penerima</th>
                <th width="9%">Nominal Bruto</th>
                <th width="7%">PPh 21</th>
                <th width="7%">PPh 22</th>
                <th width="7%">PPh 23</th>
                <th width="7%">PPh 4(2)</th>
                <th width="7%">PPN</th>
                <th width="7%">Pjk Daerah</th>
                <th width="8%">Total Pajak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $index => $t)
                @php
                    $totalRow = $t->pph21 + $t->pph22 + $t->pph23 + $t->pph4_final + $t->ppn + $t->pajak_daerah;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $t->no_bukti }}</td>
                    <td class="text-center">{{ $t->jenis_pencairan }}</td>
                    <td>{{ $t->uraian }}</td>
                    <td>{{ $t->penerima }}</td>
                    <td class="text-right">{{ number_format($t->nominal, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $t->pph21 > 0 ? number_format($t->pph21, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">{{ $t->pph22 > 0 ? number_format($t->pph22, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">{{ $t->pph23 > 0 ? number_format($t->pph23, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">{{ $t->pph4_final > 0 ? number_format($t->pph4_final, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">{{ $t->ppn > 0 ? number_format($t->ppn, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">{{ $t->pajak_daerah > 0 ? number_format($t->pajak_daerah, 0, ',', '.') : '-' }}</td>
                    <td class="text-right font-bold">{{ number_format($totalRow, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data transaksi pajak.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right font-bold">TOTAL</th>
                <th class="text-right font-bold">{{ number_format($transaksis->sum('nominal'), 0, ',', '.') }}</th>
                <th class="text-right font-bold">{{ number_format($totalPajak['pph21'], 0, ',', '.') }}</th>
                <th class="text-right font-bold">{{ number_format($totalPajak['pph22'], 0, ',', '.') }}</th>
                <th class="text-right font-bold">{{ number_format($totalPajak['pph23'], 0, ',', '.') }}</th>
                <th class="text-right font-bold">{{ number_format($totalPajak['pph4_final'], 0, ',', '.') }}</th>
                <th class="text-right font-bold">{{ number_format($totalPajak['ppn'], 0, ',', '.') }}</th>
                <th class="text-right font-bold">{{ number_format($totalPajak['pajak_daerah'], 0, ',', '.') }}</th>
                <th class="text-right font-bold" style="background: #e8f5e9;">{{ number_format($grandTotalPajak, 0, ',', '.') }}</th>
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
