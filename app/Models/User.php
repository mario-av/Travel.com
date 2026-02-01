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
 * - `user`: Default role for registered users.
 * - `advanced`: Can create/edit their own vacations.
 * - `admin`: Full administrative access.
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

    /**
     * Get the vacations created by this user.
     *
     * @return HasMany
     */
    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class, 'user_id');
    }

    /**
     * Get the bookings made by this user.
     *
     * @return HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    /**
     * Get the reviews made by this user.
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id');
    }
}
