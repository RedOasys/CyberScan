
@extends('layouts.app')
@section('title', 'Analyze Files')
@section('title', 'Home')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Analysis Results</div>
                    <div class="card-body">
                        @if(session('analysisResults'))
                            <div class="list-group">
                                @foreach(session('analysisResults') as $fileName => $result)
                                    <div class="list-group-item">
                                        <h5>{{ $fileName }}</h5>
                                        <p>Analysis ID: {{ $result['analysis_id'] ?? 'N/A' }}</p>
                                        <!-- Display other relevant result data here -->
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>No analysis results to display.</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Add any specific JavaScript or links to scripts here --}}
@endpush
