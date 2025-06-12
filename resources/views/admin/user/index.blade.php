@extends('layouts.app')
@section('css')

@endsection
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">User Listing</h1>
        <!-- <a href="" class="btn btn-sm btn-primary shadow-sm">
            <i class="fa-sm text-white-50"></i> Add User
        </a> -->
    </div>



    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">User List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email address</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <!-- <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Email address</th>
                            <th>Phone</th>
                            <th>Status</th>
                        </tr>
                    </tfoot> -->
                    <tbody>
                        @forelse ($users as $key => $employe)
                        <tr>
                            <td>{{ucfirst($employe->first_name)}} {{$employe->last_name}}</td>
                            <td>{{$employe->email}}</td>
                            <td>{{$employe->country_code}}-{{$employe->phone }}</td>
                            <td>
                                <button class="btn btn-sm block-user {{ $employe->status == 'active' ? 'btn-success' : 'btn-danger' }}"
                                    data-id="{{ $employe->id }}"
                                    data-status="{{ $employe->status == 'active' ? 'block' : 'unblock' }}">
                                    {{ $employe->status == 'active' ? 'Unblock' : 'Block' }}
                                </button>
                            </td>
                            <td>{{\Carbon\Carbon::parse($employe->created_at)->format('d-m-Y h:i:s')}}</td>
                            <td>
                                <!-- <a href="{{ route('users.edit', $employe->id) }}" class="btn btn-sm btn-warning">Edit</a> -->
                                <!-- <button class="btn btn-sm btn-danger delete-user" data-id="{{ $employe->id }}">Delete</button> -->
                                <a href="{{ route('user.details', $employe->id) }}" class="btn btn-sm btn-warning">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No user found.</td>
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
            "ordering": false
        });

        $(document).on("click", ".block-user", function() {
            var userId = $(this).data("id");
            var action = $(this).data("status");
            var confirmText = action === "block" ? "Yes, block!" : "Yes, unblock!";
            var successText = action === "block" ? "User has been blocked!" : "User has been unblocked!";

            Swal.fire({
                title: "Are you sure?",
                text: `Do you want to ${action} this user?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: confirmText
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('users.block-unblock', ':id') }}".replace(':id', userId),
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            action: action
                        },
                        success: function(response) {
                            Swal.fire("Updated!", successText, "success").then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        });
    });
</script>
@endsection