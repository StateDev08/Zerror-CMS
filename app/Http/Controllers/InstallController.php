<?php

namespace App\Http\Controllers;

use App\Support\Installer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallController extends Controller
{
    public function index(Request $request)
    {
        try {
            $envPath = base_path('.env');
            if (! File::exists($envPath) && File::exists(base_path('.env.example'))) {
                File::copy(base_path('.env.example'), $envPath);
                Artisan::call('key:generate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // .env anlegen fehlgeschlagen (z. B. Schreibrechte) – mit Standard weiter
        }

        $step = (int) $request->get('step', 1);
        if ($step < 1 || $step > 5) {
            $step = 1;
        }
        try {
            $requirements = Installer::checkRequirements();
        } catch (\Throwable $e) {
            $requirements = ['ok' => false, 'errors' => ['Prüfung fehlgeschlagen: ' . $e->getMessage()], 'php' => true, 'extensions' => [], 'writable' => []];
        }
        if ($step === 1 && ! $requirements['ok']) {
            return view('install.index', ['step' => 1, 'requirements' => $requirements]);
        }
        return view('install.index', ['step' => $step, 'requirements' => $requirements]);
    }

    public function database(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|string|max:10',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',
        ]);
        $host = $request->input('db_host');
        $port = $request->input('db_port');
        $database = $request->input('db_database');
        $username = $request->input('db_username');
        $password = (string) $request->input('db_password');

        try {
            $pdo = new \PDO(
                "mysql:host={$host};port={$port};dbname={$database}",
                $username,
                $password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['db' => 'Verbindung fehlgeschlagen: ' . $e->getMessage()]);
        }

        $this->writeEnvDatabase($host, $port, $database, $username, $password);
        return redirect()->route('install.index', ['step' => 3]);
    }

    public function migrate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            return redirect()->route('install.index', ['step' => 3])
                ->with('error', 'Migration fehlgeschlagen: ' . $e->getMessage());
        }
        return redirect()->route('install.index', ['step' => 4]);
    }

    public function finish(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);
        $name = $request->input('admin_name');
        $email = $request->input('admin_email');
        $password = bcrypt($request->input('admin_password'));

        try {
            $user = \App\Models\User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);
            Artisan::call('db:seed', ['--force' => true, '--class' => \Database\Seeders\RolePermissionSeeder::class]);
            $user->assignRole('super-admin');
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['finish' => $e->getMessage()]);
        }

        if (! Installer::markInstalled()) {
            return back()->withInput()->withErrors(['finish' => __('install.storage_failed')]);
        }
        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
        } catch (\Throwable $e) {
            // ignore
        }

        return redirect()->to('/admin')->with('install_complete', true);
    }

    protected function writeEnvDatabase(string $host, string $port, string $database, string $username, string $password): void
    {
        $envPath = base_path('.env');
        if (! File::exists($envPath)) {
            if (File::exists(base_path('.env.example'))) {
                File::copy(base_path('.env.example'), $envPath);
            } else {
                File::put($envPath, "APP_KEY=\n");
            }
        }
        $content = File::get($envPath);
        $lines = [
            'DB_HOST' => $host,
            'DB_PORT' => $port,
            'DB_DATABASE' => $database,
            'DB_USERNAME' => $username,
            'DB_PASSWORD' => $password,
        ];
        foreach ($lines as $key => $value) {
            if (preg_match('/^' . preg_quote($key, '/') . '=/m', $content)) {
                $content = preg_replace('/^(' . preg_quote($key, '/') . ')=.*/m', '$1=' . $value, $content, 1);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }
        File::put($envPath, $content);
        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
        }
        Artisan::call('config:clear');
    }
}
