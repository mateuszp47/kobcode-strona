<?php
namespace Tests\Feature;
use Tests\TestCase;
class ExampleTest extends TestCase
{
    public function test_public_pages_render_without_unverified_contact_details(): void
    {
        foreach (['/', '/uslugi/strony-internetowe', '/uslugi/aplikacje-internetowe', '/realizacje', '/o-firmie', '/kontakt'] as $url) {
            $this->get($url)->assertOk()->assertSee('noindex,nofollow', false)->assertDontSee('m.kobialka@kobcode.com');
        }
    }
}
