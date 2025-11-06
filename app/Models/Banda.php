<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Banda extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'bandas';

    protected $fillable = [
        'nome',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'band_id');
    }
}


