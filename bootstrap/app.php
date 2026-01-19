<?php

use App\Facades\Logger;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\UnauthorizedException;
use Modules\Core\Exceptions\EmailNotVerifiedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (UnauthorizedException  $e, $request) {
            Logger::security('forbidden', 'An Unauthorized User Trying To Access', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access, You do not have the required permission',
                'status'  => 403,
            ], 403);
        });
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            Logger::security('throttle', 'Too Many Attempts!', ['input' => $request->except(['password'])]);
            return response()->json([
                'success' => false,
                'message' => 'Too many attempt, Please try again after a few minutes',
                'status'  => 429,
            ], 429);
        });
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            Logger::log('model-not-found', 'Model Not Found In The Request', ['message' => $e->getMessage()], 'model');
            return response()->json([
                'success' => false,
                'message' => 'Not Found',
            ], 404);
        });
        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            Logger::security('forbidden', 'An Unauthorized User Trying To Access', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access, You do not have the required permission',
                'status'  => 403,
            ], 403);
        });
        $exceptions->render(function (EmailNotVerifiedException $e, $request) {
            Logger::log('forbidden', 'User Trying To Access Without Verfying Email', [$e->getMessage()], 'unverified-email');
            return response()->json([
                'success' => false,
                'message' => 'Your email address is not verified',
                'status'  => 403,
            ], 403);
        });
        $exceptions->render(function (\Throwable $e, $request) {
            Logger::log(
                'server-error',
                'Unhandled Exception Occurred',
                [
                    'exception' => get_class($e),
                    'message'   => $e->getMessage(),
                    'url'       => $request->fullUrl(),
                ],
                'exception'
            );

            // API / JSON requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Internal Server Error',
                    'status'  => 500,
                    'debug'   => config('app.debug') ? [
                        'exception' => get_class($e),
                        'file'      => $e->getFile(),
                        'line'      => $e->getLine(),
                    ] : null,
                ], 500);
            }

            return response()->view('errors.500', [], 500);
        });
    })->create();

