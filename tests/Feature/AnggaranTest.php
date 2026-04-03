<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Anggaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnggaranTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pptkUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->pptkUser = User::factory()->create(['role' => 'pptk']);
        // Seed a year session
        session(['tahun_anggaran' => 2026]);
    }

    // ─── INDEX ────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_view_anggaran_index(): void
    {
        $this->actingAs($this->admin)->get('/anggaran')->assertOk();
    }

    /** @test */
    public function guest_cannot_view_anggaran_index(): void
    {
        $this->get('/anggaran')->assertRedirect('/login');
    }

    // ─── STORE ────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_create_anggaran(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['tahun_anggaran' => 2026])
            ->post('/anggaran', [
                'kode'   => '1.01',
                'uraian' => 'Program Pelayanan Administrasi',
                'tahun'  => 2026,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('anggarans', [
            'kode'  => '1.01',
            'tahun' => 2026,
        ]);
    }

    /** @test */
    public function store_anggaran_automatically_finds_parent(): void
    {
        // Create parent first
        $parent = Anggaran::factory()->create(['kode' => '1.01', 'tahun' => 2026, 'parent_id' => null]);

        $response = $this->actingAs($this->admin)
            ->withSession(['tahun_anggaran' => 2026])
            ->post('/anggaran', [
                'kode'   => '1.01.01',
                'uraian' => 'Kegiatan Penyediaan Jasa',
                'tahun'  => 2026,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('anggarans', [
            'kode'      => '1.01.01',
            'parent_id' => $parent->id,
        ]);
    }

    /** @test */
    public function store_anggaran_requires_kode_and_uraian(): void
    {
        $response = $this->actingAs($this->admin)->post('/anggaran', [
            'tahun' => 2026,
        ]);

        $response->assertSessionHasErrors(['kode', 'uraian']);
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_update_anggaran(): void
    {
        $anggaran = Anggaran::factory()->create(['kode' => '1.01', 'tahun' => 2026]);

        $response = $this->actingAs($this->admin)->put("/anggaran/{$anggaran->id}", [
            'kode'  => '1.01',
            'uraian'=> 'Updated Uraian',
            'pagu'  => '5.000.000',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('anggarans', [
            'id'    => $anggaran->id,
            'uraian'=> 'Updated Uraian',
            'pagu'  => 5000000,
        ]);
    }

    /** @test */
    public function update_pagu_strips_thousand_separators(): void
    {
        $anggaran = Anggaran::factory()->create(['kode' => '1.01', 'tahun' => 2026, 'pagu' => 1000000]);

        $this->actingAs($this->admin)->put("/anggaran/{$anggaran->id}", [
            'kode'  => '1.01',
            'uraian'=> $anggaran->uraian,
            'pagu'  => '10.000.000',
        ]);

        $this->assertDatabaseHas('anggarans', ['id' => $anggaran->id, 'pagu' => 10000000]);
    }

    // ─── DELETE ───────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_delete_anggaran(): void
    {
        $anggaran = Anggaran::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/anggaran/{$anggaran->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('anggarans', ['id' => $anggaran->id]);
    }

    // ─── UPDATE PAGU INLINE (API) ─────────────────────────────────────────────

    /** @test */
    public function update_pagu_inline_returns_json_success(): void
    {
        $anggaran = Anggaran::factory()->create(['pagu' => 1000000]);

        $response = $this->actingAs($this->admin)
            ->patch("/anggaran/{$anggaran->id}/update-pagu", ['pagu' => '3.000.000']);

        $response->assertOk();
        $response->assertJsonStructure(['success', 'new_total']);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('anggarans', ['id' => $anggaran->id, 'pagu' => 3000000]);
    }

    /** @test */
    public function update_pagu_inline_returns_400_without_pagu(): void
    {
        $anggaran = Anggaran::factory()->create();

        $response = $this->actingAs($this->admin)
            ->patch("/anggaran/{$anggaran->id}/update-pagu", []);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    // ─── DELETE ALL ───────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_delete_all_anggaran_for_current_year(): void
    {
        Anggaran::factory()->count(3)->create(['tahun' => 2026]);
        Anggaran::factory()->count(2)->create(['tahun' => 2025]);

        $this->actingAs($this->admin)
            ->withSession(['tahun_anggaran' => 2026])
            ->delete('/anggaran/delete-all');

        $this->assertEquals(0, Anggaran::where('tahun', 2026)->count());
        $this->assertEquals(2, Anggaran::where('tahun', 2025)->count());
    }
}
