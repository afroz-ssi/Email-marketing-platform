<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => [
                    'id' => Auth::id() ?? null,
                    'full_name' => Auth::user()->full_name ?? null,
                    'profile_photo_url' => Auth::user()->profile_photo_url ?? null,
                ]
            ],

            'previousUrl' => url()->previous(),
            'baseUrl' => url(),
            'currentUrl' => $request->path(),
            'isLogin' => Auth::user() && Auth::user()->role_name == 'USER' ? true : false,
            'appName' => config('app.name'),
            'roles' => $request->user() ? $request->user()->roles->pluck('name') : [],
            'permissions' => $request->user() ? $request->user()->getPermissionsViaRoles()->pluck('name') : [],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'info' => fn() => $request->session()->get('info'),
                'warning' => fn() => $request->session()->get('warning'),
            ],
            'props_data' => fn() => session('props_data')
        ]);
    }
}
