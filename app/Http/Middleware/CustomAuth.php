<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class CustomAuth extends Middleware
{
    /**
     * Overwrite the method you want to customize
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->is('admin/*')) {
            return $request->expectsJson() ? null : route('admin.login');
        }
    }
}
