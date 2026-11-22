<?php
namespace App\Http\Middleware\Demo;

use App\Demo\InstallFlow\Workspace;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UseInstallFlowWorkspace
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->session()->get('installflow.token');
        $started = $request->session()->get('installflow.started', 0);
        if (! is_string($token) || ! preg_match('/^[a-f0-9]{64}$/D', $token)
            || $started < now()->subMinutes(config('demo.lifetime_minutes'))->timestamp) {
            $token = bin2hex(random_bytes(32));
            $request->session()->put(['installflow.token' => $token, 'installflow.started' => now()->timestamp]);
        }
        $template = Workspace::template();
        $path = Workspace::path($token);
        $lock = fopen($path.'.lock', 'c');
        flock($lock, LOCK_EX);
        try {
            if (! is_file($path)) { copy($template, $path); chmod($path, 0600); }
            touch($path);
            Workspace::connect($path);
            $request->attributes->set('installflow.token', $token);
            $response = $next($request);
            $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            return $response;
        } finally {
            DB::disconnect('installflow');
            flock($lock, LOCK_UN); fclose($lock);
        }
    }
}
