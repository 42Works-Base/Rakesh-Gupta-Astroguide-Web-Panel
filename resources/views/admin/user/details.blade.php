@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-lg p-4">
        <h2 class="mb-4 text-primary">User Details</h2>

        <div class="row">
            <div class="col-md-4 text-center">
                <img src="{{ $users->profile_picture ? url($users->profile_picture) : asset('default-profile.png') }}"
                    alt="Profile Picture"
                    class="img-fluid rounded-circle"
                    style="width: 150px; height: 150px;">

            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ ucfirst($users->first_name) }} {{ ucfirst($users->last_name) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $users->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ $users->country_code }} {{ $users->phone }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date of Birth:</strong></td>
                        <td>{{ $users->dob }} ({{ $users->dob_time }})</td>
                    </tr>
                    <tr>
                        <td><strong>Gender:</strong></td>
                        <td>{{ ucfirst($users->gender) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Birth City:</strong></td>
                        <td>{{ $users->birth_city }}</td>
                    </tr>
                    <tr>
                        <td><strong>Birth Country:</strong></td>
                        <td>{{ $users->birthplace_country }}</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td>{{ $users->full_address ?? 'N/A' }}</td>
                    </tr>
                    <!-- <tr>
                        <td><strong>Location:</strong></td>
                        <td>Lat: {{ $users->latitude }}, Long: {{ $users->longitude }}</td>
                    </tr> -->
                    <!-- <tr>
                        <td><strong>Timezone:</strong></td>
                        <td>{{ $users->timezone }}</td>
                    </tr> -->
                    {{--<tr>
                        <td><strong>Role:</strong></td>
                        <td>{{ ucfirst($users->role) }}</td>
                    </tr>--}}
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge {{ $users->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($users->status) }}
                            </span>
                        </td>
                    </tr>
                    {{--<tr>
                        <td><strong>OTP:</strong></td>
                        <td>{{ $users->otp }}</td>
                    </tr>--}}
                </table>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection