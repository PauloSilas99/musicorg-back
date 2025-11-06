<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musicos_evento', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')
                ->constrained('eventos')
                ->cascadeOnDelete();
            $table->string('nome_musico', 100);
            $table->string('funcao', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musicos_evento');
    }
};


