<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use App\Models\UserNotification;
use App\Support\HtmlContent;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'avatar' => ['nullable', 'image', 'max:'.\App\Support\UploadLimits::imageKb()],
            'avatar_remove' => ['nullable', 'in:0,1'],
            'biography' => ['nullable', 'string', 'max:50000'],
            'job' => ['nullable', 'string', 'max:191'],
            'about_me' => ['nullable', 'string', 'max:50000'],
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

        $validated['biography'] = HtmlContent::sanitizeOptional($validated['biography'] ?? null);
        $validated['about_me'] = HtmlContent::sanitizeOptional($validated['about_me'] ?? null);

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
        Auth::user()->update(['password' => $request->password]);
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

    public function discord()
    {
        $user = Auth::user();
        return view('usercp.discord', compact('user'));
    }

    public function generateDiscordLinkToken()
    {
        $user = Auth::user();
        $user->update([
            'discord_link_token' => Str::random(32),
            'discord_link_token_expires_at' => now()->addMinutes(15),
        ]);

        return redirect()->route('usercp.discord')->with('success', __('usercp.discord_token_generated'));
    }

    public function unlinkDiscord()
    {
        $user = Auth::user();
        $user->update([
            'discord_id' => null,
            'discord_link_token' => null,
            'discord_link_token_expires_at' => null,
        ]);

        return redirect()->route('usercp.discord')->with('success', __('usercp.discord_unlinked'));
    }

    public function notifications()
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('usercp.notifications', compact('notifications'));
    }

    public function markRead(Request $request, UserNotification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        if ($notification->link && $request->isMethod('get')) {
            return redirect()->to($notification->link);
        }

        return redirect()->route('usercp.notifications')->with('success', __('usercp.notifications_marked_read'));
    }

    public function markAllRead()
    {
        UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('usercp.notifications')->with('success', __('usercp.notifications_all_marked_read'));
    }
}
