<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserCpController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('usercp.index', compact('user'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('usercp.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'avatar_remove' => ['nullable', 'in:0,1'],
            'biography' => ['nullable', 'string', 'max:2000'],
            'job' => ['nullable', 'string', 'max:191'],
            'about_me' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:191'],
            'website' => ['nullable', 'string', 'url', 'max:255'],
            'discord_handle' => ['nullable', 'string', 'max:191'],
        ]);

        if ($request->input('avatar_remove') === '1') {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = null;
        } elseif ($request->hasFile('avatar') && $request->file('avatar') instanceof UploadedFile) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('profiles', 'public');
            $validated['avatar'] = $path;
        } else {
            unset($validated['avatar']);
        }

        $user->update($validated);
        return redirect()->route('usercp.profile')->with('success', __('usercp.profile_updated'));
    }

    public function password()
    {
        return view('usercp.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        Auth::user()->update(['password' => bcrypt($request->password)]);
        return redirect()->route('usercp.password')->with('success', __('usercp.password_updated'));
    }

    public function itemRequests()
    {
        $requests = ItemRequest::where('user_id', Auth::id())
            ->with('craftableItem')
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('usercp.item-requests', ['requests' => $requests]);
    }
}
