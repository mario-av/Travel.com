<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Comentario Model - Represents user comments and reviews.
 *
 * @property int $id
 * @property string $contenido
 * @property int $puntuacion
 * @property bool $aprobado
 * @property int $user_id
 * @property int $vacacion_id
 */
class Comentario extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comentarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contenido',
        'puntuacion',
        'aprobado',
        'user_id',
        'vacacion_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'aprobado' => 'boolean',
    ];

    /**
     * Get the user who wrote this comment.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the vacation for this comment.
     *
     * @return BelongsTo
     */
    public function vacacion(): BelongsTo
    {
        return $this->belongsTo(Vacacion::class, 'vacacion_id');
    }
}
