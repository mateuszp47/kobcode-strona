<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_content_links_resolve_to_existing_pages_and_fragments(): void
    {
        $this->withoutVite();
        config(['app.url' => 'https://kobcode.test', 'site.indexable' => true]);
        $paths = ['/', '/realizacje', '/o-firmie', '/kontakt', '/polityka-prywatnosci'];
        foreach (config('offer') as $offer) {
            $paths[] = '/uslugi/'.$offer['slug'];
        }
        $pages = [];
        foreach ($paths as $path) {
            $html = $this->get('https://kobcode.test'.$path)->assertOk()->getContent();
            $dom = new \DOMDocument;
            @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
            $pages[$path] = new \DOMXPath($dom);
        }
        foreach ($pages as $path => $xpath) {
            foreach ($xpath->query('//a[@href]') as $link) {
                $href = $link->getAttribute('href');
                $url = parse_url($href);
                if (isset($url['scheme']) && ! in_array($url['scheme'], ['http', 'https'])) {
                    continue;
                }
                if (isset($url['host']) && $url['host'] !== 'kobcode.test') {
                    continue;
                }
                $target = $url['path'] ?? (isset($url['host']) ? '/' : $path);
                $this->assertArrayHasKey($target, $pages, $path.' links to '.$href);
                if (! empty($url['fragment'])) {
                    $ids = [];
                    foreach ($pages[$target]->query('//*[@id]') as $element) {
                        $ids[] = $element->getAttribute('id');
                    }
                    $this->assertContains(rawurldecode($url['fragment']), $ids, $path.' links to missing fragment '.$href);
                }
            }
        }
    }
}
