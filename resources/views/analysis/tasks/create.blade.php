@extends('layouts.chips.main')

@section('content')
    <div class="container mt-4">
        <h2>Create Analysis Task</h2>
        <p>Select a file and set parameters for analysis.</p>

        {{-- Display success/error message --}}
        @if (session('message'))
            <div class="alert alert-success" id="success-alert" style="display: none;">
                {{ session('message') ?? '' }}
            </div>
        @endif

        {{-- Form for creating a task --}}
        <form action="{{ route('analysis.tasks.submit') }}" method="POST" id="analysisForm">
            @csrf

            <div class="mb-3">
                <label for="uploadedFile" class="form-label">Select File for Analysis</label>
                @if($unanalyzedFiles->isEmpty())
                    <div class="alert alert-warning" role="alert">
                        All Uploaded Files have been analyzed / Are queued for analysis.
                    </div>
                @else
                    <select class="form-select" id="uploadedFile" name="uploaded_file">
                        @foreach ($unanalyzedFiles as $file)
                            <option value="{{ $file->id }}">{{ $file->file_name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div class="mb-3">
                <label for="analysisTimeout" class="form-label">Analysis Timeout (in seconds)</label>
                <input type="number" class="form-control" id="analysisTimeout" name="timeout"
                       placeholder="Enter timeout in seconds" value="120">
            </div>

            <div class="mb-3">
                <label for="machineSelection" class="form-label">Machine Selection</label>
                <select class="form-select" id="machineSelection" name="machine">
                    <option value="default" selected>Default</option>
                    {{-- Add more machine options as required --}}
                </select>
            </div>
            <button type="submit" class="btn btn-primary" id="submit-button">Submit Task</button>
        </form>

        {{-- Analysis Information Section --}}
        <div id="analysisInfoSection" style="display: none;">
            <h3>Analysis Information</h3>
            <div class="mb-3">
                <label for="fileUploadId">File Upload ID:</label>
                <input type="text" class="form-control" id="fileUploadId" disabled>
            </div>
            <div class="mb-3">
                <label for="analysisId">Analysis ID:</label>
                <input type="text" class="form-control" id="analysisId" disabled>
            </div>
            <div class="mb-3">
                <label for="score">Score:</label>
                <input type="text" class="form-control" id="score" disabled>
            </div>
            <div class="mb-3">
                <label for="kind">Kind:</label>
                <input type="text" class="form-control" id="kind" disabled>
            </div>
            <div class="mb-3">
                <label for="state">State:</label>
                <input type="text" class="form-control" id="state" disabled>
            </div>
            <div class="mb-3">
                <label for="mediaType">Media Type:</label>
                <input type="text" class="form-control" id="mediaType" disabled>
            </div>
            <div class="mb-3">
                <label for="md5">MD5:</label>
                <input type="text" class="form-control" id="md5" disabled>
            </div>
            <div class="mb-3">
                <label for="sha1">SHA1:</label>
                <input type="text" class="form-control" id="sha1" disabled>
            </div>
            <div class="mb-3">
                <label for="sha256">SHA256:</label>
                <input type="text" class="form-control" id="sha256" disabled>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let message = "{{ session('message') }}";
            let analysisId = "{{ session('analysisId') }}";

            if (message.includes('Task submitted successfully')) {
                $('#success-alert').show();
                $('#analysisForm').hide();
                fetchAnalysisData(analysisId);
            }
        });

        function fetchAnalysisData(analysisId) {
            setInterval(function () {
                fetch('/analysis/data/' + analysisId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.analysis_id) {
                            displayAnalysisData(data);
                            clearInterval(this);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching analysis data:', error);
                    });
            }, 5000);
        }

        function displayAnalysisData(data) {
            $('#fileUploadId').val(data.file_upload_id);
            // Update other fields similarly
            $('#analysisInfoSection').show();
        }
    </script>
@endsection
