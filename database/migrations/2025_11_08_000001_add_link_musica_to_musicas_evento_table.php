<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('musicas_evento', function (Blueprint $table): void {
            $table->string('link_musica', 2048)
                ->nullable()
                ->after('ordem');
        });
    }

    public function down(): void
    {
        Schema::table('musicas_evento', function (Blueprint $table): void {
            $table->dropColumn('link_musica');
        });
    }
};


