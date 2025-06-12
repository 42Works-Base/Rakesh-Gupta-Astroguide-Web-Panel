<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ScheduleCall;
use App\Models\Transaction;
use App\Models\GeneralContent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //

    public function index()
    {
        $userCount = User::where('role', 'user')->count();

        $totalAppointment = ScheduleCall::whereHas('transactions', function ($query) {
            $query->where('status', 'completed'); // or ->where('is_completed', true)
        })->count();

        $totalTodayAppointment = ScheduleCall::where('is_call_canceled','0')->where('is_call_completed', '0')
        ->whereHas('transactions', function ($query) {
            $query->where('status', 'completed'); // or ->where('is_completed', true)
        })
            ->whereHas('availableTime', function ($query) {
                $query->whereDate('date', now()->toDateString())
                    ->whereTime('start_time', '>=', now()->toTimeString());
            })
            ->count();

        $revenue = Transaction::where('status', 'completed')
            ->where(function ($query) {
                $query->where('refund_status', '!=', 'completed')
                    ->orWhereNull('refund_status');
            })
            ->sum('amount');

        $appointments = ScheduleCall::whereHas('user')
            ->where(function ($query) {
                $query->where('schedule_calls.is_call_canceled', '0')
                    ->where('schedule_calls.is_call_completed', '0');
            })
            ->select('schedule_calls.*')
            ->join('available_times', 'available_times.id', '=', 'schedule_calls.available_time_id')
            ->join('transactions', 'transactions.schedule_call_id', '=', 'schedule_calls.id')
            ->where('transactions.status', 'completed')
            // ->whereDate('available_times.date', '>=', now()->toDateString()) // Only upcoming appointments
            ->where(function ($query) {
                $query->whereDate('available_times.date', '>', now()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereDate('available_times.date', now()->toDateString())
                            ->whereTime('available_times.start_time', '>=', now()->toTimeString());
                    });
            })
            ->orderByDesc('available_times.date')
            ->limit(10)
            ->get();



        // dd($appointments[0]->availableTime);
        return view('dashboard.index', compact('userCount', 'totalAppointment', 'totalTodayAppointment', 'revenue', 'appointments'));
    }





    public function getChartData(Request $request)
    {
        $month = $request->input('month'); // Expected format: Y-m
        $transactionStatus = $request->input('transaction_status');
        // $refundStatus = $request->input('refund_status');

        if (!$month) {
            $month = now()->format('Y-m');
        }

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Define base query
        $query = DB::table('transactions')
            ->selectRaw("DATE(created_at) as date, status, COUNT(*) as total")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'failed']) // Only include completed and failed
            ->groupBy('date', 'status');

        if ($transactionStatus) {
            $query->where('status', $transactionStatus);
        }

        // if ($refundStatus) {
        //     $query->where('refund_status', $refundStatus);
        // }

        $results = $query->get();
        // Initialize structure
        // $statuses = ['initiated', 'processing', 'completed', 'failed'];
        $statuses = ['completed', 'failed'];
        $labels = range(1, $daysInMonth);
        $datasets = [];

        foreach ($statuses as $status) {
            $datasets[$status] = array_fill(0, $daysInMonth, 0);
        }

        // Fill data into datasets
        foreach ($results as $row) {
            $day = Carbon::parse($row->date)->day; // 1 to 31
            $datasets[$row->status][$day - 1] = $row->total;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets
        ]);
    }

    public function showGeneralRemediesForm()
    {
        $general_remedies = GeneralContent::where('sku', 'general-remedies')->first();

        return view('admin.general_content.remedies_update', compact('general_remedies'));
    }

    public function updateGeneralRemedies(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $data = [
            'name' => 'General Remedies',
            'sku' => 'general-remedies',
            'content' => $request->input('content'),
        ];

        GeneralContent::updateOrCreate(
            ['sku' => 'general-remedies'],
            $data
        );

        return redirect()->route('general-remedies.update')->with('success', 'General Remedies updated successfully.');
    }
}
