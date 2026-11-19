<?php

namespace App\Http\Middleware;

use App\Support\Seo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicSeo
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() >= 400 || $request->is('admin', 'admin/*', 'livewire', 'livewire/*', 'up')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

            return $response;
        }

        // Only normalize successful public GET/HEAD routes; never redirect a form POST or panel request.
        if (app()->environment('production') && config('site.indexable') && $request->isMethodSafe()
            && $response->getStatusCode() === 200
            && $request->routeIs('home', 'portfolio', 'project', 'about', 'contact', 'privacy', 'robots', 'sitemap', ...array_keys(config('offer')))) {
            $canonical = Seo::url($request->path());
            $requestedPath = parse_url($request->getRequestUri(), PHP_URL_PATH);
            if ($request->getHost() !== parse_url($canonical, PHP_URL_HOST)
                || $requestedPath !== parse_url($canonical, PHP_URL_PATH)) {
                $query = $request->getQueryString();

                return redirect($canonical.($query ? '?'.$query : ''), 301);
            }
        }

        // HTTPS enforcement belongs to the confirmed hosting/proxy configuration, avoiding TLS redirect loops.
        return $response;
    }
}
