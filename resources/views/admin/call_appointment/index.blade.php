@extends('layouts.app')
@section('title', 'Appointment Listing - AstroGuide')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">Appointment Listing</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Appointment List</h6>
        </div>

        <div class="card-body">

            <!-- Filter Form start-->
            <form method="GET" action="{{ route('appointment-management.index') }}" class="mb-3">
                <div class="row align-items-end gx-2">
                    <div class="col-auto">
                        <label for="call_date" class="small">Date</label>
                        <input type="date" name="call_date" value="{{ request('call_date') }}" class="form-control form-control-sm" onchange="this.form.submit()">
                    </div>

                    @if(request('call_date'))
                    <div class="col-auto">
                        <label class="d-block small invisible">Reset</label>
                        <a href="{{ route('appointment-management.index') }}" class="btn btn-sm btn-secondary">
                            Reset
                        </a>
                    </div>
                    @endif
                </div>
            </form>
            <!-- Filter Form end-->

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>User</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Call Type</th>
                            <th>WhatsApp Link</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($callAppointments as $call)
                        @php
                        $whatsappLink = App\Helpers\CallHelper::getWhatsAppLink($call);
                        $isEnabled = App\Helpers\CallHelper::isCallLinkEnabled($call->id);
                        $canCanceld = App\Helpers\CallHelper::canReschedule($call->id);
                        $transaction = $call->transactionCompleted;
                        @endphp
                        <tr>
                            <td>{{ $call->user->first_name ?? 'Deleted User' }}</td>
                            <td>{{ optional($call->availableTime)->date ? \Carbon\Carbon::parse($call->availableTime->date)->format('d-m-Y') : 'N/A'}}</td>
                            <td>{{ optional($call->availableTime)->start_time ? \Carbon\Carbon::parse($call->availableTime->start_time)->format('h:i A') : 'N/A' }}</td>
                            <td>{{ optional($call->availableTime)->end_time ? \Carbon\Carbon::parse($call->availableTime->end_time)->format('h:i A') : 'N/A' }}</td>
                            <td>{{ ucfirst($call->call_type) }}</td>
                            <td>
                                @if($whatsappLink)
                                @if($call->is_call_canceled)
                                <button class="btn btn-sm btn-secondary" onclick="alert('WhatsApp link is only available for non-cancelled calls.')">WhatsApp (Disabled)</button>
                                @elseif($isEnabled)
                                <a href="{{ $whatsappLink }}" target="_blank" class="btn btn-sm btn-success">WhatsApp</a>
                                @else
                                <button class="btn btn-sm btn-secondary" onclick="alert('WhatsApp link is only available 5 minutes before the call and up to 10 minutes after the call ends.')">WhatsApp (Disabled)</button>
                                @endif
                                @else
                                N/A
                                @endif
                            </td>
                            <td>
                                {{-- View action start--}}
                                <a href="{{ route('appointment-management.details', $call->id) }}" target="_blank" class="mx-1" title="View Details" data-toggle="tooltip">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                                {{-- View action end--}}

                                {{-- Call completed action start--}}

                                @php $isCompleted = $call->is_call_completed; @endphp
                                <a href="javascript:void(0);" class="update-call-status mx-1" data-id="{{ $call->id }}" data-status="{{ $isCompleted ? 1 : 0 }}" title="{{ $isCompleted ? 'Call Completed' : 'Mark Call as Completed' }}" data-toggle="tooltip">
                                    <i class="fas fa-check {{ $isCompleted ? 'text-success' : 'text-muted' }}"></i>
                                </a>
                                {{-- Call completed action end--}}

                                @if ($call->is_call_canceled)
                                <i class="fas fa-times text-danger mx-1" title="Call Cancelled" data-toggle="tooltip"></i>
                                @elseif ($canCanceld)
                                <a href="javascript:void(0);" class="mark-call-cancel mx-1" data-id="{{ $call->id }}" data-status="0" title="Cancel Call" data-toggle="tooltip">
                                    <i class="fas fa-times text-danger"></i>
                                </a>
                                @else
                                <i class="fas fa-times text-muted mx-1" title="Calls can only be canceled up to one hour before the start time." data-toggle="tooltip"></i>
                                @endif


                                {{-- Refund action --}}
                                @if($call->is_call_canceled)
                                @if($transaction && $transaction->status == 'completed' && ($transaction->refund_status == 'failed' || $transaction->refund_status == null) && $transaction->payment_id != null)
                                <a href="javascript:void(0);" class="refund-call mx-1" data-id="{{ $call->id }}" data-toggle="tooltip" title="Refund Amount : {{number_format($transaction->amount,0)}}">
                                    <i class="fas fa-undo text-warning"></i>
                                </a>
                                @else
                                <i class="fas fa-undo text-muted mx-1" title="{{ $transaction ? 'Refund '. ucfirst($transaction->refund_status).' : '. number_format($transaction->amount,0) : 'No Transaction' }}" data-toggle="tooltip"></i>
                                @endif
                                @endif

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No appointment found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- /.container-fluid -->
<script>
    $(document).ready(function() {
        let table = $('#dataTable').DataTable({
            "ordering": false // Disables ordering
        });
    });
