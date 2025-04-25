<?php

namespace App\Helpers;

trait ApiResponse
{
    protected function responseSuccess($data = [], $message = 'Successful', $code = 200)
    {
        $response = [
            'timestamp' => now(),
            'status' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $code);
    }

    protected function responseError($message, $code = 400)
    {
        $response = [
            'timestamp' => now(),
            'status' => false,
            'code' => $code,
            'error' => $message,
        ];

        return response()->json($response, $code);
    }
}
