<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload; // Import your FileUpload model
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Fetch recent uploads from the file_uploads table
        $recentUploads = FileUpload::orderBy('created_at', 'desc')->take(5)->get();

        // Fetch data for $fileSizeAnalysisLabels from your database
        $fileSizeAnalysisLabels = DB::table('file_uploads')->pluck('file_name'); // Change 'file_name' to the appropriate column name

        // Fetch data for $fileSizeAnalysisData from your database
        $fileSizeAnalysisData = DB::table('file_uploads')->pluck('file_size_kb'); // Change 'file_size_kb' to the appropriate column name

        // Pass all three sets of data to the view
        return view('home', compact('recentUploads', 'fileSizeAnalysisLabels', 'fileSizeAnalysisData'));
    }

}
