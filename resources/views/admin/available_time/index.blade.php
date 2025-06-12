@extends('layouts.app')
@section('title', 'Slot Listing - AstroGuide')
@section('content')
<style>
    @media (max-width: 576px) {
        .input-group {
            flex-direction: column;
        }

        .input-group-append {
            margin-top: 5px;
        }

        .btn {
            width: 100%;
        }
    }
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">Slot Listing</h1>
        <a href="{{ route('slot-management.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fa-sm text-white-50"></i> Add Slot
        </a>
    </div>

    <!-- Date Search Form -->
    <form method="GET" action="{{ route('slot-management.index') }}" class="mb-3">
        <div class="row justify-content-center">
            <div class="col-md-3 col-sm-6">
                <div class="input-group">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}" onchange="this.form.submit()">
                    @if(request('date'))
                    <div class="input-group-append">
                        <a href="{{ route('slot-management.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </form>


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Slot List</h6>
        </div>
        {{--<div class="card-body">
            <div class="accordion" id="slotAccordion">
                @forelse ($availableTimes as $date => $slots)

                <div class="card mb-2">
                    <div class="card-header" id="heading-{{ str_replace('-', '', $date) }}">
        <h5 class="mb-0">
            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                data-target="#collapse-{{ str_replace('-', '', $date) }}"
                aria-expanded="false" aria-controls="collapse-{{ str_replace('-', '', $date) }}">
                {{ \Carbon\Carbon::parse($date)->format('d M Y') }} ({{ count($slots) }} Slots)
            </button>
        </h5>
    </div>

    <div id="collapse-{{ str_replace('-', '', $date) }}" class="collapse"
        aria-labelledby="heading-{{ str_replace('-', '', $date) }}" data-parent="#slotAccordion">
        <div class="card-body">
            <table class="table table-bordered slot-table">
                <thead>
                    <tr>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Price (₹)</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($slots as $slot)
                    <tr>
                        <td class="{{ $slot->isBooked() ? 'text-danger' : 'text-success' }}">
                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                        </td>
                        <td class="{{ $slot->isBooked() ? 'text-danger' : 'text-success' }}">
                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                        </td>
                        <td>₹{{ number_format($slot->price, 2) }}</td>
                        <td>
                            <button class="btn btn-sm toggle-availability {{ $slot->is_available == 'yes' ? 'btn-success' : 'btn-danger' }}"
                                data-id="{{ $slot->id }}"
                                data-status="{{ $slot->is_available == 'yes' ? 'no' : 'yes' }}">
                                {{ $slot->is_available == 'yes' ? 'Make Unavailable' : 'Make Available' }}
                            </button>
                        </td>
                        <td>
                            <a href="{{ route('slot-management.edit', $slot->id) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>
                            <button class="btn btn-sm btn-danger delete-slot"
                                data-id="{{ $slot->id }}"
                                data-is-booked="{{ $slot->isBooked() ? 'yes' : 'no' }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@empty
<p class="text-center text-muted">No slots found for this date.</p>
@endforelse
</div>
</div>--}}

<div class="card-body">
    <div class="accordion" id="slotAccordion">
        @forelse ($availableTimes as $date => $slots)
        @php
        $totalSlots = count($slots);
        $bookedSlots = collect($slots)->filter(fn($slot) => $slot->isBooked())->count();
        $freeSlots = $totalSlots - $bookedSlots;
        @endphp
        <div class="card mb-2">
            <div class="card-header d-flex justify-content-between align-items-center" id="heading-{{ str_replace('-', '', $date) }}">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                    data-target="#collapse-{{ str_replace('-', '', $date) }}"
                    aria-expanded="false" aria-controls="collapse-{{ str_replace('-', '', $date) }}">
                    {{ \Carbon\Carbon::parse($date)->format('d M Y') }} ({{ $totalSlots }} Slots)
                </button>
                <div>
                    <span class="badge badge-primary">Total: {{ $totalSlots }}</span>
                    <span class="badge badge-success">Free: {{ $freeSlots }}</span>
                    <span class="badge badge-danger">Booked: {{ $bookedSlots }}</span>
                </div>
            </div>

            <div id="collapse-{{ str_replace('-', '', $date) }}" class="collapse"
                aria-labelledby="heading-{{ str_replace('-', '', $date) }}" data-parent="#slotAccordion">
                <div class="card-body">
                    <table class="table table-bordered slot-table">
                        <thead>
                            <tr>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Price (₹)</th>
                                <th>Available</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slots as $slot)
                            <tr>
                                <td class="{{ $slot->isBooked() ? 'text-danger' : 'text-success' }}">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                </td>
                                <td class="{{ $slot->isBooked() ? 'text-danger' : 'text-success' }}">
                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                </td>
                                <td>₹{{ number_format($slot->price, 2) }}</td>
                                <td>
                                    <button class="btn btn-sm toggle-availability {{ $slot->is_available == 'yes' ? 'btn-success' : 'btn-danger' }}"
                                        data-id="{{ $slot->id }}"
                                        data-status="{{ $slot->is_available == 'yes' ? 'no' : 'yes' }}">
                                        {{ $slot->is_available == 'yes' ? 'Make Unavailable' : 'Make Available' }}
                                    </button>
                                </td>
                                <td>
                                    {{--<a href="{{ route('slot-management.edit', $slot->id) }}" class="btn btn-sm btn-warning">
                                    Edit
                                    </a>--}}
                                    <button class="btn btn-sm btn-danger delete-slot"
                                        data-id="{{ $slot->id }}"
                                        data-is-booked="{{ $slot->isBooked() ? 'yes' : 'no' }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <p class="text-center text-muted">No slots found for this date.</p>
        @endforelse
    </div>
</div>

</div>
</div>

<script>
    $(document).ready(function() {
        // Ensure only one section opens at a time
        $('#slotAccordion').on('show.bs.collapse', function(event) {
            $('.collapse.show').not(event.target).collapse('hide');
        });


        // Initialize DataTable for each slot table
        $('.slot-table').each(function() {
            $(this).DataTable({
                "ordering": false,
                "searching": false, // Ensure search is disabled
                "paging": true,
                "info": true,
                "lengthChange": false
            });
        });


        $(document).on('click', '.toggle-availability', function() {
            let button = $(this);
            let id = button.data('id');
            let status = button.data('status');

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to change availability?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, change it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('slot-management.availability', ':id') }}".replace(':id', id),
                        type: "POST",
                        data: {
                            id: id,
                            status: status,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                button
                                    .toggleClass("btn-success btn-danger")
                                    .text(status === "yes" ? "Make Unavailable" : "Make Available")
                                    .data("status", status === "yes" ? "no" : "yes");

                                button.closest("td").prev().find("span")
                                    .toggleClass("badge-success badge-danger")
                                    .text(status.charAt(0).toUpperCase() + status.slice(1));
                            }
                        }
                    });
                }
            });
        });

        // Delete slot with confirmation
        $(document).on('click', '.delete-slot', function() {
            let button = $(this);
            let slotId = button.data('id');
            let isBooked = button.data('is-booked') === 'yes';

            if (isBooked) {
                Swal.fire("Cannot delete", "This slot is already booked.", "error");
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "This slot will be permanently deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('slot-management.destroy', ':id') }}".replace(':id', slotId),
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                button.closest('tr').fadeOut();
                                // Swal.fire("Deleted!", "The slot has been deleted.", "success");
                                Swal.fire("Deleted!", "The slot has been deleted.", "success").then(() => {
                                    location.reload(); // Refresh the page after OK is clicked
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection