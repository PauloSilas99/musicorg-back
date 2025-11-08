<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\MusicaEvento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MusicaEventoController extends BaseController
{
    /**
     * Listar todas as músicas de um evento (Setlist ordenado)
     */
    public function index(string $eventoId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);
        
        $musicas = $evento->musicas()
            ->orderBy('ordem')
            ->get();

        return response()->json([
            'musicas' => $musicas,
        ]);
    }

    /**
     * Adicionar uma música a um evento
     */
    public function store(Request $request, string $eventoId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $validated = $request->validate([
            'titulo_musica' => 'required|string|max:255',
            'artista_ou_tom' => 'nullable|string|max:100',
            'ordem' => 'nullable|integer|min:0',
            'link_musica' => 'nullable|url|max:2048',
        ]);

        $musica = MusicaEvento::create([
            'event_id' => $evento->id,
            'titulo_musica' => $validated['titulo_musica'],
            'artista_ou_tom' => $validated['artista_ou_tom'] ?? null,
            'ordem' => $validated['ordem'] ?? 0,
            'link_musica' => $validated['link_musica'] ?? null,
        ]);

        return response()->json([
            'message' => 'Música adicionada com sucesso',
            'musica' => $musica,
        ], 201);
    }

    /**
     * Exibir uma música específica
     */
    public function show(string $eventoId, string $musicaId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $musica = $evento->musicas()->findOrFail($musicaId);

        return response()->json([
            'musica' => $musica,
        ]);
    }

    /**
     * Atualizar uma música
     */
    public function update(Request $request, string $eventoId, string $musicaId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $musica = $evento->musicas()->findOrFail($musicaId);

        $validated = $request->validate([
            'titulo_musica' => 'sometimes|required|string|max:255',
            'artista_ou_tom' => 'nullable|string|max:100',
            'ordem' => 'sometimes|integer|min:0',
            'link_musica' => 'sometimes|nullable|url|max:2048',
        ]);

        $musica->update($validated);

        return response()->json([
            'message' => 'Música atualizada com sucesso',
            'musica' => $musica,
        ]);
    }

    /**
     * Remover uma música de um evento
     */
    public function destroy(string $eventoId, string $musicaId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $musica = $evento->musicas()->findOrFail($musicaId);
        
        $musica->delete();

        return response()->json([
            'message' => 'Música removida com sucesso',
        ]);
    }

    /**
     * Reordenar as músicas do setlist
     */
    public function reorder(Request $request, string $eventoId): JsonResponse
    {
        $evento = Evento::findOrFail($eventoId);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $validated = $request->validate([
            'musicas' => 'required|array',
            'musicas.*.id' => 'required|exists:musicas_evento,id',
            'musicas.*.ordem' => 'required|integer|min:0',
        ]);

        foreach ($validated['musicas'] as $musicaData) {
            MusicaEvento::where('id', $musicaData['id'])
                ->where('event_id', $evento->id)
                ->update(['ordem' => $musicaData['ordem']]);
        }

        $musicas = $evento->musicas()
            ->orderBy('ordem')
            ->get();

        return response()->json([
            'message' => 'Setlist reordenado com sucesso',
            'musicas' => $musicas,
        ]);
    }
}


