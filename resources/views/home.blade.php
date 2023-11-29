@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="container">
        <div class="row">
            <!-- Card 1: Recent Uploads -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        Recent Uploads
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Size (KB)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Loop through recent uploads -->
                                @foreach ($recentUploads as $upload)
                                    <tr>
                                        <td>{{ $upload->id }}</td>
                                        <td class="text-truncate" style="max-width: 200px;">{{ $upload->file_name }}</td>
                                        <td>{{ $upload->file_size_kb }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: File Size Analysis -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        File Size Analysis
                    </div>
                    <div class="card-body">
                        <canvas id="fileSizeChart" width="400" height="400"></canvas>
                    </div>
                </div>
            </div>

            <!-- Card 3: Malware Types (Assuming you have this data) -->
            <!-- ... -->
        </div>

        <!-- Additional Content -->
        <!-- ... -->
    </div>

    @push('scripts')
        <!-- Chart.js Script -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var ctx = document.getElementById('fileSizeChart').getContext('2d');
                var fileSizeChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($fileSizeAnalysisLabels),
                        datasets: [{
                            label: 'Total File Size (KB)',
                            data: @json($fileSizeAnalysisData),
                            backgroundColor: 'rgba(0, 123, 255, 0.5)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @endpush

    <style>
        /* Additional styles if needed */
        .text-truncate {
            max-width: 200px; /* Adjust as needed */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endsection
