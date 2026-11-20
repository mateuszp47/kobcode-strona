<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class PresentationTest extends TestCase
{
    use RefreshDatabase;
    public function test_nationwide_presentation_and_local_service_context(): void
    {
        $this->withoutVite();
        foreach (['/', '/o-firmie', '/kontakt', '/polityka-prywatnosci', '/uslugi/strony-internetowe', '/uslugi/aplikacje-internetowe'] as $url) {
            $response = $this->get($url)->assertOk();
            $beforeFooter = explode('<footer', $response->getContent())[0];
            if (in_array($url, ['/o-firmie', '/uslugi/strony-internetowe'])) {
                $this->assertStringContainsString('w Radomiu', $beforeFooter);
                $this->assertStringContainsString('całej Polski', $beforeFooter);
            } else {
                $this->assertStringNotContainsString('Radom', $beforeFooter);
            }
            $response->assertSee('26-600 Radom');
        }
        $this->get('/')->assertDontSee('POZOSTAŁE REALIZACJE')->assertSee('Wszystkie realizacje')->assertSee('Olaya')->assertSee('olaya-home-800.webp')->assertSee('CAŁEJ POLSKI');
    }
}
