<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\BkuController;
use ReflectionClass;

class TerbilangTest extends TestCase
{
    private BkuController $controller;
    private \ReflectionMethod $terbilang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new BkuController();
        $ref = new ReflectionClass(BkuController::class);
        $this->terbilang = $ref->getMethod('terbilang');
        $this->terbilang->setAccessible(true);
    }

    private function terbilang(int|float $nilai): string
    {
        return $this->terbilang->invoke($this->controller, $nilai);
    }

    /** @test */
    public function it_converts_zero(): void
    {
        $this->assertSame('', trim($this->terbilang(0)));
    }

    /** @test */
    public function it_converts_single_digits(): void
    {
        $this->assertStringContainsString('Satu', $this->terbilang(1));
        $this->assertStringContainsString('Lima', $this->terbilang(5));
        $this->assertStringContainsString('Sembilan', $this->terbilang(9));
    }

    /** @test */
    public function it_converts_eleven(): void
    {
        $this->assertStringContainsString('Sebelas', $this->terbilang(11));
    }

    /** @test */
    public function it_converts_teens(): void
    {
        $result = $this->terbilang(15);
        $this->assertStringContainsString('Belas', $result);
    }

    /** @test */
    public function it_converts_tens(): void
    {
        $result = $this->terbilang(20);
        $this->assertStringContainsString('Dua', $result);
        $this->assertStringContainsString('Puluh', $result);
    }

    /** @test */
    public function it_converts_one_hundred(): void
    {
        $this->assertStringContainsString('Seratus', $this->terbilang(100));
    }

    /** @test */
    public function it_converts_hundreds(): void
    {
        $result = $this->terbilang(350);
        $this->assertStringContainsString('Tiga', $result);
        $this->assertStringContainsString('Ratus', $result);
        $this->assertStringContainsString('Lima', $result);
        $this->assertStringContainsString('Puluh', $result);
    }

    /** @test */
    public function it_converts_one_thousand(): void
    {
        $this->assertStringContainsString('Seribu', $this->terbilang(1000));
    }

    /** @test */
    public function it_converts_thousands(): void
    {
        $result = $this->terbilang(5000);
        $this->assertStringContainsString('Lima', $result);
        $this->assertStringContainsString('Ribu', $result);
    }

    /** @test */
    public function it_converts_millions(): void
    {
        $result = $this->terbilang(1500000);
        $this->assertStringContainsString('Juta', $result);
        $this->assertStringContainsString('Lima', $result);
        $this->assertStringContainsString('Ratus', $result);
    }

    /** @test */
    public function it_converts_typical_budget_amount(): void
    {
        // 4,931,000 => Empat Juta Sembilan Ratus Tiga Puluh Satu Ribu
        $result = $this->terbilang(4931000);
        $this->assertStringContainsString('Empat', $result);
        $this->assertStringContainsString('Juta', $result);
        $this->assertStringContainsString('Ribu', $result);
    }

    /** @test */
    public function it_handles_negative_values_as_absolute(): void
    {
        $positive = $this->terbilang(100);
        $negative = $this->terbilang(-100);
        $this->assertSame($positive, $negative);
    }
}
