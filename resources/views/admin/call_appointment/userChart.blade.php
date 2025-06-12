@extends('layouts.app')
@section('title', 'User Chart Details - AstroGuide')

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
        <h1 class="h3 mb-2 text-gray-800">User Chart Details</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Details</h6>
        </div>

        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="mb-3">
                <div class="row align-items-end gx-2">
                    <!-- Chart Type Selector -->
                    <div class="col-auto">
                        <label for="chart_type" class="small">Chart Type</label>
                        <select name="chart_type" id="chart_type" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option>Select Chart</option>
                            @foreach($chartTypeArray as $chartType)
                            <option value="{{ $chartType['type'] }}" {{ request('chart_type') == $chartType['type'] ? 'selected' : '' }}>
                                {{ $chartType['name'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-auto">
                        <label class="d-block small invisible">Reset</label>
                        <a href="{{ route(\Illuminate\Support\Facades\Route::currentRouteName(), $callAppointment->id) }}" class="btn btn-sm btn-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Chart SVG -->
            @if(request('chart_type'))
            @php
            $svgChart = App\Helpers\AstrologyHelper::getChartData($callAppointment->user, request('chart_type'));
            //$svgChartNew = App\Helpers\AstrologyHelper::getChartDataNew($callAppointment->user, request('chart_type'));
            @endphp

            @if($svgChart)
            <div class="chart-container mt-4">

                {!! html_entity_decode(json_decode($svgChart)) !!}
            </div>
            {{--@if($svgChartNew)
            <h2>New Response</h2>
            <div class="chart-container mt-4">

                @php
                $decoded = json_decode($svgChartNew);
                $svg = html_entity_decode($decoded->svg);
                @endphp

                {!! $svg !!}
            </div>
            @endif--}}
            <div class="chart-container mt-4">
                <!-- @if($customPrediction)
                @foreach($customPrediction as $prediction)
                <h5>{{$prediction['title']}}</h5>
                <p class="small">{{$prediction['text']}}</p>
                @endforeach
                @endif -->
            </div>

            <div class="chart-container mt-4">
                @if($lalKitabReport)
                <h3>Prediction</h3>
                @foreach($lalKitabReport as $lalKitab)
                <h5>{{ $lalKitab['title'] }}</h5>
                <p class="small">{{ $lalKitab['text'] }}</p>

                @if (!empty($lalKitab['remedies']))
                <h6>Remedies:</h6>
                <ul class="small">
                    @foreach($lalKitab['remedies'] as $remedy)
                    <li>{{ $remedy }}</li>
                    @endforeach
                </ul>
                @endif

                @endforeach
                @endif

            </div>

            <div class="chart-container mt-4">
                @if($customAscendantReport)
                <h3>Ascendant-Report</h3>
                @foreach($customAscendantReport as $prediction)
                <h5>{{$prediction['title']}}</h5>
                <p class="small">{{$prediction['text']}}</p>
                @endforeach
                @endif
            </div>

            <div class="chart-container mt-4">
                @if($customNumerologyReport)
                <h3>Numerology-Report</h3>
                @foreach($customNumerologyReport as $prediction)
                <h5>{{$prediction['title']}}</h5>
                <p class="small">{{$prediction['text']}}</p>
                @endforeach
                @endif
            </div>
            @else
            <div class="alert alert-warning mt-4">No chart available for this type.</div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection