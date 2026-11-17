<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Changer le rôle d'un utilisateur, ex: php artisan user:role admin@taskflow.com admin
// Utile pour créer le premier admin, puisque l'inscription crée toujours un développeur
Artisan::command('user:role {email} {role}', function (string $email, string $role) {
    if (!in_array($role, ['developer', 'manager', 'admin'], true)) {
        $this->error('Le rôle doit être: developer, manager ou admin.');
        return 1;
    }

    $user = User::where('email', $email)->first();
    if (!$user) {
        $this->error("Aucun utilisateur avec l'email {$email}.");
        return 1;
    }

    $user->update(['role' => $role]);
    $this->info("{$user->name} est maintenant {$role}.");
})->purpose("Changer le rôle d'un utilisateur");
