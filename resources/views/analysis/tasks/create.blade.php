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
            <button type="button" class="btn btn-secondary d-none" id="analyze-all-button" >Analyze All</button>
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
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">

    <script
        src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
            integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
            crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>
    <script>
        $(document).ready(function () {
            let message = "{{ session('message') }}";
            let analysisId = "{{ session('analysisId') }}";

            if (message.includes('Task submitted successfully')) {
                $('#success-alert').show();
                $('#analysisForm').hide();
                fetchAnalysisData(analysisId);
            }
            $('#analyze-all-button').click(function () {
                let files = $('#uploadedFile option').map(function () { return $(this).val(); }).get();
                $('#analysisInfoSection').hide(); // Keep this section hidden
                submitAllFiles(files);
            });
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
        function submitAllFiles(fileIds) {
            let submitPromises = fileIds.map(fileId => {
                let formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val()); // Include CSRF token
                formData.append('uploaded_file', fileId);
                formData.append('timeout', $('#analysisTimeout').val());
                formData.append('machine', $('#machineSelection').val());
                // Add other form data as needed

                return fetch('{{ route('analysis.tasks.submit') }}', {
                    method: 'POST',
                    body: formData
                });
            });

            Promise.all(submitPromises)
                .then(responses => {
                    if (responses.every(response => response.ok)) {
                        window.location.href = "{{ route('analysis.tasks.all') }}";
                    } else {
                        alert('Error submitting one or more files.');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
@endsection
