@extends('layouts.chips.main')

@section('content')
    <div class="container">
        <h2>Analysis Tasks Queue</h2>

        <table class="table table-bordered" id="analysisQueueTable">
            <thead>
            <tr>
                <th>ID</th>
                <th>File Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            {{-- Content will be loaded via DataTables --}}
            </tbody>
        </table>
    </div>

    <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">

    <script
        src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>
    <script>
        $(document).ready(function () {
            var table = $('#analysisQueueTable').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                ajax: "{{ route('analysis.tasks.queue.data') }}",
                columns: [
                    {data: 'analysis_id'},
                    {data: 'file_name'},
                    {data: 'actions', orderable: false, searchable: false},
                    {data: 'status'}

                ],
                drawCallback: function (settings) {
                    // For each 'analysis_id' value in the table
                    this.api().column(0).data().each(function (analysis_id) {
                        // AJAX call to update the analysis
                        $.ajax({
                            url: '/update-analysis/' + analysis_id,
                            type: 'GET',
                            success: function(response) {
                                // Handle the response
                                console.log('Analysis Updated:', response);
                                // Optionally reload the table or handle the update in another way
                            },
                            error: function(error) {
                                // Handle errors
                                console.error('Update failed:', error);
                            }
                        });
                    });
                }
            });

            // Refresh DataTable every 5 seconds
            setInterval(function () {
                table.ajax.reload(null, false); // false means don't reset user paging
            }, 5000); // 5000 milliseconds = 5 seconds
        });
    </script>

@endsection
