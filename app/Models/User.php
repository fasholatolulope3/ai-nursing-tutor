<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'bio',
        'preferences',
    ];

    /**
     * Get the URL to the user's profile photo.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->avatar_path
            ? Storage::disk('public')->url($this->avatar_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF',
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'preferences' => 'array',
        ];
    }

    /**
     * Get the user's simulation sessions.
     */
    public function simulationSessions()
    {
        return $this->hasMany(SimulationSession::class);
    }

    /**
     * Get the user's support tickets.
     */
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }
}
