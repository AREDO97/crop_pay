<?php

namespace App\Services;

use NjoguAmos\Pesapal\Models\PesapalToken;
use NjoguAmos\Pesapal\Pesapal;
use Carbon\Carbon;

class PesapalTokenService
{
    /**
     * Ensure a valid, non-expired Pesapal token exists in the database.
     * If missing or expiring within 30 seconds, fetch a fresh one on-demand.
     */
    public function ensureValidToken(): void
    {
        $latestToken = PesapalToken::latest()->first();

        // Check if token is missing or expires in less than 30 seconds
        if (!$latestToken || Carbon::now()->gte(Carbon::parse($latestToken->expires_at)->subSeconds(30))) {
            // Automatically fetches from Pesapal and saves to the pesapal_tokens table
            Pesapal::createToken();
        }
    }
}