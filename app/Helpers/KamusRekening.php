<?php

namespace App\Helpers;

class KamusRekening
{
    /**
     * Daftar Kamus Standar Kode Rekening Belanja.
     * Bersumber dari klasifikasi standar belanja daerah (SIPD / Permendagri).
     */
    protected static array $daftar = [
        ['kode' => '5.1.01.01.01.00001', 'uraian' => 'Belanja Gaji Pokok PNS'],
        ['kode' => '5.1.01.01.01.00002', 'uraian' => 'Belanja Gaji Pokok PPPK'],
        ['kode' => '5.1.01.01.02.00001', 'uraian' => 'Belanja Tunjangan Keluarga PNS'],
        ['kode' => '5.1.01.01.02.00002', 'uraian' => 'Belanja Tunjangan Keluarga PPPK'],
        ['kode' => '5.1.01.01.03.00001', 'uraian' => 'Belanja Tunjangan Jabatan PNS'],
        ['kode' => '5.1.01.01.05.00001', 'uraian' => 'Belanja Tunjangan Fungsional Umum PNS'],
        ['kode' => '5.1.01.01.05.00002', 'uraian' => 'Belanja Tunjangan Fungsional Umum PPPK'],
        ['kode' => '5.1.01.01.06.00001', 'uraian' => 'Belanja Tunjangan Beras PNS'],
        ['kode' => '5.1.01.01.06.00002', 'uraian' => 'Belanja Tunjangan Beras PPPK'],
        ['kode' => '5.1.01.01.07.00001', 'uraian' => 'Belanja Tunjangan PPh/Tunjangan Khusus PNS'],
        ['kode' => '5.1.01.01.08.00001', 'uraian' => 'Belanja Pembulatan Gaji PNS'],
        ['kode' => '5.1.01.01.08.00002', 'uraian' => 'Belanja Pembulatan Gaji PPPK'],
        ['kode' => '5.1.01.01.09.00001', 'uraian' => 'Belanja Iuran Jaminan Kesehatan PNS'],
        ['kode' => '5.1.01.01.09.00002', 'uraian' => 'Belanja Iuran Jaminan Kesehatan PPPK'],
        ['kode' => '5.1.01.02.01.00001', 'uraian' => 'Tambahan Penghasilan berdasarkan Beban Kerja PNS'],
        ['kode' => '5.1.01.02.01.00002', 'uraian' => 'Tambahan Penghasilan berdasarkan Beban Kerja PPPK'],
        ['kode' => '5.1.01.02.03.00001', 'uraian' => 'Tambahan Penghasilan berdasarkan Kondisi Kerja PNS'],
        ['kode' => '5.1.01.02.05.00001', 'uraian' => 'Tambahan Penghasilan berdasarkan Prestasi Kerja PNS'],
        ['kode' => '5.1.01.02.05.00002', 'uraian' => 'Tambahan Penghasilan berdasarkan Prestasi Kerja PPPK'],
        ['kode' => '5.1.02.01.01.0012',  'uraian' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Bahan Pembersih dan Alat Kebersihan'],
        ['kode' => '5.1.02.01.01.0024',  'uraian' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat Tulis Kantor'],
        ['kode' => '5.1.02.01.01.0025',  'uraian' => 'Belanja Alat Bahan untuk Kegiatan Kantor-Kertas dan Cover'],
        ['kode' => '5.1.02.01.01.0026',  'uraian' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Bahan Cetak'],
        ['kode' => '5.1.02.01.01.0029',  'uraian' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Bahan Komputer'],
        ['kode' => '5.1.02.01.01.0030',  'uraian' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Perabot Kantor'],
        ['kode' => '5.1.02.01.01.0031',  'uraian' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat Listrik'],
        ['kode' => '5.1.02.01.01.0036',  'uraian' => 'Belanja Alat/Bahan untuk Kegiatan Kantor-Alat/Bahan untuk Kegiatan Kantor Lainnya'],
        ['kode' => '5.1.02.01.01.0039',  'uraian' => 'Belanja Barang untuk Dijual/Diserahkan kepada Masyarakat'],
        ['kode' => '5.1.02.01.01.0043',  'uraian' => 'Belanja Natura dan Pakan-Natura'],
        ['kode' => '5.1.02.01.01.0052',  'uraian' => 'Belanja Makanan dan Minuman Rapat'],
        ['kode' => '5.1.02.01.01.0053',  'uraian' => 'Belanja Makanan dan Minuman Jamuan Tamu'],
        ['kode' => '5.1.02.01.01.0058',  'uraian' => 'Belanja Makanan dan Minuman Aktivitas Lapangan'],
        ['kode' => '5.1.02.01.01.00127', 'uraian' => 'Belanja Peralatan dan Mesin-Alat Kantor dan Rumah Tangga-Meja dan Kursi Kerja/Rapat Pejabat-Kursi Kerja Pejabat'],
        ['kode' => '5.1.02.02.01.00003', 'uraian' => 'Honorarium Narasumber atau Pembahas, Moderator, Pembawa Acara, dan Panitia'],
        ['kode' => '5.1.02.02.01.00004', 'uraian' => 'Honorarium Tim Pelaksana Kegiatan dan Sekretariat Tim Pelaksana Kegiatan'],
        ['kode' => '5.1.02.02.01.00037', 'uraian' => 'Belanja Jasa Juri Perlombaan/Pertandingan'],
        ['kode' => '5.1.02.02.01.00039', 'uraian' => 'Belanja Jasa Tenaga Informasi dan Teknologi'],
        ['kode' => '5.1.02.02.01.00044', 'uraian' => 'Belanja Jasa Pelayanan Perpustakaan'],
        ['kode' => '5.1.02.02.01.00061', 'uraian' => 'Belanja Tagihan Listrik'],
        ['kode' => '5.1.02.02.01.00067', 'uraian' => 'Belanja Pembayaran Pajak, Bea, dan Perizinan'],
        ['kode' => '5.1.02.02.01.00071', 'uraian' => 'Belanja Lembur'],
        ['kode' => '5.1.02.02.01.00088', 'uraian' => 'Belanja Jasa Pegawai Pemerintah dengan Perjanjian Kerja (PPPK) Paruh Waktu pada Jabatan Operator Layanan Operasional'],
        ['kode' => '5.1.02.02.01.00090', 'uraian' => 'Belanja Jasa Pegawai Pemerintah dengan Perjanjian Kerja (PPPK) Paruh Waktu pada Jabatan Penata Layanan Operasional'],
        ['kode' => '5.1.02.02.02.00006', 'uraian' => 'Belanja Iuran Jaminan Kecelakaan Kerja bagi Non ASN'],
        ['kode' => '5.1.02.02.02.00007', 'uraian' => 'Belanja Iuran Jaminan Kematian bagi Non ASN'],
        ['kode' => '5.1.02.02.02.00019', 'uraian' => 'Belanja Iuran Jaminan Kesehatan bagi PPPK Paruh Waktu pada Jabatan Operator Layanan Operasional'],
        ['kode' => '5.1.02.02.02.00021', 'uraian' => 'Belanja Iuran Jaminan Kesehatan bagi PPPK Paruh Waktu pada Jabatan Penata Layanan Operasional'],
        ['kode' => '5.1.02.02.02.00027', 'uraian' => 'Belanja Iuran Jaminan Kecelakaan Kerja bagi PPPK Paruh Waktu pada Jabatan Operator Layanan Operasional'],
        ['kode' => '5.1.02.02.02.00029', 'uraian' => 'Belanja Iuran Jaminan Kecelakaan Kerja bagi PPPK Paruh Waktu pada Jabatan Penata Layanan Operasional'],
        ['kode' => '5.1.02.02.02.00035', 'uraian' => 'Belanja Iuran Jaminan Kematian bagi PPPK Paruh Waktu pada Jabatan Operator Layanan Operasional'],
        ['kode' => '5.1.02.02.02.00037', 'uraian' => 'Belanja Iuran Jaminan Kematian bagi PPPK Paruh Waktu pada Jabatan Penata Layanan Operasional'],
        ['kode' => '5.1.02.02.04.00117', 'uraian' => 'Belanja Sewa Alat Kantor Lainnya'],
        ['kode' => '5.1.02.02.04.00118', 'uraian' => 'Belanja Sewa Mebel'],
        ['kode' => '5.1.02.02.04.00132', 'uraian' => 'Belanja Sewa Peralatan Studio Audio'],
        ['kode' => '5.1.02.02.05.00009', 'uraian' => 'Belanja Sewa Bangunan Gedung Tempat Pertemuan'],
        ['kode' => '5.1.02.02.05.00032', 'uraian' => 'Belanja Sewa Bangunan Fasilitas Umum'],
        ['kode' => '5.1.02.02.12.00002', 'uraian' => 'Belanja Sosialisasi'],
        ['kode' => '5.1.02.03.02.00038', 'uraian' => 'Belanja Pemeliharaan Alat Angkutan-Alat Angkutan Darat Bermotor-Kendaraan Bermotor Beroda Dua'],
        ['kode' => '5.1.02.03.02.00043', 'uraian' => 'Belanja Pemeliharaan Alat Angkutan-Alat Angkutan Darat Bermotor-Alat Angkutan Darat Bermotor Lainnya'],
        ['kode' => '5.1.02.03.02.00405', 'uraian' => 'Belanja Pemeliharaan Komputer-Komputer Unit-Personal Computer'],
        ['kode' => '5.1.02.03.02.00409', 'uraian' => 'Belanja Pemeliharaan Komputer-Peralatan Komputer-Peralatan Personal Computer'],
        ['kode' => '5.1.02.03.03.00001', 'uraian' => 'Belanja Pemeliharaan Bangunan Gedung-Bangunan Gedung Tempat Kerja-Bangunan Gedung Kantor'],
        ['kode' => '5.1.02.04.01.00001', 'uraian' => 'Belanja Perjalanan Dinas Biasa'],
        ['kode' => '5.1.02.04.01.00003', 'uraian' => 'Belanja Perjalanan Dinas Dalam Kota'],
        ['kode' => '5.1.02.04.01.00004', 'uraian' => 'Belanja Perjalanan Dinas Paket Meeting Dalam Kota'],
        ['kode' => '5.1.02.04.01.00005', 'uraian' => 'Belanja Perjalanan Dinas Paket Meeting Luar Kota'],
        ['kode' => '5.2.02.05.01.00004', 'uraian' => 'Belanja Modal Alat Penyimpan Perlengkapan Kantor'],
        ['kode' => '5.2.02.05.02.00001', 'uraian' => 'Belanja Modal Mebel'],
        ['kode' => '5.2.02.05.02.00006', 'uraian' => 'Belanja Modal Alat Rumah Tangga Lainnya (Home Use)'],
        ['kode' => '5.2.02.06.01.00001', 'uraian' => 'Belanja Modal Peralatan Studio Audio'],
        ['kode' => '5.2.02.06.01.00002', 'uraian' => 'Belanja Modal Peralatan Studio Video dan Film'],
        ['kode' => '5.2.02.07.02.00005', 'uraian' => 'Belanja Modal Alat Kesehatan Umum Lainnya'],
        ['kode' => '5.2.02.10.01.00002', 'uraian' => 'Belanja Modal Personal Computer'],
        ['kode' => '5.2.02.10.02.00003', 'uraian' => 'Belanja Modal Peralatan Personal Computer'],
    ];

    /**
     * Dapatkan semua data kamus sebagai collection of objects.
     */
    public static function all(): \Illuminate\Support\Collection
    {
        return collect(self::$daftar)->map(function ($item, $index) {
            return (object)[
                'id' => $index + 1,
                'kode' => $item['kode'],
                'uraian' => $item['uraian'],
            ];
        });
    }

    /**
     * Normalisasi kode rekening untuk pencocokan toleran zero-padding
     * Contoh: '5.1.02.01.01.0024' dan '5.1.02.01.01.00024' keduanya menjadi '5.1.2.1.1.24'
     */
    public static function normalize(string $kode): string
    {
        $parts = explode('.', trim($kode));
        $normalized = array_map(function ($part) {
            return ltrim($part, '0') ?: '0';
        }, $parts);
        return implode('.', $normalized);
    }

    /**
     * Cari Uraian berdasarkan kode rekening.
     * Mencoba exact match terlebih dahulu, lalu normalized match.
     */
    public static function getUraian(?string $kode): string
    {
        if (!$kode) {
            return '-';
        }

        $trimmedKode = trim($kode);

        // 1. Coba exact match
        foreach (self::$daftar as $item) {
            if ($item['kode'] === $trimmedKode) {
                return $item['uraian'];
            }
        }

        // 2. Coba normalized match (toleran jumlah angka 0 pada segmen terakhir / tengah)
        $norm = self::normalize($trimmedKode);
        foreach (self::$daftar as $item) {
            if (self::normalize($item['kode']) === $norm) {
                return $item['uraian'];
            }
        }

        return '-';
    }
}
