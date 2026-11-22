<?php

namespace App\Exceptions;

class ApiErrorResponse
{
    public static function make(array $data)
    {
        $isDebug = config('app.debug');

        $payload = [
            'success' => false,
            'status' => $data['status'],
            'error_code' => $data['error_code'],
            'message' => $data['message'] ?? self::resolveMessage($data['message_key'] ?? null),
        ];

        if (isset($data['fields'])){
            $payload['fields'] = $data['fields'];
        }

        if (!empty($data['records'])){
            $payload['records'] = $data['records'];
        }

        if (!empty($data['context'])){
            $payload['context'] = $data['context'];
        }

        if(!empty($data['headers'])){
            $payload['headers'] = $data['headers'];
        }

        if ($isDebug && !empty($data['_debug'])){
            $payload['debug'] = $data['_debug'];
        }

        return response()->json($payload, $data['status']);
    }

    private static function resolveMessage(?string $key): string{
        if (!$key){
            return __('general.internal_error');
        }

        $translated = __($key);

        return $translated !== $key ? $translated : $key;
    }
}
