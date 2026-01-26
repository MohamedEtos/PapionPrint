<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Str;

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
        $exceptions->reportable(function (\Throwable $e) {
            if (app()->bound('request')) {
                $uniqueId = request()->cookie('unique_id') ?? Str::uuid()->toString();
                
                 try {
                    \App\Models\ErrorLog::create([
                        'user_id' => auth()->id(),
                        'username' => auth()->user() ? auth()->user()->name : 'Guest',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'request_url' => request()->fullUrl(),
                        'request_method' => request()->method(),
                        'message' => $e->getMessage(),
                        'stack_trace' => $e->getTraceAsString(),
                    ]);
                } catch (\Throwable $loggingException) {
                    // Fail silently if logging fails to avoid infinite loops
                }
            }
        });
    })->create();
