<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model - Represents application users.
 *
 * Roles:
 * - `user`: Default role for registered users (Can book/review).
 * - `verified`: Verified identity (formerly advanced), manages own vacations.
 * - `advanced`: Can create vacations (requires approval), manages own.
 * - `admin`: Full administrative access (approves vacations).
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $rol
 * @property \DateTime|null $email_verified_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'accent_color',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    /**
     * Check if user has a specific role.
     *
     * @param string $rol The role to check.
     * @return bool
     */
    public function isRol(string $rol): bool
    {
        return $this->rol === $rol;
    }

    /**
     * Check if user is admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isRol('admin');
    }

    /**
     * Check if user is advanced (or admin).
     *
     * @return bool
     */
    public function isAdvanced(): bool
    {
        return $this->isRol('advanced') || $this->isRol('admin');
    }

    /**
     * Check if user is regular user.
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->isRol('user');
    }

    /**
     * Check if user is verified role.
     *
     * @return bool
     */
    public function isVerifiedRole(): bool
    {
        return $this->isRol('verified');
    }

    /**
     * Check if user has booked a specific vacation.
     *
     * @param int $vacationId The vacation ID to check.
     * @return bool
     */
    public function hasBookedVacation(int $vacationId): bool
    {
        return $this->bookings()
            ->where('vacation_id', $vacationId)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class, 'user_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id');
    }
}
