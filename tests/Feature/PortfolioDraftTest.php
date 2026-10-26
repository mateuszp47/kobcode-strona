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
        $this->assertDatabaseHas('projects', ['slug'=>'olaya-strona', 'relationship'=>'related', 'delivery_status'=>'unverified']);
        $project = Project::where('slug','olaya-aplikacja')->firstOrFail();
        $project->update(['summary'=>'Opis po korekcie']);
        $this->artisan('kobcode:portfolio-drafts')->assertSuccessful();
        $this->assertDatabaseCount('projects', 3);
        $this->assertSame('Opis po korekcie', $project->fresh()->summary);
        $this->get('/realizacje/olaya-aplikacja')->assertNotFound();
        $project->update(['approved_for_publication'=>true, 'is_published'=>true]);
        $this->get('/realizacje/olaya-aplikacja')->assertOk()->assertSee('Projekt dla firmy powiązanej')->assertSee('Status wdrożenia do potwierdzenia');
    }
}
