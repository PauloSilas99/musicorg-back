<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    /**
     * Boot do trait para adicionar global scope de tenant
     */
    protected static function bootBelongsToTenant(): void
    {
        // Adiciona global scope para filtrar automaticamente por tenant
        static::addGlobalScope('tenant', function (Builder $builder): void {
            $tenantId = static::getCurrentTenantId();
            if ($tenantId) {
                $builder->where(static::getTenantColumn(), $tenantId);
            }
        });
    }

    /**
     * Retorna o ID do tenant atual autenticado
     */
    protected static function getCurrentTenantId(): ?int
    {
        if (Auth::guard('bandas')->check()) {
            return Auth::guard('bandas')->id();
        }

        return null;
    }

    /**
     * Retorna o nome da coluna de tenant (band_id)
     */
    protected static function getTenantColumn(): string
    {
        return 'band_id';
    }

    /**
     * Scope para garantir que um modelo pertence ao tenant atual
     */
    public function scopeBelongsToTenant(Builder $query, ?int $tenantId = null): Builder
    {
        $tenantId = $tenantId ?? static::getCurrentTenantId();

        if (!$tenantId) {
            // Se não há tenant autenticado, retorna query vazia (segurança)
            return $query->whereRaw('1 = 0');
        }

        return $query->where(static::getTenantColumn(), $tenantId);
    }

    /**
     * Verifica se o modelo pertence ao tenant atual
     */
    public function belongsToCurrentTenant(?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? static::getCurrentTenantId();

        if (!$tenantId) {
            return false;
        }

        return $this->{static::getTenantColumn()} === $tenantId;
    }

    /**
     * Abort se o modelo não pertencer ao tenant atual
     */
    public function ensureBelongsToTenant(?int $tenantId = null): void
    {
        if (!$this->belongsToCurrentTenant($tenantId)) {
            abort(403, 'Você não tem permissão para acessar este recurso.');
        }
    }
}

