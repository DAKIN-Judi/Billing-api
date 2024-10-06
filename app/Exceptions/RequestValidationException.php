<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;


class RequestValidationException extends ValidationException
{
    public function render($request)
    {
        return response()->json([
            'status' => 'false',
            'message' => 'Invalid data provided',
            'errors' => $this->validator->errors(),
        ], Response::HTTP_BAD_REQUEST);
    }
}
