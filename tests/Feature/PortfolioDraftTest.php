<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
class PortfolioDraftTest extends TestCase
{
    use RefreshDatabase;
    public function test_drafts_preserve_relationships_and_do_not_overwrite_edits(): void
    {
        $this->withoutVite();
        $this->artisan('kobcode:portfolio-drafts')->assertSuccessful();
        $this->assertDatabaseCount('projects', 3);
        $this->assertDatabaseHas('projects', ['slug'=>'walbud24', 'relationship'=>'client', 'is_published'=>false]);
        $this->assertDatabaseHas('projects', ['slug'=>'olaya-strona', 'relationship'=>'related', 'delivery_status'=>'deployed']);
        $project = Project::where('slug','olaya-aplikacja')->firstOrFail();
        $project->update(['summary'=>'Opis po korekcie']);
        $this->artisan('kobcode:portfolio-drafts')->assertSuccessful();
        $this->assertDatabaseCount('projects', 3);
        $this->assertSame('Opis po korekcie', $project->fresh()->summary);
        $this->get('/realizacje/olaya-aplikacja')->assertNotFound();
        $project->update(['approved_for_publication'=>true, 'is_published'=>true]);
        $this->get('/realizacje/olaya-aplikacja')->assertOk()->assertSee('Projekt dla firmy powiązanej')->assertSee('Wdrożony')->assertDontSee('do weryfikacji')->assertDontSee('do potwierdzenia');
    }
    public function test_deployment_update_preserves_custom_copy_and_publication_choices(): void
    {
        $project = Project::create(['slug'=>'olaya-aplikacja', 'title'=>'Olaya', 'category'=>'Aplikacja', 'relationship'=>'related', 'delivery_status'=>'unverified', 'summary'=>'Własny opis', 'scope'=>'Wykonanie projektu od podstaw. Moduły i procesy zostaną opisane jako działające dopiero po weryfikacji.', 'approved_for_publication'=>false, 'is_published'=>false]);
        $migration = require database_path('migrations/2026_10_26_180000_confirm_portfolio_deployments.php');
        $migration->up();
        $project->refresh();
        $this->assertSame('deployed', $project->delivery_status);
        $this->assertSame('Własny opis', $project->summary);
        $this->assertStringNotContainsString('weryfikacji', $project->scope);
        $this->assertFalse($project->is_published);
        $this->assertFalse($project->approved_for_publication);
    }
}
