<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Liste de tous les utilisateurs (admin et manager uniquement)
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $request->validate([
            'role' => 'nullable|in:developer,manager,admin',
        ]);

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->get();

        return response()->json([
            'message' => 'Utilisateurs récupérés avec succès',
            'data'    => UserResource::collection($users),
        ], 200);
    }

    /**
     * Changer le rôle d'un utilisateur (admin uniquement)
     */
    public function updateRole(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé',
            ], 404);
        }

        $this->authorize('updateRole', $user);

        $data = $request->validate([
            'role' => 'required|in:developer,manager,admin',
        ], [
            'role.required' => 'Le rôle est obligatoire.',
            'role.in'       => 'Le rôle doit être: developer, manager ou admin.',
        ]);

        $user->update(['role' => $data['role']]);

        return response()->json([
            'message' => 'Rôle mis à jour avec succès',
            'data'    => new UserResource($user),
        ], 200);
    }
}
