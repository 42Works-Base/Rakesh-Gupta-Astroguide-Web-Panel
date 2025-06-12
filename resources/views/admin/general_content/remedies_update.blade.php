@extends('layouts.app')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">General Remedies</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fa-sm text-white-50"></i> Back
        </a>
    </div>

    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update General Remedies</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('general-remedies.save') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="content">Content:</label>
                    <textarea name="content" id="content" rows="10" class="form-control">{{ old('content', $general_remedies->content ?? '') }}</textarea>
                    @error('content')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
@endsection