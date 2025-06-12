@extends('layouts.app')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">Edit</h1>
    </div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update Admin Profile</h6>
        </div>
        <div class="card-body">
            {{-- HTML outiside the template start --}}
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="col-md-4 text-center mb-3">
                    <label for="profile_picture" style="cursor: pointer;">
                        <img src="{{ auth()->user()->profile_picture ? url(auth()->user()->profile_picture) : url('default-profile.png') }}"
                            alt="Profile Picture"
                            class="img-fluid rounded-circle"
                            style="width: 150px; height: 150px; object-fit: cover; border: 2px solid #ddd;">
                    </label>

                    {{-- Hidden file input --}}
                    <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*">

                    @error('profile_picture')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror

                    <div class="mt-2 text-muted">Click image to change</div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label>First Name:</label>
                        <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" class="form-control">
                        @error('first_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Last Name:</label>
                        <input type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" class="form-control">
                        @error('last_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Email:</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control">
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Phone:</label>
                        <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" maxlength="15" oninput="validatePhone(this)" class="form-control">
                        @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Date of Birth:</label>
                        <input type="date" name="dob" value="{{ old('dob', auth()->user()->dob) }}" class="form-control">
                        @error('dob')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Full Address:</label>
                        <textarea name="full_address" class="form-control">{{ old('full_address', auth()->user()->full_address) }}</textarea>
                        @error('full_address')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Role:</label>
                        <input type="text" name="role" value="Astrologer (Admin)" class="form-control" readonly>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Gender:</label>
                        <select name="gender" class="form-control">
                            <option value="male" {{ old('gender', auth()->user()->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', auth()->user()->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', auth()->user()->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>


            {{-- HTML outiside the template end --}}
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<script>
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.querySelector('label[for="profile_picture"] img').src = event.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    });
</script>


@endsection