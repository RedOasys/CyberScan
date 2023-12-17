@extends('layouts.chips.main')

@section('content')
    <div class="container mt-4">
        <h2>VirusTotal Analysis Results</h2>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">File Details</h5>
                <p class="card-text">
                    <strong>Type Description:</strong> {{ $results['data']['attributes']['type_description'] }}<br>
                    <strong>Creation
                        Date:</strong> {{ date('Y-m-d H:i:s', $results['data']['attributes']['creation_date']) }}<br>
                    <strong>MD5:</strong> {{ $results['data']['attributes']['md5'] }}<br>
                    <strong>SHA256:</strong> {{ $results['data']['attributes']['sha256'] }}<br>
                    <strong>File Names:</strong> {{ implode(', ', $results['data']['attributes']['names']) }}<br>
                    <strong>File Size:</strong> {{ $results['data']['attributes']['size'] }} bytes<br>
                    <strong>Signature
                        Info:</strong> {{ $results['data']['attributes']['signature_info']['description'] }}<br>
                    <strong>Last Modification
                        Date:</strong> {{ date('Y-m-d H:i:s', $results['data']['attributes']['last_modification_date']) }}
                    <br>
                    <strong>Times Submitted:</strong> {{ $results['data']['attributes']['times_submitted'] }}<br>
                    <strong>Harmless Votes:</strong> {{ $results['data']['attributes']['total_votes']['harmless'] }}<br>
                    <strong>Malicious Votes:</strong> {{ $results['data']['attributes']['total_votes']['malicious'] }}
                    <br>
                    <strong>Detection:</strong> {{ $detection }}<br>
                    @if ($detection !== 'Undetected')
                        <strong>Certainty of Detection:</strong> {{ $certainty }}%<br>
                        <strong>Kind:</strong> {{ $kind }}<br>
                    @endif


                </p>
                <a href="{{ route('analysis.tasks.create') }}" class="btn btn-primary">Create New Task</a>
            </div>
        </div>
    </div>
@endsection
