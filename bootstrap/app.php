<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    //Gerer les providers et les exceptions dans le même fichier de bootstrap pour centraliser la configuration
    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
    ])
    
    ->withExceptions(function (Exceptions $exceptions) {

        // Toutes les erreurs de validation retournent du JSON
        $exceptions->render(function (
            \Illuminate\Validation\ValidationException $e,
            $request
        ) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Données invalides',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Les erreurs 401 non authentifié
        $exceptions->render(function (
            \Illuminate\Auth\AuthenticationException $e,
            $request
        ) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Non authentifié — token manquant ou invalide',
                ], 401);
            }
        });
    })->create();

    