</script>
<script>
    // Call Completed Toggle
    $(document).on("click", ".update-call-status", function() {
        var button = $(this);
        var callId = button.data("id");
        var currentStatus = button.data("status");
        var newStatus = currentStatus == 1 ? 0 : 1;
        var icon = button.find("i");

        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to " + (newStatus == 1 ? "mark this call as Completed?" : "mark this call as Not Completed?"),
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, update it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/appointment-management/update-appointment-status') }}/" + callId,
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        is_call_completed: newStatus
                    },
                    success: function(response) {
                        Swal.fire("Updated!", response.message, "success");

                        // Update icon color and tooltip
                        button.data("status", newStatus);
                        icon.removeClass("text-success text-muted");

                        if (newStatus === 1) {
                            icon.addClass("text-success");
                            button.attr("title", "Call Completed");
                        } else {
                            icon.addClass("text-muted");
                            button.attr("title", "Mark Call as Completed");
                        }

                        // Fix tooltip refresh
                        button.tooltip('dispose').tooltip();
                    },
                    error: function() {
                        Swal.fire("Error!", "Something went wrong.", "error");
                    }
                });
            }
        });
    });

    // Call Cancel Action
    $(document).on("click", ".mark-call-cancel", function() {
        var button = $(this);
        var callId = button.data("id");
        var currentStatus = button.data("status");
        var newStatus = currentStatus == 1 ? 0 : 1;
        var icon = button.find("i");

        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to cancel this call?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, cancel it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/appointment-management/mark-call-cancel') }}/" + callId,
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        is_call_canceled: newStatus
                    },
                    success: function(response) {
                        Swal.fire("Cancelled!", response.message, "success").then(()=>{
                            location.reload();
                        });

                        // Replace cancel icon with static icon (canceled)
                       // button.replaceWith('<i class="fas fa-times text-danger mx-1" title="Call Cancelled" data-toggle="tooltip"></i>');

                        // Re-init tooltip for newly added icon
                       // $('[data-toggle="tooltip"]').tooltip();
                    },
                    error: function() {
                        Swal.fire("Error!", "Something went wrong.", "error");
                    }
                });
            }
        });
    });

    $(document).on("click", ".refund-call", function() {
        let callId = $(this).data("id");
        Swal.fire({
            title: "Are you sure?",
            text: "Would you like to refund the amount?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, refund it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/appointment-management/refund') }}/" + callId,
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        Swal.fire("Success", response.message, "success").then(() => location.reload());
                    },
                    error: function(err) {
                        // Swal.fire("Error", "Something went wrong.", "error");
                        Swal.fire("Error", "Something went wrong.", "error");
                    }
                });
            }
        });
    });


    // Initialize all tooltips on page load
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

@endsection