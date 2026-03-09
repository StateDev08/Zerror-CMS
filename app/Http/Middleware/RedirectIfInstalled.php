<?php

namespace App\Http\Middleware;

use App\Support\Installer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Installer::isInstalled()) {
            config(['session.driver' => 'file']);
            return $next($request);
        }
        return redirect()->to('/');
    }
}
