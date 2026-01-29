<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait ApiResponse
{
    /**
     * Build a standardized successful JSON response (Buy2Buy format).
     */
    public function successResponse(
        mixed $data = null,
        string $message = 'Successfully',
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    /**
     * Build authentication success response with token details.
     */
    public function authSuccessResponse(
        string $accessToken,
        mixed $user,
        string $tokenType = 'Bearer',
        ?string $expiresAt = null,
        string $message = 'Successfully',
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $accessToken,
                'user' => $user,
                'token_type' => $tokenType,
                'expires_at' => $expiresAt,
            ],
            'message' => $message,
        ], $status);
    }

    /**
     * Build a standardized error JSON response (Buy2Buy format).
     */
    public function errorResponse(
        string $message,
        ?array $errors = null,
        int $status = 400,
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
