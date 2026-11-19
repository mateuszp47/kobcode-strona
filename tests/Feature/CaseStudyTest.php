<?php

namespace Tests\Feature;

use App\Filament\Resources\ProjectResource\Pages\{CreateProject, EditProject};
use App\Models\{Project, User};
use App\Support\ProjectContent;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CaseStudyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('public');
        config(['app.url' => 'https://kobcode.test', 'site.indexable' => true]);
    }

    public function test_import_creates_private_drafts_and_preserves_existing_data_on_repeat(): void
    {
        $this->artisan('kobcode:case-studies')->assertSuccessful();
        $this->assertDatabaseCount('projects', 5);
        $project = Project::where('slug', 'olaya-strona')->firstOrFail();
        $this->assertFalse($project->is_published);
        $this->assertFalse($project->approved_for_publication);
        $this->get('https://kobcode.test/realizacje/olaya-strona')->assertNotFound();
        $this->get('https://kobcode.test/realizacje')->assertDontSee('href="https://kobcode.test/realizacje/olaya-strona"', false);
        $this->get('https://kobcode.test/sitemap.xml')->assertDontSee('/realizacje/olaya-strona');
        $project->update(['summary' => 'Własny opis', 'goal' => 'Cel po korekcie', 'technologies' => ['Własna technologia'], 'service_links' => ['apps'], 'cover' => 'custom.webp', 'cover_alt' => 'Własny obraz', 'gallery' => [['path' => 'custom.webp', 'alt' => 'Własny obraz']], 'sort_order' => 77]);
        $before = $project->fresh()->getAttributes();
        $this->artisan('kobcode:case-studies')->assertSuccessful();
        $this->assertSame($before, $project->fresh()->getAttributes());
        $this->assertDatabaseCount('projects', 5);
    }

    public function test_published_cases_have_unique_metadata_canonical_breadcrumbs_and_working_links(): void
    {
        $this->artisan('kobcode:case-studies', ['--publish' => true])->assertSuccessful();
        $this->assertSame(5, Project::published()->count());
        $titles = $descriptions = [];
        $portfolio = $this->get('https://kobcode.test/realizacje')->assertOk();
        foreach (Project::published()->get() as $project) {
            $path = '/realizacje/'.$project->slug;
            $html = $this->get('https://kobcode.test'.$path)->assertOk()->getContent();
            $dom = new \DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
            $xpath = new \DOMXPath($dom);
            $this->assertSame(1, $xpath->query('//h1')->length);
            $this->assertSame($project->title, $xpath->query('//h1')->item(0)->textContent);
            $titles[] = $xpath->query('//title')->item(0)->textContent;
            $descriptions[] = $xpath->query('//meta[@name="description"]')->item(0)->getAttribute('content');
            $this->assertSame('https://kobcode.test'.$path, $xpath->query('//link[@rel="canonical"]')->item(0)->getAttribute('href'));
            $schema = json_decode($xpath->query('//script[@type="application/ld+json"]')->item(0)->textContent, true, 512, JSON_THROW_ON_ERROR);
            $breadcrumbs = collect($schema['@graph'])->firstWhere('@type', 'BreadcrumbList');
            $this->assertCount(3, $breadcrumbs['itemListElement']);
            $this->assertSame('https://kobcode.test'.$path, $breadcrumbs['itemListElement'][2]['item']);
            $portfolio->assertSee('href="https://kobcode.test'.$path.'"', false);
            foreach ($project->service_links as $name) {
                $this->assertStringContainsString('href="'.route($name).'"', $html);
                $this->get(route($name))->assertOk();
            }
            foreach ($xpath->query('//article//img') as $image) {
                $this->assertNotEmpty($image->getAttribute('alt'));
                $this->assertGreaterThan(0, (int) $image->getAttribute('width'));
                $this->assertGreaterThan(0, (int) $image->getAttribute('height'));
                $this->assertTrue($image->getAttribute('fetchpriority') === 'high' || $image->getAttribute('loading') === 'lazy');
            }
            $this->assertStringContainsString('href="'.route('contact').'"', $html);
            $this->get('https://kobcode.test/sitemap.xml')->assertSee('https://kobcode.test'.$path, false);
        }
        $this->get('https://kobcode.test/uslugi/platformy-b2b')->assertSee('/realizacje/cermax');
        $this->get('https://kobcode.test/uslugi/aplikacje-internetowe')->assertSee('/realizacje/olaya-aplikacja');
        $this->get('https://kobcode.test/uslugi/strony-internetowe')->assertSee('/realizacje/walbud24')->assertSee('/realizacje/parkstop')->assertSee('/realizacje/olaya-strona');
        $this->assertCount(5, array_unique($titles));
        $this->assertCount(5, array_unique($descriptions));
        $this->get('https://kobcode.test/realizacje/olaya-aplikacja')->assertSee('dane demonstracyjne');
        $this->get('https://kobcode.test/realizacje/cermax')->assertDontSee('Projekt dla klienta')->assertDontSee('Zastosowane technologie');
    }

    public function test_import_handles_legacy_cermax_slug_and_does_not_publish_unverified_records(): void
    {
        $project = Project::create(['title' => 'Własna nazwa', 'slug' => 'b2b-cermax', 'category' => 'B2B', 'summary' => 'Opis', 'relationship' => 'unverified']);
        $this->artisan('kobcode:case-studies', ['--publish' => true])->assertSuccessful();
        $this->assertDatabaseCount('projects', 5);
        $this->assertDatabaseMissing('projects', ['slug' => 'cermax']);
        $this->assertSame('Własna nazwa', $project->fresh()->title);
        $this->assertFalse($project->fresh()->is_published);
        $this->get('https://kobcode.test/realizacje/b2b-cermax')->assertNotFound();
        $project->update(['relationship' => 'portfolio', 'approved_for_publication' => true, 'is_published' => true]);
        $this->get('https://kobcode.test/uslugi/platformy-b2b')->assertOk()->assertSee('/realizacje/b2b-cermax');
    }

    public function test_rich_content_is_sanitized_and_legacy_plain_text_remains_readable(): void
    {
        $html = ProjectContent::html('<h1>Podtytuł</h1><p style="position:fixed" onclick="alert(1)">Tekst <strong>ważny</strong></p><script>alert(2)</script><a href="javascript:alert(3)">link</a><img src="x" onerror="alert(4)"><ul><li>Element</li></ul><a href="/kontakt">Kontakt</a>');
        foreach (['<script', 'onclick', 'onerror', 'javascript:', 'position:fixed', '<h1', '<img'] as $unsafe) $this->assertStringNotContainsString($unsafe, $html);
        $this->assertStringContainsString('<strong>ważny</strong>', $html);
        $this->assertStringContainsString('<ul><li>Element</li></ul>', $html);
        $this->assertStringContainsString('href="/kontakt"', $html);
        $this->assertSame('<p>Pierwszy wiersz<br>\nDrugi &amp; trzeci</p>', str_replace("\n", '\n', ProjectContent::html("Pierwszy wiersz\nDrugi & trzeci")));
    }

    public function test_admin_can_create_and_edit_case_fields_without_changing_publication_rules(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Storage::disk('public')->put('portfolio/demo.webp', file_get_contents(public_path('images/portfolio/cermax-home-1600.webp')));
        Livewire::test(CreateProject::class)->fillForm([
            'title' => 'Nowa realizacja', 'slug' => 'nowa-realizacja', 'category' => 'Aplikacja', 'summary' => 'Opis projektu', 'relationship' => 'client', 'delivery_status' => 'deployed', 'sort_order' => 10,
            'goal' => '<p>Cel <strong>projektu</strong></p>', 'technologies' => ['Laravel'], 'service_links' => ['apps'],
            'gallery' => [['path' => ['portfolio/demo.webp'], 'alt' => 'Widok demonstracyjny', 'caption' => 'Dane demonstracyjne']],
            'seo_title' => 'Nowa realizacja — KOBCODE', 'seo_description' => 'Opis nowej realizacji.',
            'approved_for_publication' => true, 'is_published' => false,
        ])->call('create')->assertHasNoFormErrors();
        $project = Project::where('slug', 'nowa-realizacja')->firstOrFail();
        $this->assertSame(['Laravel'], $project->technologies);
        $this->assertSame(['apps'], $project->service_links);
        $this->assertSame('Widok demonstracyjny', $project->gallery[0]['alt']);
        $this->get('https://kobcode.test/realizacje/nowa-realizacja')->assertNotFound();
        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])->fillForm(['result' => '<p>Gotowy panel.</p>', 'is_published' => true])->call('save')->assertHasNoFormErrors();
        $this->get('https://kobcode.test/realizacje/nowa-realizacja')->assertOk()->assertSee('Gotowy panel.')->assertSee('Widok demonstracyjny');
    }

    public function test_schema_extension_preserves_existing_records(): void
    {
        $migration = require database_path('migrations/2026_11_19_160000_add_case_study_fields_to_projects.php');
        $migration->down();
        $project = Project::create(['title' => 'Starszy wpis', 'slug' => 'starszy-wpis', 'category' => 'Strona', 'summary' => 'Treść do zachowania', 'relationship' => 'client', 'approved_for_publication' => true, 'is_published' => true]);
        $before = $project->getAttributes();
        $migration->up();
        $this->assertSame('Treść do zachowania', $project->fresh()->summary);
        $this->assertTrue($project->fresh()->is_published);
        foreach ($before as $key => $value) $this->assertEquals($value, $project->fresh()->getRawOriginal($key));
        $this->assertNull($project->fresh()->gallery);
        $this->get('https://kobcode.test/realizacje/starszy-wpis')->assertOk();
    }
}
