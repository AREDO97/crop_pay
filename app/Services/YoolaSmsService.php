<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class YoolaSmsService
{
    public function send(string $phone, string $message): array
    {
        $response = Http::timeout(30)->post(
            config('services.yoola.url'),
            [
                'api_key' => config('services.yoola.key'),
                'phone' => $phone,
                'message' => $message,
                'sender' => config('services.yoola.sender'),
            ]
        );

        $response->throw();

        $result = $response->json();

        if (($result['status'] ?? null) !== 'success') {
            throw new RuntimeException(
                'Yoola SMS failed: ' .
                ($result['message'] ?? 'Unknown provider response')
            );
        }

        return $result;
    }
}
