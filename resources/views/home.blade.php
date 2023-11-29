@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <!-- Card 1 -->
                <div class="card mb-4">
                    <div class="card-header">
                        Uploads
                    </div>
                    <div class="card-body">
                        <div class="fake-table">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Size</th>
                                </tr>
                                </thead>
                                <tbody>
                                @for ($i = 1; $i <= 5; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>File {{ $i }}</td>
                                        <td>{{ rand(100, 1000) }} KB</td>
                                    </tr>
                                @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Card 2 -->
                <div class="card mb-4">
                    <div class="card-header">
                        Analysis
                    </div>
                    <div class="card-body">
                        <div class="fake-table">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Count</th>
                                </tr>
                                </thead>
                                <tbody>
                                @for ($i = 1; $i <= 5; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>Category {{ $i }}</td>
                                        <td>{{ rand(10, 100) }}</td>
                                    </tr>
                                @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Card 3 -->
                <div class="card mb-4">
                    <div class="card-header">
                        Malware Types
                    </div>
                    <div class="card-body">
                        <div class="fake-table">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Count</th>
                                </tr>
                                </thead>
                                <tbody>
                                @for ($i = 1; $i <= 5; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>Type {{ $i }}</td>
                                        <td>{{ rand(5, 50) }}</td>
                                    </tr>
                                @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- Additional Content -->
                <div class="card">
                    <div class="card-header">
                        Analysis Comparison
                    </div>
                    <div class="card-body">
                        You can add more content to your dashboard here.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Fake Table Styles (Replace with actual table styles) */
        .fake-table {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
    </style>
@endsection
