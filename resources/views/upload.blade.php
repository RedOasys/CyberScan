@extends('layouts.app')

@section('title', 'Upload')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
@endpush

@section('content')
    <div class="container">
        <!-- Upload Form -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Upload Files</div>
                    <div class="card-body">
                        <form action="{{ route('upload') }}" method="post" enctype="multipart/form-data" id="fileUploadForm">
                            @csrf
                            <div class="form-group">
                                <label for="files">Select files:</label>
                                <input type="file" name="files[]" id="files" multiple class="form-control">
                                <small class="form-text text-muted">Maximum of 10 files allowed, only .exe files are accepted.</small>
                            </div>
                            <button type="submit" class="btn btn-primary" id="uploadBtn">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar Container -->
        <div class="row justify-content-center mt-3" id="progressContainer" style="display: none;">
            <div class="col-md-8">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Info Modal -->
    <div class="modal fade" id="uploadInfoModal" tabindex="-1" aria-labelledby="uploadInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadInfoModalLabel">Upload Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="uploadInfoModalBody">
                    <!-- Uploaded and skipped files info will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modalOkayBtn">Okay</button>
                    <!-- Analyze button inside a form -->
                    <form action="{{ route('analyze') }}" method="post" style="display: inline;">
                        @csrf
                        <input type="hidden" name="uploadedFiles" id="uploadedFilesInput"> <!-- Hidden input to hold the list of uploaded files -->
                        <button type="submit" class="btn btn-primary" id="analyzeBtn" style="display: none;">Analyze</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5+z0I5t9z5lFf5r5l5u5z5F5n5nczq+qCEZu8YEx" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#modalOkayBtn').click(function() {
                $('#fileUploadForm').trigger('reset'); // Reset the form
                $('#uploadInfoModalBody').html(''); // Clear the modal content
                $('#analyzeBtn').hide(); // Hide the Analyze button
                // Optionally, reset other elements if needed
            });
            $('#files').on('change', function() {
                if (this.files.length > 10) {
                    alert('Maximum of 10 files allowed!');
                    this.value = ''; // Clear the selected files
                }
            });

            $('#fileUploadForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $('#progressContainer').show();
                $('#uploadBtn').prop('disabled', true);

                $.ajax({
                    url: '{{ route('upload') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                percentComplete = parseInt(percentComplete * 100);
                                $('.progress-bar').css('width', percentComplete + '%');
                                $('.progress-bar').attr('aria-valuenow', percentComplete);
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        let uploadedFiles = response.uploaded.length > 0 ? `<h6>Uploaded Files:</h6><ul>${response.uploaded.map(file => `<li>${file}</li>`).join('')}</ul>` : '';
                        let skippedFiles = response.skipped.length > 0 ? `<h6>Skipped Files:</h6><ul>${response.skipped.map(file => `<li>${file}</li>`).join('')}</ul>` : '';
                        let message = `<p>${response.message}</p>`;

                        $('#uploadInfoModalBody').html(uploadedFiles + skippedFiles + message);
                        var uploadInfoModal = new bootstrap.Modal(document.getElementById('uploadInfoModal'));
                        uploadInfoModal.show();

                        if (response.uploaded.length > 0) {
                            $('#analyzeBtn').show();
                            $('#uploadedFilesInput').val(JSON.stringify(response.uploaded));
                        } else {
                            $('#analyzeBtn').hide();
                        }
                    },
                    error: function(response) {
                        alert('An error occurred during the upload.');
                    },
                    complete: function() {
                        $('#progressContainer').hide();
                        $('#uploadBtn').prop('disabled', false);
                    }
                });
            });

            $('#analyzeBtn').click(function() {
                alert('Analyze logic goes here.');

            });
        });
    </script>
@endpush
