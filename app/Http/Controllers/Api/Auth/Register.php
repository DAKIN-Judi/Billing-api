<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\RequestValidationException;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ConfirmMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    /**
     *  @OA\Post(
     *      path="/api/auth/register",
     *      tags={"Auth"},
     *      summary="User Registration",
     *      operationId="register",
     *
     *     @OA\RequestBody(
     *       required=true,
     *       description="User register",
     *       @OA\JsonContent(
     *          required={"first_name", "last_name", "password","email","address", "role_id","rgpd_confirmation"},
     *          @OA\Property(property="first_name", type="string", example="Evelyne "),
     *          @OA\Property(property="last_name", type="string", example="KOUYE"),
     *          @OA\Property(property="password", type="string"),
     *          @OA\Property(property="email", type="string", example="joedo@gmail.com"),
     *          @OA\Property(property="referral_code", type="string", example="JDNCEOMK"),
     *          @OA\Property(property="address", type="string"),
     *          @OA\Property(property="role_id", type="integer", example=1),
     *          @OA\Property(property="rgpd_confirmation", type="boolean"),
     *          @OA\Property(property="long", type="double"),
     *          @OA\Property(property="lat", type="double"),
     *          @OA\Property(property="driver_licence", type="file"),
     *          @OA\Property(property="insurance", type="file"),
     *          @OA\Property(property="cni", type="file"),
     *          @OA\Property(property="driver_licence_number", type="string"),
     *          @OA\Property(property="licence_origin", type="file"),
     *          @OA\Property(property="courses", type="integer"),
     *          @OA\Property(property="gains", type="integer"),
     *          @OA\Property(property="phone_number", type="string"),
     *          @OA\Property(property="phone_prefix", type="string"),
     *      )
     *    ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Account created successfully",
     *          @OA\JsonContent()
     *      ),
     *
     *      @OA\Response(
     *          response=500,
     *          description="Error occurred while saving user",
     *          @OA\JsonContent()
     *      )
     *  )
     */
    public function register(Request $request)
    {
        $data  = $this->registerValidation($request);

        if (User::where('email', strtolower($request->email))->exists()) {
            return sendError('Email indisponible', '', 401);
        }

        $user = User::create($data);

        $this->sendConfirmationMail($user);

        return sendResponse(['user' => $user], 'User created successfully');
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
     *     summary="Confirms a user's email",
     *     description="Validates the confirmation code and confirms the user's email if valid.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"confirm_code"},
     *             @OA\Property(property="confirm_code", type="string", example="123456", description="The phone confirmation code"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email confirmed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Numéro de tel confirmé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid confirmation code",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="error"),
     *             @OA\Property(property="message", type="string", example="Code de confirmation invalide")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="error"),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(property="errors", type="object", example={"confirm_code": "The confirm code is required."})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
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
     *     security={ {"bearer": {} }},
     *     summary="Resend confirmation code",
     *     operationId="resendConfirmationCode",
     *     @OA\Response(
     *         response=200,
     *         description="Verification code sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Un code de vérification vous a été envoyé par mail"
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         ),
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

     function validateEmail($request) {
         $validator = Validator::make($request->all(), [
             'email' =>'required|email',
         ]);

         if ($validator->fails()) {
             throw new RequestValidationException($validator->errors());
         }

         return $validator->validated();
     }
}
