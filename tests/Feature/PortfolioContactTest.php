<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Project, Inquiry, User};
use Filament\Facades\Filament;
use Livewire\Livewire;
class PortfolioContactTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->withoutVite(); }
    private function project(array $extra=[]): Project {
        return Project::create(array_merge(['title'=>'Projekt demonstracyjny','slug'=>'demo','category'=>'Strona','summary'=>'Opis demonstracyjny','relationship'=>'client'],$extra));
    }
    public function test_unapproved_and_draft_projects_are_not_public(): void {
        $p=$this->project(['is_published'=>true]);
        $this->get('/realizacje')->assertDontSee($p->title);
        $this->get('/realizacje/demo')->assertNotFound();
        $p->update(['approved_for_publication'=>true]);
        $this->get('/realizacje/demo')->assertNotFound();
        $p->update(['is_published'=>true]);
        $this->get('/realizacje')->assertSee($p->title);
        $this->get('/realizacje/demo')->assertOk()->assertSee('Projekt dla klienta');
    }
    public function test_contact_is_disabled_by_default_and_validated_when_enabled(): void {
        $data=['name'=>'Jan','email'=>'jan@example.com','message'=>'Potrzebuję strony firmowej.'];
        $this->post('/kontakt',$data)->assertStatus(503);
        $this->assertDatabaseCount('inquiries',0);
        config(['site.contact_enabled'=>true]);
        $this->from('/kontakt')->post('/kontakt',['name'=>'Jan'])->assertSessionHasErrors(['email','message']);
        $this->post('/kontakt',$data+['website'=>'spam'])->assertSessionHasErrors('website');
        $this->post('/kontakt',$data)->assertRedirect('/kontakt')->assertSessionHas('success');
        $this->assertDatabaseHas('inquiries',['email'=>'jan@example.com']);
    }
    public function test_admin_panel_requires_an_admin_and_renders_resources(): void {
        $this->get('/admin')->assertRedirect('/admin/login');
        $u=User::factory()->create();
        $this->actingAs($u)->get('/admin')->assertForbidden();
        $u->is_admin=true;$u->save();
        $this->get('/admin')->assertOk();
        $this->get('/admin/projects')->assertOk();
        $this->get('/admin/projects/create')->assertOk();
        $inquiry=Inquiry::create(['name'=>'Jan','email'=>'jan@example.com','message'=>'Zapytanie testowe']);
        $this->get('/admin/inquiries/'.$inquiry->id.'/edit')->assertOk();
    }
    public function test_admin_can_create_a_project_as_a_draft(): void {
        $u=User::factory()->create();$u->is_admin=true;$u->save();$this->actingAs($u);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Livewire::test(\App\Filament\Resources\ProjectResource\Pages\CreateProject::class)
            ->fillForm(['title'=>'Nowy projekt','slug'=>'nowy-projekt','category'=>'Strona','summary'=>'Opis','relationship'=>'unverified','delivery_status'=>'in_development','sort_order'=>0,'is_published'=>false,'approved_for_publication'=>false])
            ->call('create')->assertHasNoFormErrors();
        $this->assertDatabaseHas('projects',['slug'=>'nowy-projekt','is_published'=>false]);
    }
}
