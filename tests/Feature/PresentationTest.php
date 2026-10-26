<?php
namespace Tests\Feature;
use Tests\TestCase;
class PresentationTest extends TestCase
{
    public function test_nationwide_presentation_keeps_city_only_in_footer(): void
    {
        $this->withoutVite();
        foreach (['/', '/o-firmie', '/kontakt', '/polityka-prywatnosci', '/uslugi/strony-internetowe', '/uslugi/aplikacje-internetowe'] as $url) {
            $response = $this->get($url)->assertOk();
            $beforeFooter = explode('<footer', $response->getContent())[0];
            $this->assertStringNotContainsString('Radom', $beforeFooter);
            $response->assertSee('26-600 Radom');
        }
        $this->get('/')->assertSee('Walbud / Walbud24')->assertSee('Olaya')->assertSee('SCHEMAT PREZENTACJI')->assertSee('CAŁA POLSKA');
    }
}
