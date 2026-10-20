<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Realisasi - {{ $selectedKode }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
        }

        @page {
            size: A4 landscape;
            margin: 1.5cm 2cm;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .page-container {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }

        @media screen {
            body {
                background: #e5e7eb;
                padding: 20px;
            }
            .page-container {
                max-width: 1200px;
                margin: 0 auto;
                background: #fff;
                padding: 40px 50px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                border-radius: 4px;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: #166534;
            color: #fff;
            border: none;
            padding: 12px 28px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(22, 101, 52, 0.3);
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .print-button:hover {
            background: #14532d;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(22, 101, 52, 0.4);
        }

        /* Header */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }

        .report-header h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .report-header h2 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .report-header p {
            font-size: 10pt;
            color: #333;
        }

        /* Info Section */
        .report-info {
            margin-bottom: 15px;
        }

        .report-info table {
            border-collapse: collapse;
        }

        .report-info td {
            padding: 2px 8px 2px 0;
            font-size: 10pt;
            vertical-align: top;
        }

        .report-info td:first-child {
            font-weight: bold;
            white-space: nowrap;
            width: 140px;
        }

        .report-info td:nth-child(2) {
            width: 15px;
            text-align: center;
        }

        /* Main Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        .report-table th {
            background: #166534;
            color: #fff;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #0f5132;
        }

        .report-table td {
            padding: 5px 6px;
            border: 1px solid #999;
            vertical-align: top;
        }

        .report-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .report-table tbody tr:hover {
            background: #e8f5e9;
        }

        .report-table .text-right {
            text-align: right;
        }

        .report-table .text-center {
            text-align: center;
        }

        .report-table .text-left {
            text-align: left;
        }

        .report-table tfoot td {
            font-weight: bold;
            background: #e8f5e9;
            padding: 8px 6px;
        }

        /* Summary Box */
        .summary-box {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .summary-table {
            border-collapse: collapse;
            font-size: 10pt;
        }

        .summary-table td {
            padding: 4px 12px;
            border: 1px solid #999;
        }

        .summary-table td:first-child {
            font-weight: bold;
            background: #f0fdf4;
        }

        .summary-table td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .summary-table .total-row td {
            background: #166534;
            color: #fff;
        }

        /* Signatures */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 250px;
        }

        .signature-box .title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 60px;
        }

        .signature-box .name {
            font-weight: bold;
            font-size: 10pt;
            text-decoration: underline;
        }

        .signature-box .nip {
            font-size: 9pt;
            color: #333;
        }

        /* Period label */
        .period-label {
            font-style: italic;
            font-size: 10pt;
            color: #555;
        }
    </style>
</head>
<body>
    {{-- Print Button (screen only) --}}
    <button onclick="window.print()" class="print-button no-print">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Cetak / Print
    </button>

    <div class="page-container">
        {{-- Report Header --}}
        <div class="report-header">
            <h1>Laporan Realisasi Anggaran</h1>
            <h2>Berdasarkan Kode Rekening</h2>
            <p>Tahun Anggaran {{ $tahun }}</p>
        </div>

        {{-- Report Info --}}
        <div class="report-info">
            <table>
                <tr>
                    <td>Kode Rekening</td>
                    <td>:</td>
                    <td><strong>{{ $selectedKode }}</strong></td>
                </tr>
                <tr>
                    <td>Uraian</td>
                    <td>:</td>
                    <td>{{ $uraianRekening }}</td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td>:</td>
                    <td class="period-label">
                        @if($periodeAwal && $periodeAkhir)
                            {{ \Carbon\Carbon::parse($periodeAwal)->format('d F Y') }} s.d. {{ \Carbon\Carbon::parse($periodeAkhir)->format('d F Y') }}
                        @elseif($periodeAwal)
                            Mulai {{ \Carbon\Carbon::parse($periodeAwal)->format('d F Y') }}
                        @elseif($periodeAkhir)
                            Sampai {{ \Carbon\Carbon::parse($periodeAkhir)->format('d F Y') }}
                        @else
                            Seluruh Tahun {{ $tahun }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Cetak</td>
                    <td>:</td>
                    <td>{{ now()->format('d F Y, H:i') }} WIB</td>
                </tr>
            </table>
        </div>

        {{-- Transaction Table --}}
        @if($transaksis->count() > 0)
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th style="width: 90px;">Tanggal</th>
                        <th style="width: 100px;">No. Bukti</th>
                        <th>Uraian</th>
                        <th style="width: 150px;">Penerima</th>
                        <th style="width: 120px;">Nominal (Rp)</th>
                        <th style="width: 80px;">PPh 21</th>
                        <th style="width: 80px;">PPh 22</th>
                        <th style="width: 80px;">PPh 23</th>
                        <th style="width: 80px;">PPN</th>
                        <th style="width: 70px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksis as $index => $trx)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $trx->tanggal->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $trx->no_bukti }}</td>
                            <td class="text-left">{{ $trx->uraian }}</td>
                            <td class="text-left">{{ $trx->penerima }}</td>
                            <td class="text-right">{{ number_format($trx->nominal, 0, ',', '.') }}</td>
                            <td class="text-right">{{ $trx->pph21 ? number_format($trx->pph21, 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $trx->pph22 ? number_format($trx->pph22, 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $trx->pph23 ? number_format($trx->pph23, 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $trx->ppn ? number_format($trx->ppn, 0, ',', '.') : '-' }}</td>
                            <td class="text-center">{{ $trx->status_validasi ? '✓' : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">TOTAL REALISASI</td>
                        <td class="text-right">{{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($transaksis->sum('pph21'), 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($transaksis->sum('pph22'), 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($transaksis->sum('pph23'), 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($transaksis->sum('ppn'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @else
            <p style="text-align: center; padding: 40px 0; color: #666;">Tidak ada transaksi untuk ditampilkan.</p>
        @endif

        {{-- Summary --}}
        <div class="summary-box">
            <table class="summary-table">
                <tr>
                    <td>Pagu Anggaran</td>
                    <td>Rp {{ number_format($paguAnggaran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Realisasi</td>
                    <td>Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Pajak</td>
                    <td>Rp {{ number_format($totalPajak, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td>Sisa Anggaran</td>
                    <td>Rp {{ number_format($sisaAnggaran, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="title">Bendahara Pengeluaran Pembantu</div>
                @if($bendahara)
                    <div class="name">{{ $bendahara->nama }}</div>
                    <div class="nip">NIP. {{ $bendahara->nip }}</div>
                @else
                    <div class="name">____________________</div>
                    <div class="nip">NIP. ____________________</div>
                @endif
            </div>

            <div class="signature-box">
                <div class="title">Mengetahui,<br>Camat</div>
                @if($camat)
                    <div class="name">{{ $camat->nama }}</div>
                    <div class="nip">NIP. {{ $camat->nip }}</div>
                @else
                    <div class="name">____________________</div>
                    <div class="nip">NIP. ____________________</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
