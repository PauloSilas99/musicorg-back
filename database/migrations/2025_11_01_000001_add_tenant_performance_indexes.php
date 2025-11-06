<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adiciona índices de performance para queries multi-tenant.
     * Estes índices são críticos para garantir performance adequada
     * conforme o banco de dados cresce.
     */
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table): void {
            // Índice em band_id para performance em queries multi-tenant
            // Este índice é ESSENCIAL para performance em aplicações multi-tenant
            if (!$this->indexExists('eventos', 'eventos_band_id_index')) {
                $table->index('band_id', 'eventos_band_id_index');
            }

            // Índice composto para queries comuns: buscar eventos por tenant e data
            if (!$this->indexExists('eventos', 'eventos_band_id_data_index')) {
                $table->index(['band_id', 'data'], 'eventos_band_id_data_index');
            }
        });

        Schema::table('musicos_evento', function (Blueprint $table): void {
            // Índice em event_id para performance em joins
            if (!$this->indexExists('musicos_evento', 'musicos_evento_event_id_index')) {
                $table->index('event_id', 'musicos_evento_event_id_index');
            }
        });

        Schema::table('musicas_evento', function (Blueprint $table): void {
            // Índice em event_id para performance em joins
            if (!$this->indexExists('musicas_evento', 'musicas_evento_event_id_index')) {
                $table->index('event_id', 'musicas_evento_event_id_index');
            }

            // Índice composto para ordenação do setlist (event_id + ordem)
            if (!$this->indexExists('musicas_evento', 'musicas_evento_event_id_ordem_index')) {
                $table->index(['event_id', 'ordem'], 'musicas_evento_event_id_ordem_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table): void {
            $table->dropIndex('eventos_band_id_index');
            $table->dropIndex('eventos_band_id_data_index');
        });

        Schema::table('musicos_evento', function (Blueprint $table): void {
            $table->dropIndex('musicos_evento_event_id_index');
        });

        Schema::table('musicas_evento', function (Blueprint $table): void {
            $table->dropIndex('musicas_evento_event_id_index');
            $table->dropIndex('musicas_evento_event_id_ordem_index');
        });
    }

    /**
     * Verifica se um índice já existe
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $driverName = $connection->getDriverName();

        if ($driverName === 'sqlite') {
            $indexes = $connection->select(
                "SELECT name FROM sqlite_master WHERE type='index' AND name=? AND tbl_name=?",
                [$index, $table]
            );
            return count($indexes) > 0;
        }

        if ($driverName === 'pgsql') {
            // PostgreSQL usa pg_indexes
            $query = "SELECT COUNT(*) as count FROM pg_indexes 
                      WHERE schemaname = 'public' AND tablename = ? AND indexname = ?";
            $result = $connection->select($query, [$table, $index]);
            return $result[0]->count > 0;
        }

        // Para MySQL e outros bancos
        $database = $connection->getDatabaseName();
        $query = "SELECT COUNT(*) as count FROM information_schema.statistics 
                  WHERE table_schema = ? AND table_name = ? AND index_name = ?";
        
        $result = $connection->select($query, [$database, $table, $index]);
        return $result[0]->count > 0;
    }
};

