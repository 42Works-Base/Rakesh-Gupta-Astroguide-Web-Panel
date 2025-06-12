@extends('layouts.app')
@section('title', 'User Prediction Details - AstroGuide')

@section('css')
<style>
    .chart-container {
        /*background-color: #fff3cd;*/
        /* Light yellow background */
        padding: 20px;
        border-radius: 10px;
        overflow-x: auto;
    }

    .chart-container svg {
        display: block;
        margin: auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">User Prediction Details</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Details</h6>
        </div>

        <div class="card-body">
            <!-- Filter Form -->
            <form method="POST" action="{{ route('appointment-management.user.predictions.submit', $callAppointment->id) }}" class="mb-3">
                @csrf
                <div class="row align-items-end gx-2">
                    <div class="col-auto">
                        <label for="predictions_type" class="small">Prediction Type</label>
                        <select name="predictions_type" id="predictions_type" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option>Select Predictions</option>
                            @foreach($predictionsTypeArray as $predictionsType)
                            <option value="{{ $predictionsType['type'] }}" {{ (old('predictions_type', $selectedPredictionType) == $predictionsType['type']) ? 'selected' : '' }}>
                                {{ $predictionsType['name'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            @if($customPrediction)
            <div class="chart-container mt-4">
                @foreach($customPrediction as $prediction)
                <h5>{{ $prediction['title'] }}</h5>
                <p class="small">{{ $prediction['text'] }}</p>
                @endforeach
            </div>
            @elseif(!is_null($selectedPredictionType))
            <div class="alert alert-warning mt-4">No prediction available for this type.</div>
            @endif
<p>New response.</p>
            @if($customPredictionNew)
            <div class="chart-container mt-4">
                @foreach($customPredictionNew as $prediction)
                <h5>{{ $prediction['title'] }}</h5>
                <p class="small">{{ $prediction['text'] }}</p>
                @endforeach
            </div>
            @elseif(!is_null($selectedPredictionType))
            <div class="alert alert-warning mt-4">No prediction available for this type.</div>
            @endif
        </div>
    </div>
</div>
@endsection