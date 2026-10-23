<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\PesapalTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use NjoguAmos\Pesapal\Pesapal;

class PesapalController extends Controller
{
    // Pesapal redirects the user's browser here after payment.
    public function callback(Request $request, PesapalTokenService $tokenService)
    {
        $trackingId = $request->query('OrderTrackingId');

        if (!$trackingId) {
            return response()->json([
                'message' => 'Missing Pesapal tracking ID.'
            ], 400);
        }

        return $this->processPayment($trackingId, $tokenService);
    }

    // track transaction status change
    public function ipn(Request $request, PesapalTokenService $tokenService)
    {
        $trackingId = $request->query('OrderTrackingId');

        if (!$trackingId) {
            return response()->json([
                'message' => 'Missing Pesapal tracking ID.'
            ], 400);
        }

        return $this->processPayment($trackingId, $tokenService);
    }

    // obtain real transaction status
    private function processPayment(string $trackingId, PesapalTokenService $tokenService)
    {
        // 1. ENSURE A VALID TOKEN EXISTS BEFORE QUERYING STATUS
        $tokenService->ensureValidToken();

        // Ask Pesapal for the transaction status.
        $transaction = Pesapal::getTransactionStatus(
            orderTrackingId: $trackingId
        );

        if (!is_array($transaction)) {
            return response()->json([
                'message' => 'Unable to verify payment with Pesapal.'
            ], 502);
        }

        // Find deposit using Pesapal's tracking ID.
        $deposit = Deposit::where(
            'pesapal_tracking_id',
            $trackingId
        )->first();

        if (!$deposit) {
            return response()->json([
                'message' => 'Deposit not found.'
            ], 404);
        }

        // verify amount
        if ((float) $transaction['amount'] !== (float) $deposit->amount) {
            return response()->json([
                'message' => 'Payment amount does not match deposit.'
            ], 422);
        }

        // payment status
        $status = $transaction['payment_status_description'] ?? null;

        if ($status === 'Completed') {

            DB::transaction(function () use ($deposit) {

                // enforce idempotency
                $deposit = Deposit::where('id', $deposit->id)
                    ->lockForUpdate()
                    ->first();

                if ($deposit->status === 'completed') {
                    return;
                }

                // Mark the payment as completed.
                $deposit->update([
                    'status' => 'completed',
                    'payment_method' => 'pesapal',
                ]);

                // NOW the money is considered received.
                $deposit->savingGoal()->increment(
                    'current_amount',
                    $deposit->amount
                );
            });
        }

        return response()->json([
            'message' => 'Payment status processed.',
            'status' => $status,
        ]);
    }

    // cancel payment
    public function cancel()
    {
        return response()->json([
            'message' => 'Payment was cancelled.'
        ]);
    }
}