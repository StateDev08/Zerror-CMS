<?php

namespace App\Http\Middleware;

use App\Support\PluginManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotMaintenanceMode
{
    /**
     * Wenn Wartungsmodus aktiv ist: Nur Admin-Zugang und Login erlauben, sonst Wartungsseite anzeigen.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->maintenanceEnabled()) {
            return $next($request);
        }

        // Admin-Panel und Login-Routen immer erlauben
        if ($request->is('admin*') || $request->routeIs('login')) {
            return $next($request);
        }

        // Eingeloggte User mit Zugang zum Admin zählen als „Admin“
        $user = $request->user();
        if ($user) {
            try {
                $panel = \Filament\Facades\Filament::getPanel('admin');
                if ($panel && method_exists($user, 'canAccessPanel') && $user->canAccessPanel($panel)) {
                    return $next($request);
                }
            } catch (\Throwable $e) {
                // Filament nicht geladen oder Panel nicht vorhanden
            }
        }

        $message = $this->maintenanceMessage();
        return response()->view('maintenance', ['message' => $message], 503);
    }

    protected function maintenanceEnabled(): bool
    {
        try {
            $pluginManager = app(PluginManager::class);
            if ($pluginManager->isEnabled('maintenance')) {
                $config = plugin_config('maintenance');
                return (bool) ($config['enabled'] ?? false);
            }
            return (bool) filter_var(setting('maintenance_enabled', '0'), FILTER_VALIDATE_BOOLEAN);
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function maintenanceMessage(): ?string
    {
        try {
            $pluginManager = app(PluginManager::class);
            if ($pluginManager->isEnabled('maintenance')) {
                $config = plugin_config('maintenance');
                $msg = trim((string) ($config['message'] ?? ''));
                return $msg !== '' ? $msg : null;
            }
        } catch (\Throwable $e) {
            //
        }
        return null;
    }
}
