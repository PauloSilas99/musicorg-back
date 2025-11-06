<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musicas_evento', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')
                ->constrained('eventos')
                ->cascadeOnDelete();
            $table->string('titulo_musica', 255);
            $table->string('artista_ou_tom', 100)->nullable();
            $table->integer('ordem')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musicas_evento');
    }
};


