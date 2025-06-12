@extends('layouts.app')
@section('title', 'Transaction Listing - AstroGuide')
@section('css')
<style>
    /*.dataTables_length {
        display: none;
    }*/

    .dataTables_length label {
        font-size: 0;
        /* Hides text */
    }

    .dataTables_length label select {
        font-size: 14px;
        border: 1px solid #f7c626;
        /* Restore font size for dropdown */
    }



    .dataTables_filter {
        display: none;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">Transaction Listing</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transactions</h6>
        </div>
        <div class="card-body">
            <!-- Filter Form start-->
            <form method="GET" class="mb-3">
                <div class="row align-items-end gx-2">
                    <!-- Show Entries -->
                    <!-- <div class="col-auto">
                        <label for="entries" class="small">Show</label>
                        <select name="entries" id="entries" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('entries') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('entries') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('entries') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div> -->

                    <div class="col-auto">
                        <label for="entries" class="small">Show</label>
                        <div id="dataTableLengthContainer" style="margin-bottom: -8px;"></div>
                    </div>

                    <!-- Status -->
                    <div class=" col-auto">
                        <label for="status" class="small">Status</label>
                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach(['completed', 'failed'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Refund Status -->
                    <!-- <div class="col-auto">
                        <label for="refund_status" class="small">Refund</label>
                        <select name="refund_status" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach(['initiated', 'processing', 'completed', 'failed'] as $refund)
                            <option value="{{ $refund }}" {{ request('refund_status') == $refund ? 'selected' : '' }}>
                                {{ ucfirst($refund) }}
                            </option>
                            @endforeach
                        </select>
                    </div> -->

                    <!-- Call Date -->
                    <div class="col-auto">
                        <label for="call_date" class="small">Date</label>
                        <input type="date" name="call_date" value="{{ request('call_date') }}" class="form-control form-control-sm" onchange="this.form.submit()">
                    </div>

                    <!-- Search -->
                    <div class="col-auto ml-auto">
                        <label for="search" class="small">Search:</label>
                        <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" onchange="this.form.submit()">
                    </div>

                    <!-- Reset Button -->
                    <div class="col-auto">
                        <label class="d-block small invisible">Reset</label>
                        <a href="{{ route(\Illuminate\Support\Facades\Route::currentRouteName()) }}" class="btn btn-sm btn-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Filter Form end-->


            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>User</th>
                            <th>Schedule Call</th>
                            <!-- <th>Transaction ID</th> -->
                            <th>Ref Number</th>
                            <th>Razorpay Id</th>
                            <!-- <th>Refund Status</th> -->
                            <th>Amount</th>
                            <th>Created At</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $txn)
                        <tr>
                            {{-- User --}}
                            <td>
                                @if($txn->user)
                                <a href="{{ route('user.details', $txn->user->id) }}">
                                    {{ $txn->user->first_name }} {{ $txn->user->last_name }}
                                </a>
                                @else
                                Deleted User
                                @endif
                            </td>

                            {{-- Schedule Call --}}
                            <td>
                                @if($txn->scheduleCall && $txn->scheduleCall->availableTime)
                                <span
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="
                                                Start: {{ \Carbon\Carbon::parse($txn->scheduleCall->availableTime->start_time)->format('h:i A') }},
                                                End: {{ \Carbon\Carbon::parse($txn->scheduleCall->availableTime->end_time)->format('h:i A') }},
                                                Status: {{ $txn->scheduleCall->is_call_completed ? 'Completed' : 'Pending' }}
                                            ">
                                    {{ \Carbon\Carbon::parse($txn->scheduleCall->availableTime->date)->format('d-m-Y') }}
                                </span>
                                @else
                                N/A
                                @endif
                            </td>

                            {{-- Transaction ID --}}
                            <td>{{ $txn->transaction_id }}</td>
                            <td>{{ $txn->payment_id }}</td>


                            {{-- Refund Status --}}
                            {{--<td>
                                @php
                                $refundColor = match($txn->refund_status) {
                                'refunded' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                null => 'secondary',
                                default => 'secondary',
                                };
                                @endphp
                                <span class="badge badge-{{ $refundColor }}">{{ ucfirst($txn->refund_status ?? 'N/A') }}</span>
                            </td>--}}


                            {{-- Amount --}}
                            <td>₹{{ number_format($txn->amount, 2) }}</td>

                            <td>{{ $txn->created_at ? $txn->created_at->format('d M Y h:i A') : 'N/A' }}</td>
                            {{-- Status --}}
                            <td>
                                @php
                                $statusColor = match($txn->status) {
                                'completed' => 'success',
                                'failed' => 'danger',
                                default => 'secondary',
                                };
                                @endphp
                                <span class="badge badge-{{ $statusColor }}">{{ ucfirst($txn->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No transactions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Tooltip Activation --}}
<script>
    $(document).ready(function() {
        let table = $('#dataTable').DataTable({
            "ordering": false
        });

        // Move DataTable controls into custom containers
        $('#dataTable_filter').appendTo('#dataTableSearchContainer');
        $('#dataTable_length').appendTo('#dataTableLengthContainer'); // optional if needed
    });


    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection