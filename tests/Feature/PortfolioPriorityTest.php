<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class PortfolioPriorityTest extends TestCase
{
    use RefreshDatabase;
    public function test_olaya_site_and_application_precede_walbud(): void
    {
        $this->withoutVite();
        foreach (['/', '/realizacje'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertLessThan(strpos($html, 'Walbud / Walbud24'), strpos($html, 'Olaya — aplikacja'));
            $this->assertLessThan(strpos($html, 'Olaya — aplikacja'), strpos($html, 'olaya-home-800.webp'));
            $this->assertStringContainsString('olaya-app-sales-800.webp', $html);
            $this->assertStringNotContainsString('image(6)', $html);
        }
    }
}
