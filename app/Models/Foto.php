<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Foto Model - Represents vacation package images.
 *
 * @property int $id
 * @property string $ruta
 * @property string|null $nombre_original
 * @property bool $principal
 * @property int $vacacion_id
 */
class Foto extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fotos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ruta',
        'nombre_original',
        'principal',
        'vacacion_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'principal' => 'boolean',
    ];

    /**
     * Get the vacation that owns this photo.
     *
     * @return BelongsTo
     */
    public function vacacion(): BelongsTo
    {
        return $this->belongsTo(Vacacion::class, 'vacacion_id');
    }
}
