<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Exceptions\RequestValidationException;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\ConfirmMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(name="Auth", description="Authentication APIs")
 */
class RegisterController extends Controller
{


    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="Register a new user and send a confirmation email with a confirmation code.",
     *     operationId="registerUser",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Email unavailable",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email indisponible")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $data  = $this->registerValidation($request);

        $user = User::create($data);

        $this->sendConfirmationMail($user);

        return sendResponse(new UserResource($user), 'User created successfully');
    }

    function sendConfirmationMail($user)
    {
        do {
            $code = rand(1111, 9999);
        } while (User::where('confirm_code', $code)->exists());

        Mail::to($user->email)->send(new ConfirmMail($code));

        $user->confirm_code = $code;
        $user->save();
    }

    protected function registerValidation($request)
    {
        $request->merge([
            'email' => strtolower($request->input('email')),
        ]);

        return customValidation($request, [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'email' => 'required|email|max:255|unique:users,email',
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/confirm-email",
     *     tags={"Auth"},
     *     summary="Confirm user email",
     *     description="Confirm the user's email using the confirmation code.",
     *     operationId="confirmEmail",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"confirm_code"},
     *             @OA\Property(property="confirm_code", type="integer", example=1234)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Email confirmed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Numéro de tel confirmé avec succès")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Invalid confirmation code",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Code de confirmation invalide")
     *         )
     *     )
     * )
     */
    function confirmEmail(Request $request)
    {
        $data = customValidation($request, ['confirm_code' => 'required|numeric']);

        $user = User::where('confirm_code', $data['confirm_code'])->first();

        if (!$user) {
            return sendError('Code de confirmation invalide', null, 401);
        } else {
            $user->update(['confirm_code' => null, 'phone_confirmed_at' => now()]);
            return sendResponse(null, 'Numéro de tel confirmé avec succès');
        }
    }


    /**
     * @OA\Post(
     *     path="/api/auth/resend-confirmation-code",
     *     tags={"Auth"},
     *     summary="Resend confirmation code",
     *     description="Resend a new confirmation code to the user's email.",
     *     operationId="resendConfirmationCode",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="A verification code has been sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Un code de vérification vous a été envoyé par mail")
     *         )
     *     )
     * )
     */

    function resendConfirmationCode(Request $request)
    {
        $email = $this->validateEmail($request)['email'];
        $user = User::where('email', $email);

        do {
            $code = rand(1111, 9999);
        } while (User::where('confirm_code', $code)->exists());

        Mail::to($email)->send(new ConfirmMail($code));

        $user->confirm_code = $code;
        $user->save();

        return sendResponse(null, 'Un code de vérification vous a été envoyé par mail');
    }

    function validateEmail($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            throw new RequestValidationException($validator->errors());
        }

        return $validator->validated();
    }
}
