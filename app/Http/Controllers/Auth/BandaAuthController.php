<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Banda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class BandaAuthController extends Controller
{
    /**
     * Registrar uma nova banda
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:bandas,email',
            'password' => 'required|string|min:8|confirmed',
            'plan' => 'nullable|string|in:gratuito,pro',
        ]);

        $banda = Banda::create([
            'nome' => $validated['nome'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'plan' => $validated['plan'] ?? 'gratuito',
        ]);

        $token = $banda->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Banda registrada com sucesso',
            'banda' => $banda,
            'token' => $token,
        ], 201);
    }

    /**
     * Login da banda
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $banda = Banda::where('email', $validated['email'])->first();

        if (!$banda || !Hash::check($validated['password'], $banda->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $token = $banda->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'banda' => $banda,
            'token' => $token,
        ]);
    }

    /**
     * Logout da banda
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ]);
    }

    /**
     * Obter a banda logada
     */
    public function me(Request $request)
    {
        $banda = $request->user();

        return response()->json([
            'banda' => $banda,
        ]);
    }
}


