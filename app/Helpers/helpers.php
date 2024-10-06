<?php

use App\Exceptions\RequestValidationException;
use Illuminate\Support\Facades\Validator;

if (!function_exists('sendResponse')) {
    /**
     * Send a formatted JSON response.
     *
     * @param mixed $result
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */


    function sendResponse($result, $message = null, $code = 200)
    {

        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!empty($result) || $result != null) {
            $response['data'] = $result;
        }

        return response()->json($response, 200);
    }
}


if (!function_exists('sendError')) {
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    function sendError($error, $errorMessages = [], $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}


if (!function_exists('customValidation')) {

    function customValidation($request, $rules) {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new RequestValidationException($validator);
        }

        return $validator->validated();
    }
}
