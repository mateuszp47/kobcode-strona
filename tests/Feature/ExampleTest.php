<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class ExampleTest extends TestCase
{
    use RefreshDatabase;
    public function test_public_pages_render_without_unverified_contact_details(): void
    {
        $this->withoutVite();
        foreach (['/', '/uslugi/strony-internetowe', '/uslugi/aplikacje-internetowe', '/realizacje', '/o-firmie', '/kontakt'] as $url) {
            $this->get($url)->assertOk()->assertSee('noindex,nofollow', false)->assertDontSee('m.kobialka@kobcode.com');
        }
    }
}
