@extends('layouts.app')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">Slot Listing</h1>
        <a href="{{route('slot-management.create')}}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fa-sm text-white-50"></i> Add Slot
        </a>
    </div>



    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Slot List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Price (₹)</th>
                            <th>is Available</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <!-- <tfoot>
                        <tr>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Price (₹)</th>
                            <th>is Available</th>
                            <th>Action</th>
                        </tr>
                    </tfoot> -->
                    <tbody>
                        @forelse ($availableTimes as $time)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($time->date)->format('Y-m-d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($time->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($time->end_time)->format('h:i A') }}</td>
                            <td>₹{{ number_format($time->price, 2) }}</td>
                            <td>
                                <button class="btn btn-sm toggle-availability {{ $time->is_available == 'yes' ? 'btn-success' : 'btn-danger' }}"
                                    data-id="{{ $time->id }}"
                                    data-status="{{ $time->is_available == 'yes' ? 'no' : 'yes' }}">
                                    {{ $time->is_available == 'yes' ? 'Make Unavailable' : 'Make Available' }}
                                </button>
                            </td>
                            <td>
                                <a href="{{ route('slot-management.edit', $time->id) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No slot found.</td>
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
        $('#dataTable').DataTable({
            "order": [
                [0, "desc"]
            ] // Sorts by first column (Date) in descending order
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
</script>
@endsection