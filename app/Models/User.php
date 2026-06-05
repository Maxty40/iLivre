<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Filament Access Policy Control
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role_id === 1;
    }

    /**
     * Relationship: User belongs to a specific Role (Admin/User).
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relationship: User has exactly one Membership Card (1:1).
     */
    public function membershipCard(): HasOne
    {
        return $this->hasOne(MembershipCard::class);
    }

    /**
     * Relationship: User can have multiple active or past loans (1:N).
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
