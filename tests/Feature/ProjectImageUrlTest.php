<?php

namespace Tests\Feature;

use App\Support\ProjectImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectImageUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_walbud_cover_uses_the_open_page_origin_despite_an_old_app_url(): void
    {
        $this->withoutVite();
        Storage::fake('public');
        config(['app.url' => 'http://localhost:8000', 'filesystems.disks.public.url' => 'http://localhost:8000/storage']);
        $this->artisan('kobcode:case-studies', ['--publish' => true])->assertSuccessful();
        Storage::disk('public')->assertExists('portfolio/case-studies/walbud24/walbud-home.webp');
        $this->get('http://127.0.0.1:8001/realizacje/walbud24')->assertOk()
            ->assertSee('src="/storage/portfolio/case-studies/walbud24/walbud-home.webp"', false)
            ->assertDontSee('src="http://localhost:8000/storage/', false);
    }

    public function test_gallery_and_responsive_sources_also_use_the_current_origin(): void
    {
        $this->withoutVite();
        Storage::fake('public');
        config(['app.url' => 'http://localhost:8000', 'filesystems.disks.public.url' => 'http://localhost:8000/storage']);
        $this->artisan('kobcode:case-studies', ['--publish' => true])->assertSuccessful();
        $html = $this->get('http://127.0.0.1:8001/realizacje/cermax')->assertOk()->getContent();
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $images = $xpath->query('//article//img');
        $this->assertCount(5, $images);
        foreach ($images as $image) {
            $this->assertStringStartsWith('/storage/', $image->getAttribute('src'));
            $this->assertStringContainsString('/storage/', $image->getAttribute('srcset'));
            $this->assertStringNotContainsString('localhost:8000', $image->getAttribute('srcset'));
        }
        $this->assertSame('/storage/portfolio/Widok%20panelu.webp', ProjectImage::url('portfolio/Widok panelu.webp'));
    }
}
