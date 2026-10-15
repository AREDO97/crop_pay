<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Check if the application and its critical dependencies
     * are working correctly.
     */
    public function health()
    {
        try {

            // Try to connect to MySQL.
            DB::connection()->getPdo();

            return response()->json([
                'status' => 'ok',

                'checks' => [
                    'database' => 'ok',
                ],
            ], 200);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => 'unhealthy',

                'checks' => [
                    'database' => 'failed',
                ],
            ], 503);
        }
    }


    /**
     * Check if the Laravel application itself is alive.
     
     */
    public function live()
    {
        return response()->json([
            'status' => 'alive',
        ], 200);
    }
}
