<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, CanResetPasswordContract
{
    use CanResetPassword, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'avatar', 'biography', 'job', 'about_me',
        'location', 'website', 'discord_handle', 'discord_id',
        'discord_link_token', 'discord_link_token_expires_at',
    ];

    protected $hidden = ['password', 'remember_token', 'discord_link_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'discord_link_token_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('super-admin') || $this->can('access_admin');
    }

    public function clanMember(): HasOne
    {
        return $this->hasOne(ClanMember::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }
        return storage_asset($this->avatar);
    }
}
