@extends('layouts.chips.main')

@section('content')
    <div class="container mt-4">
        <h2>VirusTotal Analysis Results</h2>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">File Details</h5>
                <p class="card-text">
                    <strong>Type Description:</strong> {{ $results['data']['attributes']['type_description'] ?? 'N/A' }}<br>
                    <strong>Creation Date:</strong>
                    {{ isset($results['data']['attributes']['creation_date']) ? date('Y-m-d H:i:s', $results['data']['attributes']['creation_date']) : 'N/A' }}<br>
                    <strong>MD5:</strong> {{ $results['data']['attributes']['md5'] ?? 'N/A' }}<br>
                    <strong>SHA256:</strong> {{ $results['data']['attributes']['sha256'] ?? 'N/A' }}<br>
                    <strong>File Names:</strong>
                    {{ isset($results['data']['attributes']['names']) ? implode(', ', $results['data']['attributes']['names']) : 'N/A' }}<br>
                    <strong>File Size:</strong> {{ $results['data']['attributes']['size'] ?? 'N/A' }} bytes<br>
                    <strong>Signature Info:</strong>
                    {{ $results['data']['attributes']['signature_info']['description'] ?? 'N/A' }}<br>
                    <strong>Last Modification Date:</strong>
                    {{ isset($results['data']['attributes']['last_modification_date']) ? date('Y-m-d H:i:s', $results['data']['attributes']['last_modification_date']) : 'N/A' }}<br>
                    <strong>Times Submitted:</strong> {{ $results['data']['attributes']['times_submitted'] ?? 'N/A' }}<br>
                    <strong>Harmless Votes:</strong> {{ $results['data']['attributes']['total_votes']['harmless'] ?? 'N/A' }}<br>
                    <strong>Malicious Votes:</strong> {{ $results['data']['attributes']['total_votes']['malicious'] ?? 'N/A' }}<br>
                    <strong>Detection:</strong> {{ $detection ?? 'N/A' }}<br>
                    @if ($detection !== 'Undetected')
                        <strong>Certainty of Detection:</strong> {{ $certainty ?? 'N/A' }}%<br>
                        <strong>Kind:</strong> {{ $kind ?? 'N/A' }}<br>
                    @endif
                </p>
                <a href="{{ route('analysis.tasks.create') }}" class="btn btn-primary">Create New Task</a>
            </div>
        </div>
    </div>
@endsection
