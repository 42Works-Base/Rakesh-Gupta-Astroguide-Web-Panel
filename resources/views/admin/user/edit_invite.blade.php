@extends('layouts.app')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-2 text-gray-800">{{ isset($employee) ? 'Edit' : 'Add' }}</h1>
    <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-primary shadow-sm">
        <i class="fa-sm text-white-50"></i> Back
    </a>
</div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update Employe</h6>
        </div>
        <div class="card-body">
            {{-- HTML outiside the template start --}}
            <form action="{{ isset($employee) ? route('employees-update-invite', $employee->id) : route('admin.employees.store') }}" method="POST">
                @csrf
                @if(isset($employee))
                @method('PUT')
                @endif

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label>First Name:</label>
                        <input type="text" name="firstname" value="{{ old('firstname', $employee->firstname ?? '') }}" class="form-control">
                        @error('firstname')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label>Last Name:</label>
                        <input type="text" name="lastname" value="{{ old('lastname', $employee->lastname ?? '') }}" class="form-control">
                        @error('lastname')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Email:</label>
                        <input type="email" name="email" value="{{ old('email', $employee->email ?? '') }}" class="form-control" {{ isset($employee->email) ? 'readonly' : '' }}>
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 col-md-6">
                        <label>Phone:</label>
                        <input type="text" name="mobile_phone" value="{{ old('mobile_phone', $employee->phone ?? '') }}" class="form-control">
                        @error('mobile_phone')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label>Wage:</label>
                        <input type="number" name="wage" value="{{ old('wage', $employee->wage_rate ?? '') }}" class="form-control">
                        @error('wage')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label>Assign a Shift:</label>
                        <select class="form-control" name="shift_id" id="shift" required>
                            <option value="">Select a Shift</option>
                            @if(!empty($shifts) && $shifts->count() > 0)
                            @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}" @if($shift->id==$employee->shift_id) selected @endif>
                                {{ $shift->shift_name }}
                            </option>
                            @endforeach
                            @else
                            <option value="" disabled>No Shift Available</option>
                            @endif
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">{{ isset($employee) ? 'Update' : 'Create' }}</button>
            </form>
            {{-- HTML outiside the template end --}}
        </div>
    </div>

</div>
<!-- /.container-fluid -->


@endsection