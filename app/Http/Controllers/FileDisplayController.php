<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FileDisplayController extends Controller
{
    public function index()
    {
        // Fetch the recent files for the authenticated user
        $recentFiles = FileUpload::where('user_id', Auth::id())
            ->latest('created_at')
            ->take(10) // Get 10 recent files
            ->get();

        // Fetch the total size of all files for the authenticated user
        $totalSizeKB = FileUpload::where('user_id', Auth::id())
            ->sum('file_size_kb'); // Sum of file sizes in KB

        $totalSizeGB = number_format($totalSizeKB / 1024, 2); // Convert KB to GB

        return view('files', [
            'recentFiles' => $recentFiles,
            'totalSizeGB' => $totalSizeGB
        ]);
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
