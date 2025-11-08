<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bandas', function (Blueprint $table): void {
            $table->string('plan', 20)
                ->default('gratuito')
                ->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('bandas', function (Blueprint $table): void {
            $table->dropColumn('plan');
        });
    }
};


