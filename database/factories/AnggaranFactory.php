<?php

namespace Database\Factories;

use App\Models\Anggaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnggaranFactory extends Factory
{
    protected $model = Anggaran::class;

    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'kode'      => '1.01.' . str_pad($counter, 2, '0', STR_PAD_LEFT),
            'uraian'    => fake()->sentence(4),
            'pagu'      => fake()->numberBetween(1000000, 100000000),
            'tahun'     => 2026,
            'parent_id' => null,
            'jenis'     => 'Murni',
        ];
    }
}
