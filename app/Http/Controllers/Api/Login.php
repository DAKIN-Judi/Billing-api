<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\RequestValidationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="User login",
     *     operationId="login",
     *
     *     @OA\RequestBody(
     *       required=true,
     *       description="User login",
     *       @OA\JsonContent(
     *          required={"email", "password"},
     *          @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *          @OA\Property(property="password", type="string", format="password", example="userUSER1234@")
     *      )
     *    ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Invalid email or password",
     *          @OA\JsonContent()
     *      ),
     *
     *
     * )
     */


    public function login(Request $request)
    {
        $token = Auth::attempt($this->validateLoginInfo($request));

        if (!$token) {
            return sendError('Invalid email or password', null, 401);
        }

        return sendResponse($this->respondWithToken($token));
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
