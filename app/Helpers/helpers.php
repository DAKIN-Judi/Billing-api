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

if (!function_exists('calculateTax')) {

    function calculateTax($amount, $taxRate = 18) {
        return $amount * ($taxRate / 100);
    }
}


if (!function_exists('determineTax')) {

    function determineTax($invoice, $taxRate = 18) {
        $totalAmount = $invoice->products->sum('quantity * unit_price');
        $tax = calculateTax($totalAmount, $taxRate);

        $invoice->update(['tax' => $tax]);
    }
}
