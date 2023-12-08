@extends('layouts.app')
@section('title', 'Uploaded Files')

@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col">
                <label for="pageSizeSelect">Items per page:</label>
                <select id="pageSizeSelect" class="form-select" style="width: auto;">
                    @foreach([10, 15, 20, 25, 30, 35, 40, 45, 50] as $size)
                        <option value="{{ $size }}" {{ $size == 10 ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>File Name</th>
                    <th>Size (KB)</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="tableBody">
                <!-- Table rows will be inserted here -->
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button id="prevPage" class="btn btn-primary me-2">Previous Page</button>
            <button id="nextPage" class="btn btn-primary">Next Page</button>
        </div>
    </div>

    <!-- File View Modal -->
    <div class="modal fade" id="fileViewModal" tabindex="-1" aria-labelledby="fileViewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            let currentPage = 1;
            let pageSize = $('#pageSizeSelect').val(); // Set the initial page size value

            function viewFile(id) {
                $.get('{{ url('files/view') }}/' + id, function (data) {
                    $('#fileViewModal .modal-content').html(data);
                    $('#fileViewModal').modal('show');
                });
            }

            function loadTableData(page, size) {
                $.get('{{ route('files.data') }}', { page: page, size: size }, function (data) {
                    $('#tableBody').html(data);
                    // Disable the Previous Page button if the current page is 1
                    $('#prevPage').prop('disabled', currentPage === 1);
                });
            }

            $('#pageSizeSelect').change(function() {
                pageSize = $(this).val();
                currentPage = 1;
                loadTableData(currentPage, pageSize);
            });

            $('#prevPage').click(function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadTableData(currentPage, pageSize);
                }
            });

            $('#nextPage').click(function() {
                currentPage++;
                loadTableData(currentPage, pageSize);
            });

            // Initial load
            loadTableData(currentPage, pageSize);

            window.viewFile = viewFile; // Make the function available globally
        });
    </script>
@endpush
