@extends('layouts.app')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">Update Slot</h1>
        <a href="{{ route('slot-management.index') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fa-sm text-white-50"></i> Back
        </a>
    </div>

    <!-- Update Slot Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update Slot</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('slot-management.update', $selectedAvailableTime->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <input type="hidden" name="id" value="{{ old('id', $selectedAvailableTime->id) }}">
                    <!-- Date Field -->
                    <div class="mb-3 col-md-6">
                        <label>Date:</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', $selectedAvailableTime->date) }}">
                        @error('date') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <!-- Price Field -->
                    <div class="mb-3 col-md-6">
                        <label>Price:</label>
                        <input type="number" name="price" id="price" class="form-control"
                            value="{{ old('price', $selectedAvailableTime->price ?? 2100) }}">
                        @error('price') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <!-- Start Time Field -->
                    <div class="mb-3 col-md-6">
                        <label>Start Time:</label>
                        <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $selectedAvailableTime->start_time) }}">
                        @error('start_time') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <!-- End Time Field -->
                    <div class="mb-3 col-md-6">
                        <label>End Time:</label>
                        <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $selectedAvailableTime->end_time) }}">
                        @error('end_time') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                </div>

                <button type="submit" class="btn btn-success">Update</button>
            </form>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
@endsection