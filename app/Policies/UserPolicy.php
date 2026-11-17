<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * ADMIN → voit tous les utilisateurs et gère les rôles
     * MANAGER → voit les utilisateurs (pour composer ses équipes)
     * DEVELOPER → aucun accès à la liste des utilisateurs
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Seul un admin peut changer un rôle, et pas le sien (évite de perdre le dernier accès admin)
     */
    public function updateRole(User $user, User $target): bool
    {
        return $user->isAdmin() && $user->id !== $target->id;
    }
}
