@foreach($files as $file)
    <tr>
        <td>{{ $file->file_name }}</td>
        <td>{{ $file->file_size_kb }}</td>
        <td>
            <button class="btn btn-primary btn-sm" onclick="viewFile({{ $file->id }})">View</button>
            <form action="{{ route('files.delete', $file->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </td>
    </tr>
@endforeach
