<?php

use App\Http\Middleware\CustomAuth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUser;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::redirect('/admin', 'admin/login');
            Route::redirect('/', 'admin/login');
            Route::middleware(['web', HandleInertiaRequests::class])
                ->prefix('admin')
                ->name('admin.')
            ->group(base_path('routes/admin.php'));
    },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
        $middleware->alias([
            'isAdmin' => IsAdmin::class,
            'isUser' => IsUser::class,
            'customAuth' => CustomAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
        // $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
        //     if (in_array($response->status(), [403])) {
        //         session()->flash('error', 'Sorry! you are not allowed to do this.');
        //         return back();
        //     }
        //     if (app()->environment(['local', 'testing']) && in_array($response->status(), [500, 503, 404, 403])) {
        //         return Inertia::render('Error', ['status' => $response->status()])
        //             ->toResponse($request)
        //             ->setStatusCode($response->status());
        //     } elseif ($response->status() === 419) {
        //         return back()->with(['error' => 'The page expired, please try again.']);
        //     }

        //     return $response;
        // });
    })->create();
