@extends('layouts.app')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 text-gray-800">Add</h1>
        <a href="{{ route('slot-management.index') }}" class="btn btn-sm btn-primary mb-3">
            <i class="fa-sm text-white-50"></i> Back
        </a>
    </div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Add Slot</h6>
        </div>
        <div class="card-body">
            {{-- HTML outiside the template start --}}
            <form action="{{ route('slot-management.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label>Date:</label>
                        <input type="date" name="date" value="{{ old('date') }}" class="form-control">
                        @error('date') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Price:</label>
                        <input type="number" name="price" id="price" value="{{ old('price', 2100) }}" class="form-control" min="0.01" step="0.01" placeholder="Enter price">
                        @error('price') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    {{--<div class="mb-3 col-md-6">
                        <label>Start Time:</label>
                        <input type="time" name="day_start_time" id="day_start_time" value="{{ old('start_time') }}" class="form-control">
                    @error('day_start_time') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label>End Time:</label>
                    <input type="time" name="day_end_time" id="day_end_time" value="{{ old('day_end_time') }}" class="form-control" readonly>
                    @error('day_end_time') <div class="text-danger">{{ $message }}</div> @enderror
                </div>--}}

                <div class="mb-3 col-md-6">
                    <label>Day Start Time:</label>
                    <input type="time" name="day_start_time" id="day_start_time" value="{{ old('day_start_time') }}" class="form-control">
                    @error('day_start_time') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label>Day End Time:</label>
                    <input type="time" name="day_end_time" id="day_end_time" value="{{ old('day_end_time') }}" class="form-control">
                    @error('day_end_time') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        </form>


        {{-- HTML outiside the template end --}}
    </div>
</div>

</div>
<!-- /.container-fluid -->

@endsection