<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function daily()
    {
        $today = Carbon::today();
        $dailyTotal = Transaction::where('user_id', auth()->id())
            ->whereDate('transaction_date', $today)
            ->sum('amount_transaction');

        return response()->json([
            'total' => $dailyTotal,
            'date' => $today->format('Y-m-d')
        ]);
    }

    public function weekly()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $weeklyTotal = Transaction::where('user_id', auth()->id())
            ->whereBetween('transaction_date', [$startOfWeek, $endOfWeek])
            ->sum('amount_transaction');

        return response()->json([
            'total' => $weeklyTotal,
            'start_date' => $startOfWeek->format('Y-m-d'),
            'end_date' => $endOfWeek->format('Y-m-d')
        ]);
    }

    public function monthly()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $monthlyTotal = Transaction::where('user_id', auth()->id())
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount_transaction');

        return response()->json([
            'total' => $monthlyTotal,
            'month' => $startOfMonth->format('Y-m'),
            'start_date' => $startOfMonth->format('Y-m-d'),
            'end_date' => $endOfMonth->format('Y-m-d')
        ]);
    }

    public function yearly()
    {
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        $yearlyTotal = Transaction::where('user_id', auth()->id())
            ->whereBetween('transaction_date', [$startOfYear, $endOfYear])
            ->sum('amount_transaction');

        return response()->json([
            'total' => $yearlyTotal,
            'year' => $startOfYear->format('Y'),
            'start_date' => $startOfYear->format('Y-m-d'),
            'end_date' => $endOfYear->format('Y-m-d')
        ]);
    }

    public function chartDaily()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $dailyData = [];
        $labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $total = Transaction::where('user_id', auth()->id())
                ->whereDate('transaction_date', $date)
                ->sum('amount_transaction');
            $dailyData[] = $total;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $dailyData
        ]);
    }

    public function chartWeekly()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $weeklyData = [];
        $labels = [];

        $currentWeek = $startOfMonth->copy()->startOfWeek();
        $weekNumber = 1;

        while ($currentWeek->lte($endOfMonth)) {
            $weekEnd = $currentWeek->copy()->endOfWeek();
            if ($weekEnd->gt($endOfMonth)) {
                $weekEnd = $endOfMonth->copy();
            }

            $total = Transaction::where('user_id', auth()->id())
                ->whereBetween('transaction_date', [$currentWeek, $weekEnd])
                ->sum('amount_transaction');

            $weeklyData[] = $total;
            $labels[] = "Minggu {$weekNumber}";

            $currentWeek->addWeek();
            $weekNumber++;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $weeklyData
        ]);
    }

    public function chartMonthly()
    {
        $startOfYear = Carbon::now()->startOfYear();
        $monthlyData = [];
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($i = 0; $i < 12; $i++) {
            $monthStart = $startOfYear->copy()->addMonths($i);
            $monthEnd = $monthStart->copy()->endOfMonth();

            $total = Transaction::where('user_id', auth()->id())
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount_transaction');
            $monthlyData[] = $total;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $monthlyData
        ]);
    }

    public function chartYearly()
    {
        $currentYear = Carbon::now()->year;
        $yearlyData = [];
        $labels = [];

        for ($i = 4; $i >= 0; $i--) {
            $year = $currentYear - $i;
            $yearStart = Carbon::createFromDate($year, 1, 1);
            $yearEnd = Carbon::createFromDate($year, 12, 31);

            $total = Transaction::where('user_id', auth()->id())
                ->whereBetween('transaction_date', [$yearStart, $yearEnd])
                ->sum('amount_transaction');

            $yearlyData[] = $total;
            $labels[] = (string) $year;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $yearlyData
        ]);
    }

    public function categoryAnalytics()
    {
        try {
            $categorySpending = Transaction::where('user_id', auth()->id())
                ->with('categoryTransaction')
                ->get()
                ->groupBy('category_transaction_id')
                ->map(function ($transactions) {
                    $firstTransaction = $transactions->first();
                    return [
                        'name_category' => $firstTransaction->categoryTransaction->name_category ?? 'Unknown',
                        'total_amount' => $transactions->sum('amount_transaction'),
                        'transaction_count' => $transactions->count()
                    ];
                })
                ->sortByDesc('total_amount')
                ->take(5)
                ->values();

            return response()->json([
                'categories' => $categorySpending
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch category analytics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
