<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Vacacion Model - Represents vacation packages.
 *
 * @property int $id
 * @property string $titulo
 * @property string $descripcion
 * @property string $ubicacion
 * @property float $precio
 * @property int $duracion_dias
 * @property int $plazas_disponibles
 * @property string $fecha_inicio
 * @property string $fecha_fin
 * @property bool $destacado
 * @property bool $activo
 * @property int $tipo_id
 * @property int $user_id
 */
class Vacacion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vacaciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'ubicacion',
        'precio',
        'duracion_dias',
        'plazas_disponibles',
        'fecha_inicio',
        'fecha_fin',
        'destacado',
        'activo',
        'tipo_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'precio' => 'decimal:2',
        'destacado' => 'boolean',
        'activo' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    /**
     * Get the type for this vacation.
     *
     * @return BelongsTo
     */
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(Tipo::class, 'tipo_id');
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
    public function fotos(): HasMany
    {
        return $this->hasMany(Foto::class, 'vacacion_id');
    }

    /**
     * Get the reservations for this vacation.
     *
     * @return HasMany
     */
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'vacacion_id');
    }

    /**
     * Get the comments for this vacation.
     *
     * @return HasMany
     */
    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'vacacion_id');
    }
}
