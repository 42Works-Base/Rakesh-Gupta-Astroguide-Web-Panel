@extends('layouts.app')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">Update</h1>
        <a href="{{ route('slot-management.index') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fa-sm text-white-50"></i> Back
        </a>
    </div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update Slot</h6>
        </div>
        <div class="card-body">
            {{-- HTML outiside the template start --}}
            <form action="{{ route('slot-management.update',$selectedAvailableTime->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">


                    <div class="mb-3 col-md-6">
                        <label>Price:</label>
                        <input type="number" name="price" id="price" value="{{ $selectedAvailableTime->price?$selectedAvailableTime->price: 2100 }}" class="form-control">
                        @error('price') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>




                </div>
                <button type="submit" class="btn btn-success">Update</button>
            </form>


            {{-- HTML outiside the template end --}}
        </div>
    </div>

</div>
<!-- /.container-fluid -->

@endsection