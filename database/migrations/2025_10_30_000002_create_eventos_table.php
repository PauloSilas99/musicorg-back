<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table): void {
            $table->id();

            // Multi-tenancy key
            $table->foreignId('band_id')
                ->constrained('bandas')
                ->cascadeOnDelete();

            $table->string('titulo', 255);
            $table->date('data');
            $table->time('hora');
            $table->string('local', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};


