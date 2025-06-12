<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;


class TransactionController extends BaseController
{

    // public function index(Request $request)
    // {
    //     $query = Transaction::with(['user', 'scheduleCall.availableTime']);

    //     $query->whereIn('status', ['completed', 'failed']);

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->filled('refund_status')) {
    //         $query->where('refund_status', $request->refund_status);
    //     }

    //     if ($request->filled('call_date')) {
    //         $query->whereHas('scheduleCall.availableTime', function ($q) use ($request) {
    //             $q->whereDate('date', $request->call_date);
    //         });
    //     }

    //     // if ($request->filled('search')) {
    //     //     $search = $request->search;
    //     //     $query->where(function ($q) use ($search) {
    //     //         $q->where('transaction_id', 'like', "%{$search}%");
    //     //     });
    //     // }

    //     if ($request->filled('search')) {
    //         $search = $request->search;

    //         $query->where(function ($q) use ($search) {
    //             $q->where('transaction_id', 'like', "%{$search}%")
    //                 ->orWhereHas('user', function ($userQuery) use ($search) {
    //                     $userQuery->where('first_name', 'like', "%{$search}%")
    //                         ->orWhere('last_name', 'like', "%{$search}%")
    //                         ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
    //                 });
    //         });
    //     }


    //     $transactions = $query->latest()->get();

    //     return view('admin.transaction.index', compact('transactions'));
    // }

    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'scheduleCall.availableTime'])
            ->whereIn('status', ['completed', 'failed'])
            // ->whereHas('user', function ($q) {
            //     $q->whereNull('deleted_at'); // Exclude soft-deleted users
            // })
            ;

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('refund_status')) {
            $query->where('refund_status', $request->refund_status);
        }

        if ($request->filled('call_date')) {
            $query->whereHas('scheduleCall.availableTime', function ($q) use ($request) {
                $q->whereDate('date', $request->call_date);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('payment_id', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->whereNull('deleted_at')
                            ->where(function ($q) use ($search) {
                                $q->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
                            });
                    });
            });
        }

        $transactions = $query->latest()->get();

        return view('admin.transaction.index', compact('transactions'));
    }
}
