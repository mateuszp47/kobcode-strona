<?php
namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        config(['app.url' => 'https://example.test', 'site.indexable' => true]);
    }

    public function test_pages_have_distinct_descriptions_and_consistent_canonical_metadata(): void
    {
        $descriptions = [];
        foreach (['/', '/realizacje', '/o-firmie', '/kontakt', '/uslugi/strony-internetowe', '/uslugi/aplikacje-internetowe'] as $path) {
            $html = $this->get($path.'?utm_source=test')->assertOk()->getContent();
            preg_match('/<meta name="description" content="([^"]+)"/', $html, $match);
            $descriptions[] = $match[1];
            $this->assertStringContainsString('<link rel="canonical" href="https://example.test'.$path.'">', $html);
            $this->assertStringContainsString('property="og:url" content="https://example.test'.$path.'"', $html);
            preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $json);
            $schema = json_decode($json[1], true, 512, JSON_THROW_ON_ERROR);
            $this->assertSame('Organization', $schema['@graph'][0]['@type']);
            $this->assertSame(1, substr_count($html, '<h1>'));
        }
        $this->assertCount(count($descriptions), array_unique($descriptions));
    }

    public function test_sitemap_includes_only_public_projects_and_has_no_fabricated_dates(): void
    {
        Project::create(['title'=>'Publiczny projekt', 'slug'=>'publiczny', 'category'=>'Aplikacja', 'summary'=>'Opis', 'relationship'=>'client', 'approved_for_publication'=>true, 'is_published'=>true]);
        Project::create(['title'=>'Szkic', 'slug'=>'szkic', 'category'=>'Aplikacja', 'summary'=>'Opis', 'relationship'=>'client', 'approved_for_publication'=>false, 'is_published'=>false]);
        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $response->assertSee('https://example.test/realizacje/publiczny', false)->assertDontSee('/realizacje/szkic')->assertDontSee('/admin')->assertDontSee('<priority>')->assertDontSee('/kontakt');
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: https://example.test/sitemap.xml', false);
    }

    public function test_preview_remains_unindexable_without_advertising_a_sitemap(): void
    {
        config(['site.indexable'=>false]);
        $this->get('/')->assertOk()->assertSee('noindex,nofollow');
        $this->get('/robots.txt')->assertSee('Disallow: /', false)->assertDontSee('Sitemap:');
        $this->get('/sitemap.xml')->assertNotFound();
    }

    public function test_project_metadata_cannot_inject_markup_or_break_json_ld(): void
    {
        Project::create(['title'=>'Projekt', 'slug'=>'test', 'category'=>'Aplikacja', 'summary'=>'Opis', 'seo_title'=>'Test </script><script>alert(1)</script>', 'relationship'=>'client', 'approved_for_publication'=>true, 'is_published'=>true]);
        $html = $this->get('/realizacje/test')->assertOk()->getContent();
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $json);
        $this->assertNotNull(json_decode($json[1], true, 512, JSON_THROW_ON_ERROR));
    }
}
