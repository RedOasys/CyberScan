@extends('layouts.chips.main')

@section('content')
    <link href="https://cdn.datatables.net/v/zf/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/r-2.5.0/datatables.min.css" rel="stylesheet">



    <div class="card shadow mb-4 ">

        <div class="card-body">
            <div class="table-responsive overflow-hidden ">
                <table id="detectionsTable" class="display">
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
                    @foreach ($detections as $detection)
                        <tr>
                            <td>{{ $detection->fileName }}</td>
                            <td>{{ $detection->analysis_id }}</td>
                            <td>{{ $detection->detectionStatus }}</td>
                            <td>{{ $detection->malware_type }}</td>
                            <td>{{ $detection->certainty }}</td>
                            <td>{{ $detection->source }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">

    <script
        src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#detectionsTable').DataTable();
        });
    </script>
@endsection
