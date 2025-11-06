<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\MusicoEvento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MusicoEventoController extends BaseController
{
    /**
     * Listar todos os músicos de um evento
     */
    public function index(string $eventoId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);
        
        $musicos = $evento->musicos;

        return response()->json([
            'musicos' => $musicos,
        ]);
    }

    /**
     * Adicionar um músico a um evento
     */
    public function store(Request $request, string $eventoId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $validated = $request->validate([
            'nome_musico' => 'required|string|max:100',
            'funcao' => 'required|string|max:100',
        ]);

        $musico = MusicoEvento::create([
            'event_id' => $evento->id,
            'nome_musico' => $validated['nome_musico'],
            'funcao' => $validated['funcao'],
        ]);

        return response()->json([
            'message' => 'Músico adicionado com sucesso',
            'musico' => $musico,
        ], 201);
    }

    /**
     * Exibir um músico específico
     */
    public function show(string $eventoId, string $musicoId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $musico = $evento->musicos()->findOrFail($musicoId);

        return response()->json([
            'musico' => $musico,
        ]);
    }

    /**
     * Atualizar um músico
     */
    public function update(Request $request, string $eventoId, string $musicoId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $musico = $evento->musicos()->findOrFail($musicoId);

        $validated = $request->validate([
            'nome_musico' => 'sometimes|required|string|max:100',
            'funcao' => 'sometimes|required|string|max:100',
        ]);

        $musico->update($validated);

        return response()->json([
            'message' => 'Músico atualizado com sucesso',
            'musico' => $musico,
        ]);
    }

    /**
     * Remover um músico de um evento
     */
    public function destroy(string $eventoId, string $musicoId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $musico = $evento->musicos()->findOrFail($musicoId);
        
        $musico->delete();

        return response()->json([
            'message' => 'Músico removido com sucesso',
        ]);
    }
}


