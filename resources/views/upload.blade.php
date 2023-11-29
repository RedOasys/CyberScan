@extends('layouts.app')
@section('title', 'Upload')
@section('content')
    <div class="container">
        <h2>File Upload</h2>
        <form id="file-upload-form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="files" class="form-label">Select files</label>
                <input class="form-control" type="file" id="files" name="files[]" multiple>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
        <div class="progress mt-3" style="display:none;">
            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div id="upload-status"></div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#file-upload-form').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $('.progress').show();
                var startTime = Date.now();

                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                percentComplete = parseInt(percentComplete * 100);
                                $('.progress-bar').width(percentComplete + '%');
                                $('.progress-bar').html(percentComplete + '%');

                                // Calculate speed and time remaining
                                var timeElapsed = (Date.now() - startTime) / 1000; // in seconds
                                var speed = evt.loaded / timeElapsed; // bytes per second
                                var remainingBytes = evt.total - evt.loaded;
                                var remainingTime = remainingBytes / speed; // in seconds

                                var statusHtml = '<p>Speed: ' + (speed / 1024).toFixed(2) + ' KB/s</p>';
                                statusHtml += '<p>Estimated time remaining: ' + remainingTime.toFixed(2) + ' seconds</p>';
                                $('#upload-status').html(statusHtml);
                            }
                        }, false);
                        return xhr;
                    },
                    type: 'POST',
                    url: '{{ route("file.upload") }}',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        setTimeout(function() {
                            var successHtml = '<p>Uploaded Files: ' + response.uploaded.join(', ') + '</p>';
                            successHtml += '<p>Skipped Files: ' + response.skipped.join(', ') + '</p>';
                            $('#upload-status').append(successHtml);
                            $('.progress-bar').width('100%').html('Upload Complete');
                        }, 1000); // Delay the success message to ensure it's visible
                    },
                    error: function(response) {
                        $('#upload-status').html('<p>Error: ' + response.responseJSON.error + '</p>');
                        $('.progress-bar').width('0%').html('0%');
                    }
                });
            });
        });
    </script>


@endpush
