<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FileDisplayController extends Controller
{
    public function index()
    {
        // Fetch the 10 most recent files from all users
        $recentFiles = FileUpload::latest('created_at')
            ->take(10) // Get 10 recent files
            ->get();

        // Fetch the total size of all files in the database
        $totalSizeKB = FileUpload::sum('file_size_kb'); // Sum of file sizes in KB

        $totalSizeGB = number_format($totalSizeKB / 1024, 2); // Convert KB to GB

        return view('files', [
            'recentFiles' => $recentFiles,
            'totalSizeGB' => $totalSizeGB
        ]);
    }


    public function searchFiles(Request $request)
    {
        $search = $request->input('search');

        // Search for filenames containing the search string among all files
        $recentFiles = FileUpload::where('file_name', 'like', '%' . $search . '%')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate the total size in GB for the searched files
        $totalSizeGB = number_format($recentFiles->sum('file_size_kb') / 1024, 2);

        return view('files', ['recentFiles' => $recentFiles, 'totalSizeGB' => $totalSizeGB]);
    }
    public function fetchAllFiles(Request $request)
    {
        $searchValue = $request->input('search.value'); // Get the search value

        // Build the initial query
        $query = FileUpload::with('staticAnalysis')

            ->orderBy('id', 'desc');

        // Filter query based on the search value
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('file_name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('md5_hash', 'LIKE', "%{$searchValue}%");
            });
        }

        $files = $query->get(); // Fetch all files

        // Map the data for DataTables
        $data = $files->map(function ($file) {
            $analysis = $file->staticAnalysis; // Adjust this based on your relationship

            return [
                'DT_RowId' => 'row_' . $file->id,
                'file_id' => $file->id,
                'file_name' => $file->file_name,
                'md5_hash' => $file->md5_hash,
                'file_size_kb' => $file->file_size_kb, // Include file_size_kb in the response
                'analysis_id' => $analysis ? $analysis->id : 'N/A',
                'created_at' => $analysis ? $analysis->created_at : 'N/A',
                'status' => $analysis ? $analysis->state : 'N/A',
                'actions' => $analysis ? view('partials.analysis_actions', compact('file', 'analysis'))->render() : 'No Actions'
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => FileUpload::count(),
            'recordsFiltered' => $files->count(),
            'data' => $data
        ]);
    }


}
