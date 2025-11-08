<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicaEvento extends Model
{
    use HasFactory;

    protected $table = 'musicas_evento';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'titulo_musica',
        'artista_ou_tom',
        'ordem',
        'link_musica',
    ];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'event_id');
    }
}


