<?php

namespace App\Http\Middleware;

use App\Models\Translation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class LoadDbTranslations
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! \App\Support\Installer::isInstalled()) {
            return $next($request);
        }
        try {
            $locale = app()->getLocale();
            if (Schema::hasTable('translations')) {
                $lines = Translation::loadForLocale($locale);
                if (! empty($lines)) {
                    app('translator')->addLines($lines, $locale);
                }
            }
        } catch (\Throwable $e) {
            // DB nicht erreichbar → ohne DB-Übersetzungen fortfahren
        }
        return $next($request);
    }
}
