<!DOCTYPE html>
<html>

<head>
    <title>Kwitansi</title>
    <style>
        @page {
            size: 21.5cm 33cm portrait;
            margin: 1cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        .container {
            width: 100%;
            border: 3px double #000;
            padding: 3px;
            box-sizing: border-box;
            height: 45%;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .header h3 {
            margin: 0;
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
        }

        .header h4 {
            margin: 2px 0;
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
        }

        .sub-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .main-table {
            width: 100%;
            margin-top: 10px;
            border-top: 3px double #000;
            border-bottom: 3px double #000;
            padding: 10px 0;
        }

        .main-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .amount-words {
            font-style: italic;
        }

        .calculation-table {
            width: 100%;
            margin-top: 10px;
        }

        .calculation-table td {
            padding: 2px;
        }

        .signatures {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .signatures td {
            border: 1px solid #000;
            text-align: center;
            vertical-align: top;
            padding: 5px;
            width: 25%;
            height: 100px;
            position: relative;
            font-size: 9pt;
        }

        .signature-title {
            margin-bottom: 40px;
            font-weight: bold;
            font-size: 9pt;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
        }

        .signature-nip {
            font-size: 10pt;
            position: absolute;
            bottom: 5px;
            left: 0;
            right: 0;
        }

        .qr-section {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 6px 10px;
            border: 1px dashed #999;
            border-radius: 4px;
            background: #fafafa;
        }

        .qr-section .qr-image {
            flex-shrink: 0;
        }

        .qr-section .qr-info {
            font-size: 8pt;
            color: #555;
            line-height: 1.5;
        }

        .qr-section .qr-info .qr-title {
            font-weight: bold;
            font-size: 9pt;
            color: #333;
            margin-bottom: 2px;
        }

        .qr-section .qr-info .qr-hash {
            font-family: monospace;
            font-size: 7pt;
            color: #888;
            word-break: break-all;
        }

        @media print {
            .qr-section {
                border-color: #ccc;
                background: #fff;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h3>PEMERINTAH KABUPATEN BANDUNG</h3>
            <h4>KECAMATAN PASIRJAMBU</h4>
        </div>

        <div class="sub-header">KUITANSI</div>

        <table style="width: 100%; border: none; margin-bottom: 5px;">
            <tr>
                <td style="vertical-align: top;">
                    <table class="info-table">
                        <tr>
                            <td width="120">Tahun Anggaran</td>
                            <td width="10">:</td>
                            <td>{{ \Carbon\Carbon::parse($bku->tanggal)->year }}</td>
                        </tr>
                        <tr>
                            <td>Nomor Kuitansi</td>
                            <td>:</td>
                            <td>{{ $bku->no_bukti }}</td>
                        </tr>
                        <tr>
                            <td>Kode Rekening</td>
                            <td>:</td>
                            <td>{{ $bku->kode_rekening }}</td>
                        </tr>
                        @if($bku->kode_sub_kegiatan)
                            <tr>
                                <td>Kode Sub Kegiatan</td>
                                <td>:</td>
                                <td>{{ $bku->kode_sub_kegiatan }}</td>
                            </tr>
                        @endif
                        @if($bku->nama_sub_kegiatan)
                            <tr>
                                <td>Nama Sub Kegiatan</td>
                                <td>:</td>
                                <td>{{ $bku->nama_sub_kegiatan }}</td>
                            </tr>
                        @endif
                    </table>
                </td>
                @if(isset($qrCodeDataUri) && $qrCodeDataUri)
                    <td style="vertical-align: top; text-align: right; width: 120px;">
                        <div class="qr-section" style="margin-top: 0; display: inline-block;">
                            <div class="qr-image">
                                <img src="{{ $qrCodeDataUri }}" width="100" height="100" style="display: block;">
                            </div>
                        </div>
                    </td>
                @endif
            </tr>
        </table>

        <div class="main-table">
            <table>
                <tr>
                    <td width="120">Terima dari</td>
                    <td width="10">:</td>
                    <td>Bendahara Kecamatan Pasirjambu</td>
                </tr>
                <tr>
                    <td>Uang sejumlah</td>
                    <td></td>
                    <td>
                        <span style="font-weight: bold; margin-right: 20px;">Rp
                            {{ number_format($bku->nominal, 0, ',', '.') }}</span>
                        <span class="amount-words">({{ ucwords($terbilang) }})</span>
                    </td>
                </tr>
                <tr>
                    <td>Untuk Pembayaran</td>
                    <td>:</td>
                    <td>{{ $bku->uraian }}</td>
                </tr>
            </table>

            @php
                $totalPajak = 0;
                $taxes = [
                    'Pajak Daerah' => $bku->pajak_daerah,
                    'PPh 21' => $bku->pph21,
                    'PPh 22' => $bku->pph22,
                    'PPh 23' => $bku->pph23,
                    'PPh Pasal 4 (Final)' => $bku->pph4_final,
                    'PPN' => $bku->ppn,
                ];
            @endphp
            <table class="calculation-table">
                <!-- Gross Amount -->
                <tr>
                    <td width="120">Keterangan</td>
                    <td width="10">:</td>
                    <td width="150">Jumlah Kotor</td>
                    <td width="20"></td>
                    <td width="80"></td>
                    <td width="25" style="text-align: right;">Rp</td>
                    <td width="90" style="text-align: right;">{{ number_format($bku->nominal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                <!-- Individual Taxes -->
                @foreach($taxes as $label => $amount)
                    @if($amount > 0)
                        @php $totalPajak += $amount; @endphp
                        <tr>
                            <td></td>
                            <td></td>
                            <td>- {{ $label }}</td>
                            <td style="text-align: right;">Rp</td>
                            <td style="text-align: right;">{{ number_format($amount, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach

                <!-- Total Tax (Only if taxes exist) -->
                @if($totalPajak > 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Total Potongan Pajak</td>
                        <td></td>
                        <td style="text-align: right;">-</td>
                        <td style="text-align: right;">Rp</td>
                        <td style="text-align: right; border-bottom: 1px solid #000;">
                            {{ number_format($totalPajak, 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                @else
                    <!-- Spacer if no tax -->
                    <tr>
                        <td colspan="8" style="height: 10px;"></td>
                    </tr>
                @endif

                <!-- Net Amount -->
                <tr>
                    <td></td>
                    <td></td>
                    <td style="font-weight: bold;">Jumlah yang diterima</td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold;">Rp</td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ number_format($bku->nominal - $totalPajak, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>

        <table class="signatures">
            <tr>
                <td>
                    <div class="signature-title">
                        Menyetujui<br>
                        {{ $camat ? $camat->jabatan : 'Camat' }} Pasirjambu
                    </div>
                    <div class="signature-name">{{ $camat ? $camat->nama : '...................' }}</div>
                    <div class="signature-nip">NIP. {{ $camat ? $camat->nip : '...................' }}</div>
                </td>
                <td>
                    <div class="signature-title">
                        Mengetahui<br>
                        PPTK
                    </div>
                    <div class="signature-name">{{ $pptk ? $pptk->nama : '...................' }}</div>
                    <div class="signature-nip">NIP. {{ $pptk ? $pptk->nip : '...................' }}</div>
                </td>
                <td>
                    <div class="signature-title">
                        Yang Menyerahkan<br>
                        Bendahara
                    </div>
                    <div class="signature-name">{{ $bendahara ? $bendahara->nama : '...................' }}</div>
                    <div class="signature-nip">NIP. {{ $bendahara ? $bendahara->nip : '...................' }}</div>
                </td>
                <td>
                    <div class="signature-title">
                        Pasirjambu, {{ \Carbon\Carbon::parse($bku->tanggal)->isoFormat('D MMMM Y') }}<br>
                        Yang Menerima
                    </div>
                    <div class="signature-name">{{ $bku->penerima }}</div>
                    <div class="signature-nip">
                        <!-- QR Code for verification could go here if needed -->
                    </div>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>