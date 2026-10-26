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
        $pages = collect(['home', 'web', 'apps', 'portfolio', 'about'])->map(fn ($name) => ['url' => Seo::url(route($name, [], false))]);
        if (config('site.contact_enabled') || config('site.email') || config('site.phone')) {
            $pages->push(['url' => Seo::url(route('contact', [], false))]);
        }
        foreach (Project::published()->orderBy('id')->cursor() as $project) {
            $pages->push(['url' => Seo::url(route('project', $project->slug, false)), 'modified' => $project->updated_at?->toAtomString()]);
        }
        return response()->view('sitemap', compact('pages'))->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
