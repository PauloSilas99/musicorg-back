<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bandas', function (Blueprint $table): void {
            $table->id();
            $table->string('nome', 50);
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bandas');
    }
};


