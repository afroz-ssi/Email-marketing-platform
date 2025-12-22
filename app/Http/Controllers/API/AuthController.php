<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/login",
     * operationId="authLogin",
     * tags={"Auth Management"},
     * summary="User Login",
     * description="Login User Here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="password", type="password")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Login Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function login(Request $request)
    {

        // Log::debug("login credential ".print_r($request->all(),true));

        $validator = Validator::make($request->all(), [
            "email" =>  "required|email",
            "password" =>  "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all()[0]);
        }

        try {

            $user = User::where("email", $request->email)->first();

            if (is_null($user)) {
                return $this->sendError('Failed! email not found', [], 400);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user       =       User::find(Auth::id());
                $token      =       $user->createToken('token')->plainTextToken;
                $userData = [
                    'user_id' => $user->id,
                    'user_name' => $user->full_name,
                    'token' => $token
                ];

                $response = [
                    'success' => true,
                    'data'    => $user,
                    'token'    => $token,
                    'message' => 'Login successfully.',
                ];
                return response()->json($response, 201);
            } else {
                return $this->sendError("Whoops! invalid password", [], 400);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }
}
