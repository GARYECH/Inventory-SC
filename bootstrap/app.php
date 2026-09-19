<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(
        function (
            Middleware $middleware
        ): void {

            $middleware->alias([
                'admin' =>
                    AdminMiddleware::class,
            ]);
        }
    )
    ->withExceptions(
    function (
        Exceptions $exceptions
    ): void {
        $exceptions->render(
            function (
                \Symfony\Component\HttpKernel\Exception\RequestEntityTooLargeHttpException $e,
                \Illuminate\Http\Request $request
            ) {
                if (
                    $request->expectsJson()
                ) {
                    return response()->json(
                        [
                            'message' =>
                                'Request Entity Too Large.',
                        ],
                        413
                    );
                }

                return response()->view(
                    'errors.413',
                    [],
                    413
                );
            }
        );
    }

    )
    ->create();