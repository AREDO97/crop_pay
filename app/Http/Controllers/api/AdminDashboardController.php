<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\ProductSale;
use App\Models\SavingGoal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Get aggregate statistics for the admin dashboard.
     */
    public function stats(Request $request)
    {
        // 1. Authorization check
        $admin = $request->user();
        if (!$admin || !in_array($admin->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        // Date markers
        $now = Carbon::now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $endOfThisMonth   = $now->copy()->endOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth   = $now->copy()->subMonth()->endOfMonth();

      // user stats
        $totalUsers      = User::count();
        $usersThisMonth  = User::whereBetween('created_at', [$startOfThisMonth, $endOfThisMonth])->count();
        $usersLastMonth  = User::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $activeUsers     = User::where('is_active', true)->count();

       // deposits
        $completedDepositsQuery = Deposit::where('status', 'completed');

        $totalDepositsCount       = Deposit::count();
        $completedDepositsAmount  = (float) (clone $completedDepositsQuery)->sum('amount');
        
        $depositsAmountThisMonth  = (float) (clone $completedDepositsQuery)
            ->whereBetween('created_at', [$startOfThisMonth, $endOfThisMonth])
            ->sum('amount');

        $depositsAmountLastMonth  = (float) (clone $completedDepositsQuery)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('amount');

        $averageDepositAmount     = (float) (clone $completedDepositsQuery)->avg('amount') ?? 0;

        $depositsByStatus = Deposit::select('status', DB::raw('count(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('status')
            ->get();

        // saving goals
        $totalSavingGoals      = SavingGoal::count();
        $savingGoalsThisMonth  = SavingGoal::whereBetween('created_at', [$startOfThisMonth, $endOfThisMonth])->count();
        $totalTargetAmount     = (float) SavingGoal::sum('target_amount');
        $averageTargetAmount   = (float) SavingGoal::avg('target_amount') ?? 0;

        // product sales
        $totalProductSales      = ProductSale::count();
        $productSalesThisMonth  = ProductSale::whereBetween('created_at', [$startOfThisMonth, $endOfThisMonth])->count();
        $totalExpectedRevenue   = (float) ProductSale::sum('expected_total_amount');

        // response
        return response()->json([
            'users' => [
                'total'        => $totalUsers,
                'active'       => $activeUsers,
                'this_month'   => $usersThisMonth,
                'last_month'   => $usersLastMonth,
            ],
            'deposits' => [
                'total_records'          => $totalDepositsCount,
                'total_completed_amount' => round($completedDepositsAmount, 2),
                'amount_this_month'      => round($depositsAmountThisMonth, 2),
                'amount_last_month'      => round($depositsAmountLastMonth, 2),
                'average_amount'         => round($averageDepositAmount, 2),
                'breakdown_by_status'    => $depositsByStatus,
            ],
            'saving_goals' => [
                'total'               => $totalSavingGoals,
                'created_this_month'  => $savingGoalsThisMonth,
                'total_target_amount' => round($totalTargetAmount, 2),
                'average_target'      => round($averageTargetAmount, 2),
            ],
            'product_sales' => [
                'total_sales_placed'     => $totalProductSales,
                'sales_this_month'       => $productSalesThisMonth,
                'total_expected_revenue' => round($totalExpectedRevenue, 2),
            ],
        ]);
    }
}