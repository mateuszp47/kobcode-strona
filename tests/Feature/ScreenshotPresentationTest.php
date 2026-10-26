<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class ScreenshotPresentationTest extends TestCase
{
    use RefreshDatabase;
    public function test_gallery_is_visible_without_publishing_draft_records(): void
    {
        $this->withoutVite();
        $response = $this->get('/realizacje')->assertOk();
        foreach (['cermax-home','cermax-catalog','cermax-b2b','olaya-home','olaya-catalog','olaya-projects','olaya-app-sales'] as $image) {
            $response->assertSee($image.'-800.webp')->assertSee($image.'-1600.webp');
            $this->assertFileExists(public_path('images/portfolio/'.$image.'-800.webp'));
            $this->assertFileExists(public_path('images/portfolio/'.$image.'-1600.webp'));
        }
        $this->get('/realizacje/olaya-strona')->assertNotFound();
        $response->assertDontSee('Materiały projektowe pojawią się')->assertDontSee('walbud-home-800.webp');
        $html = $response->getContent();
        foreach (['parkstop', 'walbud'] as $id) {
            $section = explode('</section>', explode('id="'.$id.'"', $html)[1])[0];
            $this->assertStringNotContainsString('<img', $section);
        }
    }
    public function test_walbud_alone_does_not_leave_an_empty_details_section(): void
    {
        $this->withoutVite();
        \App\Models\Project::create(['title'=>'Walbud / Walbud24','slug'=>'walbud24','category'=>'Strona','summary'=>'Opis','relationship'=>'client','approved_for_publication'=>true,'is_published'=>true]);
        $this->get('/realizacje')->assertOk()->assertDontSee('Opisy projektów')->assertSee('Walbud / Walbud24');
    }
}
