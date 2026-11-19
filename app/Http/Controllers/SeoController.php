<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\Seo;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $text = "User-agent: *\n";
        $text .= config('site.indexable')
            ? "Allow: /\nDisallow: /admin\nDisallow: /livewire\nSitemap: ".Seo::url('sitemap.xml')."\n"
            : "Disallow: /\n";
        return response($text, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(): Response
    {
        abort_unless(config('site.indexable'), 404);
        $pages = collect(['home', 'web', 'apps', 'portfolio', 'about', 'contact', 'privacy'])
            ->map(fn ($name) => ['url' => Seo::url(route($name, [], false))]);
        foreach (Project::published()->orderBy('id')->cursor() as $project) {
            $pages->push(['url' => Seo::url(route('project', $project->slug, false)), 'modified' => $project->updated_at?->toAtomString()]);
        }
        // Serialize XML directly: sitemap delivery must not depend on compiled Blade views.
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $urlset = $xml->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
        $xml->appendChild($urlset);
        foreach ($pages as $page) {
            $url = $xml->createElement('url');
            $urlset->appendChild($url);
            foreach (['loc' => $page['url'], 'lastmod' => $page['modified'] ?? null] as $tag => $value) {
                if ($value !== null) {
                    $element = $xml->createElement($tag);
                    $element->appendChild($xml->createTextNode($value));
                    $url->appendChild($element);
                }
            }
        }

        return response($xml->saveXML(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
