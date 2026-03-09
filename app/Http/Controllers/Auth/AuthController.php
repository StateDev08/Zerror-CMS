<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($validated, (bool) $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('usercp.index'));
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (! (bool) filter_var(setting('auth_registration_enabled', '1'), FILTER_VALIDATE_BOOLEAN)) {
            abort(404);
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (! (bool) filter_var(setting('auth_registration_enabled', '1'), FILTER_VALIDATE_BOOLEAN)) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $memberRole = \Spatie\Permission\Models\Role::where('name', 'member')->where('guard_name', 'web')->first();
            if ($memberRole) {
                $user->assignRole($memberRole);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('usercp.index')->with('success', __('auth.registered'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
