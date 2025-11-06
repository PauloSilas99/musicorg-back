<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class EventoController extends BaseController
{
    /**
     * Listar todos os eventos da banda logada
     * 
     * Query Parameters:
     * - with: Relacionamentos para carregar (ex: ?with=musicos,musicas)
     * - page: Número da página (para paginação)
     * - per_page: Itens por página (padrão: 15)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Evento::query();

        // ⚡ PERFORMANCE: Carregar relacionamentos apenas se solicitado
        $with = $request->query('with');
        if ($with) {
            $relations = array_filter(explode(',', $with));
            $allowedRelations = ['musicos', 'musicas'];
            $relations = array_intersect($relations, $allowedRelations);
            
            if (!empty($relations)) {
                $query->with($relations);
            }
        }

        // ⚡ PERFORMANCE: Ordenação otimizada usando índice composto
        $query->orderBy('data', 'desc')
              ->orderBy('hora', 'desc');

        // ⚡ PERFORMANCE: Paginação (opcional, mas recomendado)
        $perPage = min((int) $request->query('per_page', 15), 100); // Máximo 100 por página
        
        if ($request->has('page')) {
            $eventos = $query->paginate($perPage);
            
            return response()->json([
                'eventos' => $eventos->items(),
                'pagination' => [
                    'current_page' => $eventos->currentPage(),
                    'last_page' => $eventos->lastPage(),
                    'per_page' => $eventos->perPage(),
                    'total' => $eventos->total(),
                ],
            ]);
        }

        // Se não usar paginação, retorna todos (mas sem relacionamentos por padrão)
        $eventos = $query->get();

        return response()->json([
            'eventos' => $eventos,
        ]);
    }

    /**
     * Criar um novo evento (band_id será preenchido automaticamente)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'data' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'local' => 'nullable|string|max:255',
        ]);

        $banda = $request->user();

        $evento = Evento::create([
            'band_id' => $banda->id,
            'titulo' => $validated['titulo'],
            'data' => $validated['data'],
            'hora' => $validated['hora'],
            'local' => $validated['local'] ?? null,
        ]);

        // ⚡ PERFORMANCE: Carregar relacionamentos apenas se necessário
        $evento->load(['musicos', 'musicas']);

        return response()->json([
            'message' => 'Evento criado com sucesso',
            'evento' => $evento,
        ], 201);
    }

    /**
     * Exibir um evento específico
     */
    public function show(string $id): JsonResponse
    {
        $evento = Evento::with(['musicos', 'musicas'])
            ->findOrFail($id);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        return response()->json([
            'evento' => $evento,
        ]);
    }

    /**
     * Atualizar um evento
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $evento = Evento::findOrFail($id);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $validated = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'data' => 'sometimes|required|date',
            'hora' => 'sometimes|required|date_format:H:i',
            'local' => 'nullable|string|max:255',
        ]);

        $evento->update($validated);

        // ⚡ PERFORMANCE: Carregar relacionamentos apenas se necessário
        $evento->load(['musicos', 'musicas']);

        return response()->json([
            'message' => 'Evento atualizado com sucesso',
            'evento' => $evento,
        ]);
    }

    /**
     * Excluir um evento
     */
    public function destroy(string $id): JsonResponse
    {
        $evento = Evento::findOrFail($id);

        // 🔒 VALIDAÇÃO CRÍTICA: Garantir que o evento pertence ao tenant atual
        $this->ensureEventoBelongsToTenant($evento);

        $evento->delete();

        return response()->json([
            'message' => 'Evento excluído com sucesso',
        ]);
    }
}


