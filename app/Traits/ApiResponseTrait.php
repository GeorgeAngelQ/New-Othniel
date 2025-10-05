<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse($data = null, string $message = 'OK', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'error' => null
        ], $code);
    }

    protected function errorResponse(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
            'error' => $errors
        ], $code);
    }
}
