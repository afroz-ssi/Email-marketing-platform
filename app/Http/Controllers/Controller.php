<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title=APP_NAME,
 *      description=L5_SWAGGER_CONST_HOST,
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function sendServerError($e)
    {
        return response()->json(["status" => false, "message" => "Something went wrong. Please try again",'error'=>$e->getMessage().' File: '.$e->getFile().' Line:'.$e->getLine(), "data" => []]);
        
    }
}
