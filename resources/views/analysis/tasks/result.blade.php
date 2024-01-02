@extends('layouts.chips.main')

@section('content')
    <div class="container mt-4">
        <h2>Pre-Analysis Information (Static Info)</h2>

        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fileUploadId" class="form-label">File Upload ID:</label>
                        <input type="text" class="form-control" id="fileUploadId" value="{{ $analysis->file_upload_id }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="analysisId" class="form-label">Analysis ID:</label>
                        <input type="text" class="form-control" id="analysisId" value="{{ $analysis->analysis_id }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="score" class="form-label">Score:</label>
                        <input type="text" class="form-control" id="score" value="{{ $analysis->score }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kind" class="form-label">Kind:</label>
                        <input type="text" class="form-control" id="kind" value="{{ $analysis->kind }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="state" class="form-label">State:</label>
                        <input type="text" class="form-control" id="state" value="{{ $analysis->state }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mediaType" class="form-label">Media Type:</label>
                        <input type="text" class="form-control" id="mediaType" value="{{ $analysis->media_type }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="md5" class="form-label">MD5:</label>
                        <input type="text" class="form-control" id="md5" value="{{ $analysis->md5 }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sha1" class="form-label">SHA1:</label>
                        <input type="text" class="form-control" id="sha1" value="{{ $analysis->sha1 }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sha256" class="form-label">SHA256:</label>
                        <input type="text" class="form-control" id="sha256" value="{{ $analysis->sha256 }}" readonly>
                    </div>
                </div>
                <a href="javascript:void(0)" onclick="updateAnalysis('{{ $analysis->analysis_id }}')" class="btn btn-secondary">Refresh</a>

                <a href="{{ route('analysis.static') }}" class="btn  btn btn-outline-dark">View Detailed Static Analysis</a>
                <a href="{{ route('analysis.dynamic') }}" class="btn  btn btn-outline-info">View Dynamic Analysis</a>
                <a href="{{ route('analysis.virustotal', ['md5' => $analysis->md5]) }}" class="btn btn-warning d-none">Check VirusTotal</a>
                <a href="{{ route('analysis.tasks.create') }}" class="btn btn-primary">Create New Task</a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function updateAnalysis(analysisId) {
            $.ajax({
                url: '/update-analysis/' + analysisId,
                type: 'GET',
                success: function(response) {
                    // Handle the response
                    console.log('Analysis Updated:', response);
                    location.reload(); // Reload the page to update the info
                },
                error: function(error) {
                    // Handle errors
                    console.error('Update failed:', error);
                }
            });
        }
    </script>

@endsection
