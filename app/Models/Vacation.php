<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Vacation Model - Represents vacation packages.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $location
 * @property float $price
 * @property int $duration_days
 * @property int $available_slots
 * @property string $start_date
 * @property string $end_date
 * @property bool $featured
 * @property bool $active
 * @property int $category_id
 * @property int $user_id
 */
class Vacation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vacations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'location',
        'price',
        'duration_days',
        'available_slots',
        'start_date',
        'end_date',
        'featured',
        'active',
        'approved',
        'category_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'featured' => 'boolean',
        'active' => 'boolean',
        'approved' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the category for this vacation.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the user who created this vacation.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the photos for this vacation.
     *
     * @return HasMany
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class, 'vacation_id');
    }

    /**
     * Get the bookings for this vacation.
     *
     * @return HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'vacation_id');
    }

    /**
     * Get the reviews for this vacation.
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'vacation_id');
    }

    /**
     * Get the count of approved reviews.
     *
     * @return int
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->where('approved', true)->count();
    }

    /**
     * Get the average rating of approved reviews.
     *
     * @return float
     */
    public function getAverageRatingAttribute(): float
    {
        return (float) $this->reviews()->where('approved', true)->avg('rating') ?? 0.0;
    }
}
