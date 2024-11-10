@extends('layouts.app')

<!-- Include the vertical navigation bar -->
@include('common.navbar')

@section('content')
    <div class="container-fluid">
        <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div class="main-content">
                <!-- Alert Messages -->
                @include('common.alert')
                {{-- Progress bar at the top --}}
                <div class="progress" style="height: 20px; margin-bottom: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                        style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                        {{ $progress }}%
                    </div>
                </div>

                {{-- The steps taken to recocile the products discrepancy here--}}
                <form action="{{ route('inventory.audit.step4') }}" method="POST">
                    @csrf
                
                    <label for="actions_taken" class="form-label">Describe Actions Taken to Resolve Discrepancies:</label>
                    <textarea class="form-control" id="actions_taken" rows="5" name="actions_taken" placeholder="List all actions taken for each discrepancy here..." required></textarea>
                
                    <button type="submit" class="btn btn-primary">Next</button>
                </form>
            </div>
        </main>
    </div>
@endsection
