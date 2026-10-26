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
        foreach (['walbud-home','olaya-home','olaya-catalog','olaya-projects'] as $image) {
            $response->assertSee($image.'-800.webp')->assertSee($image.'-1600.webp');
            $this->assertFileExists(public_path('images/portfolio/'.$image.'-800.webp'));
            $this->assertFileExists(public_path('images/portfolio/'.$image.'-1600.webp'));
        }
        $this->get('/realizacje/olaya-strona')->assertNotFound();
        $response->assertDontSee('Materiały projektowe pojawią się');
    }
}
