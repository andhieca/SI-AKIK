<?php

namespace Database\Factories;

use App\Models\BkuTransaksi;
use App\Models\Pejabat;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BkuTransaksiFactory extends Factory
{
    protected $model = BkuTransaksi::class;

    public function definition(): array
    {
        static $seq = 0;
        $seq++;

        return [
            'tanggal'           => fake()->dateTimeBetween('2026-01-01', '2026-12-31')->format('Y-m-d'),
            'no_bukti'          => 'KW/' . str_pad($seq, 3, '0', STR_PAD_LEFT) . '/Kec Psjb/UP/2026',
            'kode_rekening'     => '5.1.02.01.01.0001',
            'kode_sub_kegiatan' => '1.01.01.2.01.01',
            'nama_sub_kegiatan' => 'Penyediaan Alat Tulis Kantor',
            'uraian'            => fake()->sentence(6),
            'penerima'          => fake()->company(),
            'nominal'           => fake()->numberBetween(100000, 10000000),
            'jenis_pencairan'   => 'UP',
            'pptk_id'           => null,
            'pph21'             => 0,
            'pph22'             => 0,
            'pph23'             => 0,
            'ppn'               => 0,
            'pajak_daerah'      => 0,
            'pph4_final'        => 0,
            'status_validasi'   => false,
            'qr_code_hash'      => Str::random(32),
        ];
    }
}
