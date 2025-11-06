<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    protected $fillable = [
        'band_id',
        'titulo',
        'data',
        'hora',
        'local',
    ];

    protected static function booted(): void
    {
        // Filtragem automática por tenant (banda logada)
        static::addGlobalScope('banda', function (Builder $builder): void {
            if (Auth::guard('bandas')->check()) {
                $builder->where('band_id', Auth::guard('bandas')->id());
            }
        });
    }

    public function banda(): BelongsTo
    {
        return $this->belongsTo(Banda::class, 'band_id');
    }

    public function musicos(): HasMany
    {
        return $this->hasMany(MusicoEvento::class, 'event_id');
    }

    public function musicas(): HasMany
    {
        return $this->hasMany(MusicaEvento::class, 'event_id');
    }
}


