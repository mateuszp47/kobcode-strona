<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_connect_real_portfolio_sections_to_a_quote_and_match_their_breadcrumbs(): void
    {
        $this->withoutVite();
        config(['app.url' => 'https://kobcode.test', 'site.indexable' => true]);
        $portfolio = $this->get('/realizacje')->assertOk();
        $home = $this->get('/')->assertOk();
        $sitemap = $this->get('/sitemap.xml')->assertOk();

        foreach (config('offer') as $name => $service) {
            $path = '/uslugi/'.$service['slug'];
            $response = $this->get($path)->assertOk()->assertSee($service['name'])
                ->assertSee(route('contact'), false)->assertSee('Zapytaj o wycenę');
            $home->assertSee(route($name), false);
            $sitemap->assertSee('<loc>https://kobcode.test'.$path.'</loc>', false);
            foreach ($service['cases'] as $fragment => $label) {
                $response->assertSee(route('portfolio').'#'.$fragment, false)->assertSee($label);
                $portfolio->assertSee('id="'.$fragment.'"', false);
            }
            foreach ($service['related'] as $related) {
                $response->assertSee(route($related), false);
            }

            $dom = new \DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
            $xpath = new \DOMXPath($dom);
            $json = $xpath->evaluate('string(//script[@type="application/ld+json"])');
            $graph = array_column(json_decode($json, true, flags: JSON_THROW_ON_ERROR)['@graph'], null, '@type');
            $trail = $graph['BreadcrumbList']['itemListElement'];
            $this->assertSame([1, 2], array_column($trail, 'position'));
            $this->assertSame($xpath->evaluate('string(//nav[@aria-label="Ścieżka nawigacji"]/a)'), $trail[0]['name']);
            $this->assertSame($xpath->evaluate('string(//nav/span[@aria-current="page"])'), $trail[1]['name']);
            $this->assertSame('https://kobcode.test'.$path, $trail[1]['item']);
            $this->assertSame($service['name'], $graph['Service']['name']);
        }
        $this->get('/uslugi/aplikacje-internetowe')->assertSee('id="integracje"', false)->assertSee('id="automatyzacje"', false);
        $this->get('/uslugi/nieistniejaca-usluga')->assertNotFound();
        $this->app['env'] = 'production';
        $this->get('https://www.kobcode.test/uslugi/platformy-b2b?utm_source=test')
            ->assertStatus(301)->assertRedirect('https://kobcode.test/uslugi/platformy-b2b?utm_source=test');
    }
}
