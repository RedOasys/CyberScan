@extends('layouts.chips.main')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive overflow-hidden">
                <table class="table" id="detectionsTable">
                    <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Analysis ID</th>
                        <th>Detection</th>
                        <th>Malware Type</th>
                        <th>Certainty</th>
                        <th>Source</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">

    <!-- Your existing scripts for jQuery, Popper, and DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" ...></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" ...></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>

    <script>
        $(document).ready(function () {
            var table = $('#detectionsTable').DataTable({
                processing: true,
                ajax: "{{ route('detections.data') }}",
                columns: [
                    { data: 'file_upload_id' }, // Update to 'file_upload_id'
                    { data: 'analysis_id' },
                    { data: 'detectionStatus' },
                    { data: 'malware_type' },
                    { data: 'certainty' },
                    { data: 'source' }
                ],
                lengthMenu: [10, 25, 50, 100], // Define the available options for rows per page
                pageLength: 10, // Set the default number of rows per page
                // Additional DataTables configuration as needed
            });

            setInterval(function () {
                table.ajax.reload(null, false);
            }, 5000); // Refresh every 5 seconds
        });
    </script>
@endsection
