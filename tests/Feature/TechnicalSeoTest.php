<?php

namespace Tests\Feature;

use App\Http\Middleware\PublicSeo;
use App\Models\Project;
use App\Support\ProjectImage;
use App\Support\Seo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TechnicalSeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        config(['app.url' => 'https://kobcode.test', 'site.indexable' => true]);
    }

    public function test_all_public_pages_have_valid_unique_metadata_and_one_h1(): void
    {
        $titles = $descriptions = [];
        foreach (['/', '/kontakt', '/o-firmie', '/realizacje', '/uslugi/strony-internetowe', '/uslugi/aplikacje-internetowe', '/polityka-prywatnosci'] as $path) {
            $html = $this->get($path.'?utm_source=audit')->assertOk()->getContent();
            $dom = new \DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
            $xpath = new \DOMXPath($dom);
            $this->assertSame(1, $xpath->query('//h1')->length);
            $this->assertSame(1, $xpath->query('//title')->length);
            $this->assertSame(1, $xpath->query('//meta[@name="description"]')->length);
            $this->assertSame(1, $xpath->query('//link[@rel="canonical"]')->length);
            $this->assertSame('https://kobcode.test'.$path, $xpath->evaluate('string(//link[@rel="canonical"]/@href)'));
            $titles[] = $xpath->evaluate('string(//title)');
            $descriptions[] = $xpath->evaluate('string(//meta[@name="description"]/@content)');
            $this->assertStringContainsString('KOBCODE', end($titles));
            $this->assertNotEmpty(end($descriptions));
            $this->assertSame(0, $xpath->query('//meta[@name="keywords"]')->length);
            $this->assertSame(end($titles), $xpath->evaluate('string(//meta[@property="og:title"]/@content)'));
            $this->assertSame(end($descriptions), $xpath->evaluate('string(//meta[@name="twitter:description"]/@content)'));
            $this->assertSame(1, $xpath->query('//script[@type="application/ld+json"]')->length);
            $schema = json_decode($xpath->evaluate('string(//script[@type="application/ld+json"])'), true, 512, JSON_THROW_ON_ERROR);
            $this->assertSame('https://schema.org', $schema['@context']);
            $ids = array_column($schema['@graph'], '@id');
            $this->assertCount(count($ids), array_unique($ids));
            $this->assertSame(str_starts_with($path, '/uslugi/') ? 1 : 0, count(array_filter($schema['@graph'], fn ($node) => $node['@type'] === 'Service')));
            foreach ($xpath->query('//img') as $image) {
                $this->assertTrue($image->hasAttribute('alt'));
            }
        }
        $this->assertCount(7, array_unique($titles));
        $this->assertCount(7, array_unique($descriptions));
        $this->assertSame('https://kobcode.test/kontakt', Seo::url('/kontakt/'));
    }

    public function test_sitemap_lists_all_seven_pages_and_only_approved_projects(): void
    {
        $project = Project::create(['title' => 'Projekt & firma', 'slug' => 'projekt', 'category' => 'Strona', 'summary' => 'Opis', 'relationship' => 'client', 'approved_for_publication' => true, 'is_published' => true]);
        $project->update(['updated_at' => '2026-11-01 12:00:00']);
        $xml = simplexml_load_string($this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->getContent());
        $urls = array_map('strval', $xml->xpath('//*[local-name()="loc"]'));
        $this->assertCount(8, $urls);
        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
        $modified = $xml->xpath('//*[local-name()="lastmod"]');
        $this->assertCount(1, $modified);
        $this->assertSame($project->fresh()->updated_at->toAtomString(), (string) $modified[0]);
        $project->update(['is_published' => false]);
        $this->get('/sitemap.xml')->assertDontSee('/realizacje/projekt');
        $this->get('/realizacje/projekt')->assertNotFound();
        $robots = $this->get('/robots.txt')->assertOk()->getContent();
        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringNotContainsString('Disallow: /images', $robots);
    }

    public function test_cover_dimensions_match_the_uploaded_image_on_both_views(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('portfolio/test.webp', file_get_contents(public_path('images/portfolio/olaya-home-1600.webp')));
        Project::create(['title' => 'Projekt testowy', 'slug' => 'testowy', 'category' => 'Strona', 'summary' => 'Opis', 'relationship' => 'client', 'approved_for_publication' => true, 'is_published' => true, 'cover' => 'portfolio/test.webp']);
        foreach (['/realizacje', '/realizacje/testowy'] as $path) {
            $this->get($path)->assertOk()->assertSee('alt="Projekt testowy"', false)->assertSee('width="1600" height="870"', false);
        }
        $this->assertNull(ProjectImage::dimensions('portfolio/missing.webp'));
        $this->assertNull(ProjectImage::dimensions('../private.png'));
        $this->get('/realizacje/testowy')->assertSee('fetchpriority="high"', false)->assertDontSee('loading="lazy"', false);
        $this->get('/')->assertSee('fetchpriority="high"', false);
    }

    public function test_public_redirects_are_permanent_preserve_query_and_do_not_loop(): void
    {
        $this->app['env'] = 'production';
        $this->get('https://www.kobcode.test/kontakt?utm_source=test')->assertRedirect('https://kobcode.test/kontakt?utm_source=test')->assertStatus(301);
        $this->get('https://kobcode.test/kontakt/')->assertRedirect('https://kobcode.test/kontakt')->assertStatus(301);
        $this->get('https://kobcode.test/kontakt')->assertOk();
        $this->head('https://www.kobcode.test/o-firmie')->assertStatus(301);
        // Simulate LH's internal /public rewrite while Laravel resolves the actual public route.
        $request = Request::create('https://kobcode.test/public/kontakt?source=test');
        $request->setRouteResolver(fn () => (new \Illuminate\Routing\Route('GET', 'kontakt', fn () => 'OK'))->name('contact'));
        $request->server->set('SCRIPT_NAME', '/public/index.php');
        $response = (new PublicSeo)->handle($request, fn () => response('OK'));
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('https://kobcode.test/kontakt?source=test', $response->headers->get('Location'));
    }

    public function test_preview_post_admin_and_errors_are_not_canonicalized(): void
    {
        $this->get('https://www.kobcode.test/kontakt')->assertOk();
        $this->app['env'] = 'production';
        config(['site.indexable' => false]);
        $this->get('https://www.kobcode.test/kontakt')->assertOk()->assertSee('noindex,nofollow');
        config(['site.indexable' => true]);
        $this->get('/does-not-exist')->assertNotFound()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/up')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/admin/login')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->post('https://www.kobcode.test/kontakt', [])->assertStatus(503);
    }
}
