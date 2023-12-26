@extends('layouts.chips.main')

@section('head')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/v/zf/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/r-2.5.0/datatables.min.css" rel="stylesheet">
@endsection

@section('content')


    <div class="main-content">
        <!-- Table for displaying detections -->
        <table class="table" id="detectionsTable">
            <thead>
            <tr>
                <th>File Upload ID</th>
                <th>Analysis ID</th>
                <th>Detected</th>
                <th>Malware Type</th>
                <th>Certainty</th>
                <th>Source</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($detections as $detection)
                <tr>
                    <td>{{ $detection->file_upload_id }}</td>
                    <td>{{ $detection->analysis_id }}</td>
                    <td>{{ $detection->detected == 1 ? 'Detected' : 'Undetected' }}</td>
                    <td>{{ $detection->malware_type }}</td>
                    <td>{{ $detection->certainty }}</td>
                    <td>{{ $detection->source }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <!-- DataTables JS and PDF make scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/zf/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/r-2.5.0/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#detectionsTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
                ]
            });
        });
    </script>
@endsection
