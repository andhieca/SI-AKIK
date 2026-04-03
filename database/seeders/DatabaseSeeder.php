<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Users
        User::create([
            'name' => 'Admin Bendahara',
            'email' => 'admin@siakik.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'PPTK',
            'email' => 'pptk@siakik.com',
            'role' => 'pptk',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'Camat',
            'email' => 'camat@siakik.com',
            'role' => 'camat',
            'password' => bcrypt('password'),
        ]);

        // Pejabats
        \App\Models\Pejabat::create([
            'nama' => 'Nia Kania, S.PT., M.I.L',
            'nip' => '197809272010012008', // Adjusted space
            'jabatan' => 'Camat',
        ]);

        \App\Models\Pejabat::create([
            'nama' => 'Usep Rohmat, S.Ag',
            'nip' => '197602132014121001',
            'jabatan' => 'PPTK',
        ]);

        \App\Models\Pejabat::create([
            'nama' => 'Alimin, A.Md., S.M',
            'nip' => '198003162014121003', // Adjusted space
            'jabatan' => 'Bendahara',
        ]);

        // Anggaran 2025
        \App\Models\Anggaran::create([
            'tahun' => 2025,
            'jenis' => 'Operasi',
            'pagu' => 5309505725.00,
        ]);

        \App\Models\Anggaran::create([
            'tahun' => 2025,
            'jenis' => 'Modal',
            'pagu' => 86994500.00,
        ]);
    }
}
