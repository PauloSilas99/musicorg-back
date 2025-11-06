<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class BaseController extends Controller
{
    /**
     * Retorna o tenant (banda) atual autenticado
     */
    protected function getCurrentTenant()
    {
        return Auth::guard('bandas')->user();
    }

    /**
     * Retorna o ID do tenant atual
     */
    protected function getCurrentTenantId(): ?int
    {
        $tenant = $this->getCurrentTenant();
        return $tenant?->id;
    }

    /**
     * Valida se um recurso pertence ao tenant atual
     * 
     * @param mixed $resource O recurso a ser validado (deve ter método belongsToCurrentTenant ou atributo band_id)
     * @param int|null $tenantId ID do tenant (opcional, usa o atual por padrão)
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function ensureResourceBelongsToTenant($resource, ?int $tenantId = null): void
    {
        $tenantId = $tenantId ?? $this->getCurrentTenantId();

        if (!$tenantId) {
            abort(403, 'Você precisa estar autenticado para acessar este recurso.');
        }

        // Se o recurso tem o método belongsToCurrentTenant, usa ele
        if (method_exists($resource, 'belongsToCurrentTenant')) {
            if (!$resource->belongsToCurrentTenant($tenantId)) {
                abort(403, 'Você não tem permissão para acessar este recurso.');
            }
            return;
        }

        // Caso contrário, verifica diretamente o band_id
        $resourceTenantId = $resource->band_id ?? $resource->event_id 
            ? ($resource->evento?->band_id ?? null)
            : null;

        if (!$resourceTenantId || $resourceTenantId !== $tenantId) {
            abort(403, 'Você não tem permissão para acessar este recurso.');
        }
    }

    /**
     * Valida se um evento pertence ao tenant atual
     */
    protected function ensureEventoBelongsToTenant($evento, ?int $tenantId = null): void
    {
        $tenantId = $tenantId ?? $this->getCurrentTenantId();

        if (!$tenantId) {
            abort(403, 'Você precisa estar autenticado para acessar este recurso.');
        }

        if ($evento->band_id !== $tenantId) {
            abort(403, 'Você não tem permissão para acessar este evento.');
        }
    }
}

