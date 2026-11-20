<?php
namespace App\Http\Middleware\Demo;

use App\Demo\InstallFlow\DemoAccess;
use Closure;
use Illuminate\Http\Request;

class EnsureDemoEnabled
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(DemoAccess::enabled(), 404);
        $response = $next($request);
        if (! DemoAccess::indexable() || $request->routeIs('installflow.*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }
        return $response;
    }
}
