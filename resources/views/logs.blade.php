{{-- Extending the main layout --}}
@extends('layouts.chips.main')

{{-- Additional styles can be added here if your layout has a section for it --}}
@section('additional-styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection

{{-- Content section --}}
@section('content')
    <h1>Logs</h1>
    <div class="container mt-5">
        @foreach($logs as $log)
            <div class="card my-2">
                <div class="card-header">{{ $log->date }}</div>
                <div class="card-body">
                    <pre>{{ $log->entries }}</pre>
                </div>
            </div>
        @endforeach
    </div>
@endsection

{{-- Additional scripts can be added here if your layout has a section for it --}}
@section('additional-scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
