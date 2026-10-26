<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class PortfolioPriorityTest extends TestCase
{
    use RefreshDatabase;
    public function test_cermax_leads_then_olaya_parkstop_and_walbud(): void
    {
        $this->withoutVite();
        foreach (['/' => ['id="work-cermax"', 'id="work-site"', 'id="work-app"', '#parkstop', '#walbud'], '/realizacje' => ['id="cermax"', 'id="olaya"', 'id="olaya-aplikacja"', 'id="parkstop"', 'id="walbud"']] as $url => $markers) {
            $html = $this->get($url)->assertOk()->getContent();
            $previous = -1;
            foreach ($markers as $marker) {
                $position = strpos($html, $marker);
                $this->assertNotFalse($position, $marker);
                $this->assertGreaterThan($previous, $position, $marker);
                $previous = $position;
            }
            $this->assertStringContainsString('olaya-app-sales-800.webp', $html);
            $this->assertStringContainsString('SKU', $html);
            $this->assertStringContainsString('EAN', $html);
            $this->assertStringNotContainsString('image(6)', $html);
        }
    }
}
