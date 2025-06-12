@extends('layouts.app')
@section('title', 'Update Bank Details - AstroGuide')
@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">Edit Bank Information</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update Bank Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.bank.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label>Account Holder Name:</label>
                        <input type="text" name="account_holder_name" value="{{ old('account_holder_name', $bankDetails->account_holder_name ?? '') }}" class="form-control">
                        @error('account_holder_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Phone Number (Bank):</label>
                        <input type="text" name="phone" value="{{ old('phone', $bankDetails->phone ?? '') }}" maxlength="15" oninput="validatePhone(this)" class="form-control">
                        @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Bank Name:</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $bankDetails->bank_name ?? '') }}" class="form-control">
                        @error('bank_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>Account Number:</label>
                        <input type="text" name="account_number" value="{{ old('account_number', $bankDetails->account_number ?? '') }}" class="form-control">
                        @error('account_number')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>IFSC Code:</label>
                        <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $bankDetails->ifsc_code ?? '') }}" class="form-control">
                        @error('ifsc_code')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label>UPI ID:</label>
                        <input type="text" name="upi_id" value="{{ old('upi_id', $bankDetails->upi_id ?? '') }}" class="form-control">
                        @error('upi_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Bank Details</button>
            </form>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
@endsection