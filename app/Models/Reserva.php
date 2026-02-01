<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Reserva Model - Represents user vacation bookings.
 *
 * @property int $id
 * @property int $num_personas
 * @property float $precio_total
 * @property string $estado
 * @property string|null $notas
 * @property int $user_id
 * @property int $vacacion_id
 */
class Reserva extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reservas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'num_personas',
        'precio_total',
        'estado',
        'notas',
        'user_id',
        'vacacion_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'precio_total' => 'decimal:2',
    ];

    /**
     * Get the user who made this reservation.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the vacation for this reservation.
     *
     * @return BelongsTo
     */
    public function vacacion(): BelongsTo
    {
        return $this->belongsTo(Vacacion::class, 'vacacion_id');
    }
}
