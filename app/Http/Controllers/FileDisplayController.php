<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FileDisplayController extends Controller
{
    public function index()
    {
        // Fetch the recent files for the authenticated user, ordered by creation date
        $recentFiles = FileUpload::where('user_id', Auth::id())
            ->latest('created_at') // Ensure we're getting the latest files
            ->take(10) // Limit to 10 recent files
            ->get();

        // Calculate the total size of files in GB
        $totalSizeGB = number_format($recentFiles->sum('file_size_kb') / 1024, 2);

        return view('files', ['recentFiles' => $recentFiles, 'totalSizeGB' => $totalSizeGB]);
    }

    public function searchFiles(Request $request)
    {
        $search = $request->input('search');

        $recentFiles = FileUpload::where('user_id', Auth::id())
            ->where('file_name', 'like', '%' . $search . '%') // Search for filenames containing the search string
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate the total size in GB
        $totalSizeGB = number_format($recentFiles->sum('file_size_kb') / 1024, 2);

        return view('files', ['recentFiles' => $recentFiles, 'totalSizeGB' => $totalSizeGB]);

    }
    public function fetchAllFiles()
    {
        $files = FileUpload::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['files' => $files]);
    }


}
