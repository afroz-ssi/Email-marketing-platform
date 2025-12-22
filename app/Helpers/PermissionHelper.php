<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Gate;

class PermissionHelper
{
    public static function abort403($permission)
    {
        if (Gate::denies($permission)) {
            \abort(403);
        }
    }
}
