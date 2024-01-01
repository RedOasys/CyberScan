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
        $start = $request->input('start'); // Starting point of records
        $length = $request->input('length'); // Number of records to fetch
        $searchValue = $request->input('search.value'); // Get the search value
        $order = $request->input('order', []);

        // Build the initial query
        $query = FileUpload::with('staticAnalysis')
            ->leftJoin('static_analyses', 'file_uploads.id', '=', 'static_analyses.file_upload_id')
            ->select('file_uploads.*', 'static_analyses.id as analysis_id');

        // Filter query based on the search value
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('file_uploads.file_name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('file_uploads.md5_hash', 'LIKE', "%{$searchValue}%");
            });
        }

        // Get filtered count
        $totalFilteredRecords = $query->count();

        // Apply pagination and get results
        $files = $query->skip($start)->take($length)->get();

        // Map the data for DataTables
        $data = $files->map(function ($file) {
            $analysis = $file->staticAnalysis; // Get the related StaticAnalysis object
            $analysisId = $analysis ? $analysis->id : 'N/A'; // Check for null

            return [
                'DT_RowId' => 'row_' . $file->id,
                'file_id' => $file->id,
                'file_name' => $file->file_name,
                'md5_hash' => $file->md5_hash,
                'file_size_kb' => $file->file_size_kb,
                'actions' => $analysis ? view('partials.analysis_actions', compact('file', 'analysis'))->render() : 'No Actions'
            ];
        });

        if (!empty($order)) {
            $orderColumnIndex = $order[0]['column']; // Column index
            $orderDirection = $order[0]['dir']; // asc or desc

            // Mapping column index to database column names
            $columns = ['file_id', 'file_name', 'md5_hash', 'file_size_kb']; // Adjust this array based on your actual columns
            $orderColumn = $columns[$orderColumnIndex];

            $query->orderBy($orderColumn, $orderDirection);
        }

        $totalRecords = FileUpload::count();

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFilteredRecords,
            "data" => $data
        ]);
    }


}
