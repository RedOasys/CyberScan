@extends('layouts.chips.main')

@section('content')
    <div class="container mt-4">
        <h2>Create Analysis Task</h2>
        <p>Select a file and set parameters for analysis.</p>

        {{-- Display success/error message --}}
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        {{-- Form for creating a task --}}
        <form action="{{ route('analysis.tasks.submit') }}" method="POST">
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

            teststststst
            {{-- Analysis timeout --}}
            <div class="mb-3">
                <label for="analysisTimeout" class="form-label">Analysis Timeout (in seconds)</label>
                <input type="number" class="form-control" id="analysisTimeout" name="timeout" placeholder="Enter timeout in seconds" value="120"> <!-- Default timeout -->
            </div>

            {{-- Machine selection --}}
            <div class="mb-3">
                <label for="machineSelection" class="form-label">Machine Selection</label>
                <select class="form-select" id="machineSelection" name="machine">
                    <option value="default" selected>Default</option>
                    <!-- Add more machine options as required -->
                </select>
            </div>

            {{-- Additional options --}}
            <div class="mb-3">
                <label for="additionalOptions" class="form-label">Additional Options</label>
                <textarea class="form-control" id="additionalOptions" name="options" rows="3" placeholder="Enter any additional options"></textarea>
            </div>

            {{-- Submit button --}}
            <button type="submit" class="btn btn-primary">Submit Task</button>
        </form>
    </div>
@endsection
