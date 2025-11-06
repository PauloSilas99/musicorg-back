<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicoEvento extends Model
{
    use HasFactory;

    protected $table = 'musicos_evento';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'nome_musico',
        'funcao',
    ];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'event_id');
    }
}


