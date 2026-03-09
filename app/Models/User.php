<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
        'avatar', 'biography', 'job', 'about_me',
        'location', 'website', 'discord_handle', 'discord_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
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
