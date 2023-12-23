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
    public function fetchAllFiles(Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');

        // Query for getting total records
        $totalQuery = FileUpload::where('user_id', Auth::id());
        $totalRecords = $totalQuery->count();

        // Query for getting filtered data
        $filesQuery = FileUpload::where('user_id', Auth::id())
            ->leftJoin('static_analyses', 'file_uploads.id', '=', 'static_analyses.file_upload_id')
            ->select('file_uploads.*', 'static_analyses.id as analysis_id')
            ->with('staticAnalysis');

        // Apply search filter
        if (!empty($search)) {
            $filesQuery->where(function($query) use ($search) {
                $query->where('file_uploads.file_name', 'LIKE', "%{$search}%")
                    ->orWhere('file_uploads.md5_hash', 'LIKE', "%{$search}%");
            });
        }

        $totalFilteredRecords = $filesQuery->count();

        // Apply pagination
        $files = $filesQuery->skip($start)->take($length)->get();

        // Map data for DataTables
        $data = $files->map(function ($file) {
            $analysis = $file->staticAnalysis; // Get the related StaticAnalysis object
            return [
                'file_id' => $file->id,

                'file_name' => $file->file_name,
                'md5_hash' => $file->md5_hash,
                'file_size_kb' => $file->file_size_kb,

                'actions' => view('partials.analysis_actions', compact('file', 'analysis'))->render()
            ];
        });

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFilteredRecords,
            "data" => $data
        ]);
    }


}
