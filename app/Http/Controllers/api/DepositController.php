<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\SavingGoal;
use App\Services\YoolaSmsService;
use App\Services\PesapalTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use NjoguAmos\Pesapal\Pesapal;
use NjoguAmos\Pesapal\DTOs\PesapalOrderData;
use NjoguAmos\Pesapal\DTOs\PesapalAddressData;
use NjoguAmos\Pesapal\Enums\ISOCurrencyCode;
use NjoguAmos\Pesapal\Enums\ISOCountryCode;
use NjoguAmos\Pesapal\Enums\RedirectMode;

class DepositController extends Controller
{
    // create a Pesapal deposit.
    public function create(Request $request, SavingGoal $goal, YoolaSmsService $sms, PesapalTokenService $tokenService)
    {
        $user = $request->user();

        // Validate the amount 
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'phone' => 'required'
        ]);

        // check ownership
        if ($goal->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        //  ENSURING A VALID TOKEN EXISTS BEFORE CALLING PESAPAL
        $tokenService->ensureValidToken();

        //  unique merchant reference.
        $reference = 'CP-' . strtoupper((string) Str::ulid());

        // Creating the deposit as PENDING.
        $deposit = Deposit::create([
            'user_id' => $user->id,
            'saving_goal_id' => $goal->id,
            'amount' => $request->amount,
            'reference' => $reference,
            'status' => 'pending',
            'payment_method' => 'pesapal',
        ]);

        // send an sms
        $sms->send(
            $request->phone,
            'Your depositing money into crop pay, enter pin to approve'
        );

        // Get the IPN ID registered with Pesapal.
        $ipnId = config('services.pesapal.ipn_id');

        // Build the Pesapal order.
        $orderData = new PesapalOrderData(
            id: $deposit->reference,
            currency: ISOCurrencyCode::UGX,
            amount: (float) $deposit->amount,
            description: 'Saving deposit',
            callbackUrl: route('pesapal.callback'),
            notificationId: $ipnId,
            cancellationUrl: route('pesapal.cancel'),
            redirectMode: RedirectMode::TOP_WINDOW,
        );

        // Pesapal billing information.
        $billingAddress = new PesapalAddressData(
            emailAddress: $user->email,
            countryCode: ISOCountryCode::UG,
            firstName: $user->name,
        );

        // Submit the order to Pesapal.
        $order = Pesapal::createOrder(
            orderData: $orderData,
            billingAddress: $billingAddress,
        );

        // If Pesapal rejected the request
        if (!is_array($order)) {
            return response()->json([
                'message' => 'Unable to start Pesapal payment.',
            ], 502);
        }

        // Save the Pesapal transaction ID.
        $deposit->update([
            'pesapal_tracking_id' => $order['order_tracking_id'],
        ]);

        // Send the payment URL 
        return response()->json([
            'message' => 'Deposit created. Complete the payment.',
            'deposit' => $deposit,
            'payment_url' => $order['redirect_url'],
        ], 201);
    }

    // deposits history
    public function index(Request $request)
    {
        // current user
        $user = $request->user();
        // user deposits
        $deposits = $user->deposits;
        // response
        return response()->json($deposits);
    }
    // all deposits
    public function deposits(Request $request)
    {
        $admin = $request->user();

    if ($admin->role !== 'admin' && $admin->role !== 'super_admin') {
        abort(403, 'Unauthorised action');
    }
    // all deposits
    $deposits = Deposit::latest()->paginate(10);
    // response
    return response()->json($deposits); 
    }
}