<?php

namespace Database\Factories;

use App\Models\Pejabat;
use Illuminate\Database\Eloquent\Factories\Factory;

class PejabatFactory extends Factory
{
    protected $model = Pejabat::class;

    public function definition(): array
    {
        return [
            'nama'    => fake()->name(),
            'jabatan' => fake()->randomElement(['PPTK', 'Bendahara', 'Camat']),
            'nip'     => fake()->numerify('####################'),
        ];
    }

    public function pptk(): static
    {
        return $this->state(['jabatan' => 'PPTK']);
    }

    public function camat(): static
    {
        return $this->state(['jabatan' => 'Camat']);
    }

    public function bendahara(): static
    {
        return $this->state(['jabatan' => 'Bendahara']);
    }
}
