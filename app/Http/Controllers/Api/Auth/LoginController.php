<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="User login",
     *     operationId="login",
     *     @OA\RequestBody(
     *       required=true,
     *       description="User login",
     *       @OA\JsonContent(
     *          required={"email", "password"},
     *          @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *          @OA\Property(property="password", type="string", format="password", example="userUSER1234@")
     *       )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="access_token", type="string", example="your_jwt_token"),
     *              @OA\Property(property="token_type", type="string", example="bearer"),
     *              @OA\Property(property="expires_in", type="integer", example=3600)
     *          )
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="Invalid email or password",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Invalid email or password")
     *          )
     *      ),
     * )
     */

    public function login(Request $request)
    {
        $token = auth()->attempt($this->validateLoginInfo($request));


        if (!$token) {
            return sendError('Invalid email or password', null, 401);
        }

        return sendResponse($this->respondWithToken($token), 'Login successful');
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }

    protected function validateLoginInfo($request)
    {
        $request->merge([
            'email' => strtolower($request->input('email')),
        ]);

        return customValidation($request, [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6'
        ]);
    }
}
