<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Http\Resources\AuthResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //Inscrition d'un nouvel utilisateur
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        // Toujours développeur à l'inscription : seul un admin peut promouvoir (PATCH /users/{id}/role)
        $data['role'] = 'developer';

        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription reussie',
            'user'    => new AuthResource($user),
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Connexion d'un utilisateur existant
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Auth::attempt vérifie email + password automatiquement
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect',
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Supprime les anciens tokens pour éviter l'accumulation
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion reussie',
            'user'    => new AuthResource($user),
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Déconnexion — révoque le token actuel
     */
    public function logout(Request $request): JsonResponse
    {
        // Supprime uniquement le token utilisé pour cette requête
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion reussie',
        ], 200);
    }

    /**
     * Retourne l'utilisateur connecté
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new AuthResource($request->user()),
        ], 200);
    }
}
