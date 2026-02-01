<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tipo Model - Represents vacation type categories.
 *
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 */
class Tipo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Get the vacations for this type.
     *
     * @return HasMany
     */
    public function vacaciones(): HasMany
    {
        return $this->hasMany(Vacacion::class, 'tipo_id');
    }
}
