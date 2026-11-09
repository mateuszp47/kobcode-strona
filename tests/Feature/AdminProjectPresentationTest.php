<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProjectPresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_projects_use_compact_sections_in_admin_order(): void
    {
        $this->withoutVite();
        $data = ['category' => 'Strona internetowa', 'summary' => 'Opis realizacji',
            'relationship' => 'client', 'delivery_status' => 'deployed',
            'approved_for_publication' => true, 'is_published' => true];
        Project::create($data + ['title' => 'Drugi projekt', 'slug' => 'drugi-projekt', 'sort_order' => 20]);
        Project::create($data + ['title' => 'Home Kreator', 'slug' => 'home-kreator', 'sort_order' => 10,
            'result' => 'Czytelna prezentacja oferty.', 'cover' => 'portfolio/home.webp',
            'cover_alt' => 'Strona główna Home Kreator']);
        Project::create(array_replace($data, ['title' => 'Ukryty projekt', 'slug' => 'ukryty',
            'sort_order' => 0, 'is_published' => false]));

        $html = $this->get('/realizacje')->assertOk()
            ->assertSee('class="compact-case" id="project-home-kreator"', false)
            ->assertSee('Czytelna prezentacja oferty.')
            ->assertSee('alt="Strona główna Home Kreator"', false)
            ->assertSee('portfolio/home.webp')
            ->assertSee('href="#project-home-kreator"><span>05</span>Home Kreator</a>', false)
            ->assertSee('href="#project-drugi-projekt"><span>06</span>Drugi projekt</a>', false)
            ->assertDontSee('href="#project-ukryty"', false)
            ->assertSee(route('project', 'home-kreator'), false)
            ->assertDontSee('Ukryty projekt')
            ->assertDontSee('class="project-card"', false)
            ->getContent();

        $this->assertLessThan(strpos($html, 'id="project-drugi-projekt"'), strpos($html, 'id="project-home-kreator"'));
        $this->get('/realizacje/home-kreator')->assertOk();
        $withoutCover = explode('</section>', explode('id="project-drugi-projekt"', $html)[1])[0];
        $this->assertStringNotContainsString('<img', $withoutCover);
    }
}
