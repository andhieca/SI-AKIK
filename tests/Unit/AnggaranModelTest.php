<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Anggaran;
use App\Models\BkuTransaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnggaranModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_pagu_recursive_returns_own_pagu_for_leaf_node(): void
    {
        $leaf = Anggaran::factory()->create([
            'pagu'      => 5000000,
            'tahun'     => 2026,
            'parent_id' => null,
        ]);

        // No children — should return its own pagu
        $this->assertEquals(5000000, $leaf->getPaguRecursive());
    }

    /** @test */
    public function get_pagu_recursive_sums_children_pagu(): void
    {
        $parent = Anggaran::factory()->create([
            'pagu'      => 999, // value doesn't matter for non-leaf
            'tahun'     => 2026,
            'parent_id' => null,
        ]);

        Anggaran::factory()->create(['pagu' => 2000000, 'tahun' => 2026, 'parent_id' => $parent->id]);
        Anggaran::factory()->create(['pagu' => 3000000, 'tahun' => 2026, 'parent_id' => $parent->id]);

        $this->assertEquals(5000000, $parent->fresh()->getPaguRecursive());
    }

    /** @test */
    public function get_pagu_recursive_handles_nested_hierarchy(): void
    {
        // Program > Kegiatan > Sub Kegiatan
        $program = Anggaran::factory()->create(['pagu' => 0, 'tahun' => 2026, 'parent_id' => null]);
        $kegiatan = Anggaran::factory()->create(['pagu' => 0, 'tahun' => 2026, 'parent_id' => $program->id]);
        $sub1 = Anggaran::factory()->create(['pagu' => 1000000, 'tahun' => 2026, 'parent_id' => $kegiatan->id]);
        $sub2 = Anggaran::factory()->create(['pagu' => 2000000, 'tahun' => 2026, 'parent_id' => $kegiatan->id]);

        $this->assertEquals(3000000, $program->fresh()->getPaguRecursive());
        $this->assertEquals(3000000, $kegiatan->fresh()->getPaguRecursive());
    }

    /** @test */
    public function get_realisasi_returns_zero_when_no_transactions(): void
    {
        $leaf = Anggaran::factory()->create([
            'pagu'      => 5000000,
            'kode'      => '1.01.01.2.01.01',
            'tahun'     => 2026,
            'parent_id' => null,
        ]);

        $this->assertEquals(0, $leaf->getRealisasi());
    }

    /** @test */
    public function get_realisasi_sums_bku_transaksi_for_leaf(): void
    {
        $leaf = Anggaran::factory()->create([
            'kode'      => '1.01.01.2.01.01',
            'tahun'     => 2026,
            'parent_id' => null,
        ]);

        BkuTransaksi::factory()->create([
            'kode_sub_kegiatan' => '1.01.01.2.01.01',
            'nominal'           => 500000,
            'tanggal'           => '2026-03-01',
        ]);

        BkuTransaksi::factory()->create([
            'kode_sub_kegiatan' => '1.01.01.2.01.01',
            'nominal'           => 300000,
            'tanggal'           => '2026-03-15',
        ]);

        $this->assertEquals(800000, $leaf->fresh()->getRealisasi());
    }

    /** @test */
    public function get_realisasi_ignores_different_year(): void
    {
        $leaf = Anggaran::factory()->create([
            'kode'      => '1.01.01.2.01.02',
            'tahun'     => 2026,
            'parent_id' => null,
        ]);

        BkuTransaksi::factory()->create([
            'kode_sub_kegiatan' => '1.01.01.2.01.02',
            'nominal'           => 1000000,
            'tanggal'           => '2025-12-31', // different year
        ]);

        $this->assertEquals(0, $leaf->fresh()->getRealisasi());
    }

    /** @test */
    public function get_realisasi_sums_children_recursively(): void
    {
        $parent = Anggaran::factory()->create(['kode' => 'P.01', 'tahun' => 2026, 'parent_id' => null]);
        $child1 = Anggaran::factory()->create(['kode' => 'C.01', 'tahun' => 2026, 'parent_id' => $parent->id]);
        $child2 = Anggaran::factory()->create(['kode' => 'C.02', 'tahun' => 2026, 'parent_id' => $parent->id]);

        BkuTransaksi::factory()->create(['kode_sub_kegiatan' => 'C.01', 'nominal' => 400000, 'tanggal' => '2026-02-01']);
        BkuTransaksi::factory()->create(['kode_sub_kegiatan' => 'C.02', 'nominal' => 600000, 'tanggal' => '2026-02-01']);

        $this->assertEquals(1000000, $parent->fresh()->getRealisasi());
    }
}
