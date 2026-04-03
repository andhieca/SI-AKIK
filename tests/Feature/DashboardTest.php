<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pejabat;
use App\Models\BkuTransaksi;
use App\Models\Anggaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    /** @test */
    public function admin_can_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
    }

    /** @test */
    public function pptk_can_access_dashboard(): void
    {
        $pejabat = Pejabat::factory()->pptk()->create();
        $user = User::factory()->create(['role' => 'pptk', 'pejabat_id' => $pejabat->id]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
    }

    /** @test */
    public function dashboard_passes_total_anggaran_to_view(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        Anggaran::factory()->create(['tahun' => date('Y'), 'pagu' => 10000000, 'parent_id' => null]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertViewHas('totalAnggaran');
    }

    /** @test */
    public function dashboard_passes_total_realisasi_to_view(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertViewHas('totalRealisasi');
    }

    /** @test */
    public function dashboard_passes_sisa_kas_to_view(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertViewHas('sisaKas');
    }

    /** @test */
    public function dashboard_passes_grafik_kuitansi_array_to_view(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertViewHas('grafikKuitansi');

        // Should be 12 months
        $grafik = $response->viewData('grafikKuitansi');
        $this->assertIsArray($grafik);
        $this->assertCount(12, $grafik);
    }

    /** @test */
    public function pptk_sees_own_transaction_total_as_total_anggaran(): void
    {
        $pejabat = Pejabat::factory()->pptk()->create();
        $user = User::factory()->create(['role' => 'pptk', 'pejabat_id' => $pejabat->id]);

        $year = date('Y');
        BkuTransaksi::factory()->create([
            'pptk_id' => $pejabat->id,
            'nominal' => 500000,
            'tanggal' => $year . '-03-01',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        // For PPTK, totalAnggaran is sum of their transactions
        $this->assertEquals(500000, $response->viewData('totalAnggaran'));
    }

    /** @test */
    public function dashboard_includes_validation_summary_for_pptk(): void
    {
        $pejabat = Pejabat::factory()->pptk()->create();
        $user = User::factory()->create(['role' => 'pptk', 'pejabat_id' => $pejabat->id]);

        $year = date('Y');
        BkuTransaksi::factory()->create(['pptk_id' => $pejabat->id, 'status_validasi' => true, 'tanggal' => $year . '-01-01']);
        BkuTransaksi::factory()->create(['pptk_id' => $pejabat->id, 'status_validasi' => false, 'tanggal' => $year . '-01-02']);
        BkuTransaksi::factory()->create(['pptk_id' => $pejabat->id, 'status_validasi' => false, 'tanggal' => $year . '-01-03']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $this->assertEquals(1, $response->viewData('validatedCount'));
        $this->assertEquals(2, $response->viewData('unvalidatedCount'));
    }

    /** @test */
    public function dashboard_verify_query_returns_correct_transaction(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $hash = 'testqrhash1234567890123456789012';
        $bku = BkuTransaksi::factory()->create([
            'qr_code_hash' => $hash,
            'pptk_id'      => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard?verify=' . $hash);
        $response->assertOk();
        $result = $response->viewData('verificationResult');
        $this->assertEquals($bku->id, $result->id);
    }
}
