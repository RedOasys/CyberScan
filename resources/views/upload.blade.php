@extends('layouts.app')
@section('content')
<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
    <!-- Add your stylesheets here -->
</head>
<body>
    <div class="container">
        <h1>Upload a File for Malware Analysis</h1>
        <form action="{{ route('upload.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" required>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
@endsection