<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pejabat;
use App\Models\BkuTransaksi;
use App\Models\Anggaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class BkuTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pptkUser;
    private Pejabat $pptkPejabat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pptkPejabat = Pejabat::factory()->pptk()->create();

        $this->admin = User::factory()->create([
            'role'       => 'admin',
            'pejabat_id' => null,
        ]);

        $this->pptkUser = User::factory()->create([
            'role'       => 'pptk',
            'pejabat_id' => $this->pptkPejabat->id,
        ]);
    }

    // ─── INDEX ────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_view_bku_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/bku');
        $response->assertOk();
    }

    /** @test */
    public function pptk_can_view_bku_index(): void
    {
        $response = $this->actingAs($this->pptkUser)->get('/bku');
        $response->assertOk();
    }

    /** @test */
    public function guest_cannot_access_bku_index(): void
    {
        $this->get('/bku')->assertRedirect('/login');
    }

    /** @test */
    public function bku_index_can_filter_by_jenis_pencairan(): void
    {
        BkuTransaksi::factory()->create([
            'jenis_pencairan' => 'UP',
            'tanggal'         => '2026-01-01',
            'pptk_id'         => $this->pptkPejabat->id,
        ]);
        BkuTransaksi::factory()->create([
            'jenis_pencairan' => 'GU 1',
            'tanggal'         => '2026-01-01',
            'pptk_id'         => $this->pptkPejabat->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/bku?jenis_pencairan=UP&year=2026');

        $response->assertOk();
    }

    // ─── STORE ────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_create_bku_transaction(): void
    {
        $response = $this->actingAs($this->admin)->post('/bku', [
            'tanggal'          => '2026-03-15',
            'no_bukti'         => 'KW/001/Kec Psjb/UP/2026',
            'kode_rekening'    => '5.1.02.01.01.0001',
            'kode_sub_kegiatan'=> '1.01.01.2.01.01',
            'nama_sub_kegiatan'=> 'Penyediaan ATK',
            'uraian'           => 'Pembelian Alat Tulis Kantor',
            'penerima'         => 'Toko Buku Maju',
            'nominal'          => '1500000',
            'jenis_pencairan'  => 'UP',
            'pptk_id'          => $this->pptkPejabat->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bku_transaksis', [
            'no_bukti' => 'KW/001/Kec Psjb/UP/2026',
            'nominal'  => 1500000,
        ]);
    }

    /** @test */
    public function store_fails_without_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post('/bku', [
            'tanggal' => '2026-03-15',
            // Missing no_bukti, kode_rekening, uraian, penerima, nominal, jenis_pencairan, pptk_id
        ]);

        $response->assertSessionHasErrors(['no_bukti', 'kode_rekening', 'uraian', 'penerima', 'nominal', 'jenis_pencairan', 'pptk_id']);
    }

    /** @test */
    public function store_rejects_invalid_jenis_pencairan(): void
    {
        $response = $this->actingAs($this->admin)->post('/bku', [
            'tanggal'         => '2026-03-15',
            'no_bukti'        => 'KW/001/Kec Psjb/INVALID/2026',
            'kode_rekening'   => '5.1.02.01.01.0001',
            'uraian'          => 'Test',
            'penerima'        => 'Test Penerima',
            'nominal'         => 1000000,
            'jenis_pencairan' => 'INVALID', // not in allowed list
            'pptk_id'         => $this->pptkPejabat->id,
        ]);

        $response->assertSessionHasErrors('jenis_pencairan');
    }

    /** @test */
    public function store_accepts_localized_nominal_with_dots(): void
    {
        $response = $this->actingAs($this->admin)->post('/bku', [
            'tanggal'         => '2026-03-15',
            'no_bukti'        => 'KW/002/Kec Psjb/UP/2026',
            'kode_rekening'   => '5.1.02.01.01.0001',
            'uraian'          => 'Test dengan nominal format lokal',
            'penerima'        => 'Toko Test',
            'nominal'         => '1.500.000', // Indonesian formatting
            'jenis_pencairan' => 'UP',
            'pptk_id'         => $this->pptkPejabat->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bku_transaksis', ['nominal' => 1500000]);
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_update_bku_transaction(): void
    {
        $bku = BkuTransaksi::factory()->create([
            'pptk_id' => $this->pptkPejabat->id,
            'nominal' => 1000000,
        ]);

        $response = $this->actingAs($this->admin)->put("/bku/{$bku->id}", [
            'tanggal'         => '2026-04-01',
            'no_bukti'        => $bku->no_bukti,
            'kode_rekening'   => $bku->kode_rekening,
            'uraian'          => 'Uraian Updated',
            'penerima'        => $bku->penerima,
            'nominal'         => '2000000',
            'jenis_pencairan' => 'GU 1',
            'pptk_id'         => $this->pptkPejabat->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bku_transaksis', ['id' => $bku->id, 'nominal' => 2000000]);
    }

    // ─── DELETE ───────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_delete_bku_transaction(): void
    {
        $bku = BkuTransaksi::factory()->create(['pptk_id' => $this->pptkPejabat->id]);

        $response = $this->actingAs($this->admin)->delete("/bku/{$bku->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('bku_transaksis', ['id' => $bku->id]);
    }

    // ─── VALIDASI ─────────────────────────────────────────────────────────────

    /** @test */
    public function pptk_can_validate_own_transaction(): void
    {
        $bku = BkuTransaksi::factory()->create([
            'pptk_id'         => $this->pptkPejabat->id,
            'status_validasi' => false,
        ]);

        $response = $this->actingAs($this->pptkUser)->post("/bku/{$bku->id}/validasi");

        $response->assertRedirect();
        $this->assertDatabaseHas('bku_transaksis', ['id' => $bku->id, 'status_validasi' => 1]);
    }

    /** @test */
    public function admin_cannot_validate_transaction(): void
    {
        $bku = BkuTransaksi::factory()->create(['pptk_id' => $this->pptkPejabat->id, 'status_validasi' => false]);

        $response = $this->actingAs($this->admin)->post("/bku/{$bku->id}/validasi");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('bku_transaksis', ['id' => $bku->id, 'status_validasi' => 0]);
    }

    /** @test */
    public function pptk_cannot_validate_another_pptk_transaction(): void
    {
        $otherPptk = Pejabat::factory()->pptk()->create();
        $bku = BkuTransaksi::factory()->create([
            'pptk_id'         => $otherPptk->id,
            'status_validasi' => false,
        ]);

        $response = $this->actingAs($this->pptkUser)->post("/bku/{$bku->id}/validasi");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function pptk_can_cancel_validation(): void
    {
        $bku = BkuTransaksi::factory()->create([
            'pptk_id'         => $this->pptkPejabat->id,
            'status_validasi' => true,
        ]);

        $this->actingAs($this->pptkUser)->post("/bku/{$bku->id}/batal-validasi");

        $this->assertDatabaseHas('bku_transaksis', ['id' => $bku->id, 'status_validasi' => 0]);
    }

    // ─── GENERATE NO BUKTI ────────────────────────────────────────────────────

    /** @test */
    public function generate_no_bukti_returns_correct_format(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/bku/generate-no-bukti?year=2026&jenis_pencairan=UP');

        $response->assertOk();
        $response->assertJsonStructure(['no_bukti']);

        $noBukti = $response->json('no_bukti');
        $this->assertMatchesRegularExpression('/^KW\/\d{3}\/Kec Psjb\/UP\/2026$/', $noBukti);
    }

    /** @test */
    public function generate_no_bukti_increments_sequence(): void
    {
        // Create existing transactions
        BkuTransaksi::factory()->create([
            'no_bukti'        => 'KW/001/Kec Psjb/UP/2026',
            'jenis_pencairan' => 'UP',
            'tanggal'         => '2026-01-01',
            'pptk_id'         => $this->pptkPejabat->id,
        ]);
        BkuTransaksi::factory()->create([
            'no_bukti'        => 'KW/002/Kec Psjb/UP/2026',
            'jenis_pencairan' => 'UP',
            'tanggal'         => '2026-01-02',
            'pptk_id'         => $this->pptkPejabat->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/bku/generate-no-bukti?year=2026&jenis_pencairan=UP');

        $this->assertEquals('KW/003/Kec Psjb/UP/2026', $response->json('no_bukti'));
    }
}
