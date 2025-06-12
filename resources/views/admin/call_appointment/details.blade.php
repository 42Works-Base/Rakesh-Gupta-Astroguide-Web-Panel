@extends('layouts.app')

@section('content')
<div class="container">

    <!-- User Details -->
    <div class="card shadow-lg p-4 mb-5">
        <h2 class="mb-4 text-primary">User Details</h2>

        <div class="row">
            <div class="col-md-4 text-center">
                <img src="{{ @$callAppointment->user->profile_picture ? url(@$callAppointment->user->profile_picture) : asset('default-profile.png') }}"
                    alt="Profile Picture"
                    class="img-fluid rounded-circle"
                    style="width: 150px; height: 150px;">
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ ucfirst(@$callAppointment->user->first_name) }} {{ ucfirst(@$callAppointment->user->last_name) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ @$callAppointment->user->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ @$callAppointment->user->country_code }} {{ @$callAppointment->user->phone }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date of Birth:</strong></td>
                        <td>
                            {{ \Carbon\Carbon::parse($callAppointment->user->dob)->format('d-m-Y') ?? 'N/A' }}
                            ({{ $callAppointment->user->dob_time ?? 'N/A' }})
                        </td>

                    </tr>
                    <tr>
                        <td><strong>Gender:</strong></td>
                        <td>{{ ucfirst(@$callAppointment->user->gender) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Birth City:</strong></td>
                        <td>{{ @$callAppointment->user->birth_city }}</td>
                    </tr>
                    <tr>
                        <td><strong>Birth Country:</strong></td>
                        <td>{{ @$callAppointment->user->birthplace_country }}</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td>{{ @$callAppointment->user->full_address }}</td>
                    </tr>
                    <!-- <tr>
                        <td><strong>Location:</strong></td>
                        <td>Lat: {{ @$callAppointment->user->latitude }}, Long: {{ @$callAppointment->user->longitude }}</td>
                    </tr> -->
                    <!-- <tr>
                        <td><strong>Timezone:</strong></td>
                        <td>{{ @$callAppointment->user->timezone }}</td>
                    </tr> -->
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge {{ @$callAppointment->user->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst(@$callAppointment->user->status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-lg p-4 mb-5">
        <h2 class="mb-4 text-primary">Kundali Details</h2>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Gana:</strong></td>
                        <td>{{ @$callAppointment->kundaliDetail->gana }}</td>
                    </tr>
                    <tr>
                        <td><strong>Yoni:</strong></td>
                        <td>{{ @$callAppointment->kundaliDetail->yoni }}</td>
                    </tr>
                    <tr>
                        <td><strong>Vasya:</strong></td>
                        <td>{{ @$callAppointment->kundaliDetail->vasya }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nadi:</strong></td>
                        <td>{{ @$callAppointment->kundaliDetail->nadi }}</td>
                    </tr>
                    <tr>
                        <td><strong>Varna:</strong></td>
                        <td>{{ @$callAppointment->kundaliDetail->varna }}</td>
                    </tr>
                    <tr>
                        <td><strong>Ascendant Sign:</strong></td>
                        <td>{{ @$callAppointment->kundaliDetail->ascendant_sign }}</td>
                    </tr>
                    <tr>
                        <td><strong>Ascendant Nakshatra:</strong></td>
                        <td>{{ @$callAppointment->kundaliDetail->ascendant_nakshatra }}</td>
                    </tr>
                    <tr>
                        <td><strong>Rasi:</strong></td>
                        <td>{{ @$callAppointment->kundaliDetail->rasi }}</td>
                    </tr>
                    <tr>
                        <td><strong>Sun Sign:</strong></td>
                        <td>{{ @$callAppointment->kundaliDetail->sun_sign }}</td>
                    </tr>
                    <tr>
                        <td><strong>Yoga:</strong></td>
                        <td>{{ @$callAppointment->kundaliDetail->yoga }}</td>
                    </tr>
                </table>

            </div>
        </div>
    </div>

    <!-- Call Appointment & Transactions -->

    <div class="card shadow-lg p-4">
        <h2 class="mb-4 text-primary">Call Appointment Details</h2>

        @php
        use App\Helpers\CallHelper;

        $call = $callAppointment;
        $whatsappLink = CallHelper::getWhatsAppLink($call);
        $isEnabled = CallHelper::isCallLinkEnabled($call->id);
        $canCanceld = CallHelper::canReschedule($call->id);
        @endphp

        <table class="table table-bordered mb-4">
            <tr>
                <td><strong>Call Date:</strong></td>
                <td>{{ \Carbon\Carbon::parse($call->availableTime->date)->format('d-m-Y') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Start Time:</strong></td>
                <td>{{ $call->availableTime ? \Carbon\Carbon::parse($call->availableTime->start_time)->format('h:i A') : 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>End Time:</strong></td>
                <td>{{ $call->availableTime ? \Carbon\Carbon::parse($call->availableTime->end_time)->format('h:i A') : 'N/A' }}</td>
            </tr>

            <!-- WhatsApp Link -->
            <tr>
                <td><strong>WhatsApp Link:</strong></td>
                <td>
                    @if($whatsappLink)
                    @if($isEnabled)
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <a href="{{ $whatsappLink }}" target="_blank" class="btn btn-sm btn-success">Open WhatsApp</a>

                        <input type="text" value="{{ $whatsappLink }}" id="whatsappLinkCopy" readonly class="form-control form-control-sm w-auto" style="max-width: 300px;">
                        <button onclick="copyToClipboard('whatsappLinkCopy')" class="btn btn-sm btn-outline-primary">Copy Link</button>
                    </div>
                    @else
                    <button class="btn btn-sm btn-secondary" onclick="alert('WhatsApp link is only available 5 minutes before the call and up to 10 minutes after the call ends.')">WhatsApp (Disabled)</button>
                    @endif
                    @else
                    N/A
                    @endif
                </td>
            </tr>


            <!-- Statuses -->
            <!-- <tr>
                <td><strong>Is Link Used:</strong></td>
                <td>{{ $call->is_link_used ? 'Yes' : 'No' }}</td>
            </tr> -->
            <!-- <tr>
                <td><strong>Call Completed:</strong></td>
                <td>
                    <span class="badge {{ $call->is_call_completed ? 'badge-success' : 'badge-warning' }}">
                        {{ $call->is_call_completed ? 'Completed' : 'Not Completed' }}
                    </span>
                </td>
            </tr> -->

            <tr>
                <td><strong>Call Cancelled:</strong></td>
                <td>
                    <span class="badge {{ $call->is_call_canceled ? 'badge-danger' : 'badge-primary' }}">
                        {{ $call->is_call_canceled ? 'Cancelled' : 'Not Cancelled' }}
                    </span>
                </td>

            </tr>

            <!-- Agenda -->
            <tr>
                <td><strong>Agenda:</strong></td>
                <td>{{ $call->agenda ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>View User Chart:</strong></td>
                <td>
                    <a href="{{ route('appointment-management.user.chart', $call->id) }}" class="btn btn-sm btn-warning" target="_blank">View Chart</a>
                </td>
            </tr>

            <tr>
                <td><strong>User Predictions:</strong></td>
                <td>
                    <a href="{{ route('appointment-management.user.predictions', $call->id) }}" class="btn btn-sm btn-warning" target="_blank">View Predictions</a>
                </td>
            </tr>
        </table>

        {{--<h4 class="text-info">Transactions</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Status</th>
                    <th>Refund Status</th>
                    <th>Amount</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($call->transactions as $txn)
                <tr>
                    <td>{{ $txn->transaction_id }}</td>
        <td>{{ ucfirst($txn->status) }}</td>
        <td>{{ ucfirst($txn->refund_status ?? 'N/A') }}</td>
        <td>₹{{ number_format($txn->amount, 2) }}</td>
        <td>{{ $txn->created_at ? $txn->created_at->format('d M Y h:i A') : 'N/A' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center text-muted">No transactions found.</td>
        </tr>
        @endforelse
        </tbody>
        </table>--}}

        <div class="mt-4">
            <a href="{{ route('appointment-management.index') }}" class="btn btn-secondary">Back to Appointments</a>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    //with https
    // function copyToClipboard(id) {
    //     var copyText = document.getElementById(id);
    //     copyText.select();
    //     copyText.setSelectionRange(0, 99999); // For mobile devices
    //     navigator.clipboard.writeText(copyText.value);
    //     alert("Copied the link: " + copyText.value);
    // }

    function copyToClipboard(id) {
        var copyText = document.getElementById(id);

        if (!copyText) {
            alert("Element not found!");
            return;
        }

        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        try {
            document.execCommand("copy");
            alert("Copied the link: " + copyText.value);
        } catch (err) {
            console.error("Copy failed:", err);
            alert("Copy not supported.");
        }
    }
</script>
@endsection