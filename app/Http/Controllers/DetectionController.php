<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use App\Models\FileUpload;
use Illuminate\Http\Request;

class DetectionController extends Controller
{
    public function index()
    {
        $detections = Detection::with('fileUpload')->get()->map(function ($detection) {
            $detection->fileName = FileUpload::find($detection->file_upload_id)->file_name;
            $detection->detectionStatus = $detection->detected ? 'Detected' : 'Undetected';
            return $detection;
        });

        return view('analysis.detections', compact('detections'));

    }

    public function detectionsData(Request $request)
    {
        // Fetch parameters from DataTables request
        $start = $request->input('start', );
        $length = $request->input('length', );
        $searchValue = $request->input('search.value'); // Get the search value

        // Build the query
        $query = Detection::withCount('fileUpload')
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->whereHas('fileUpload', function ($q) use ($searchValue) {
                    $q->where('file_name', 'like', '%' . $searchValue . '%');
                });
            });

        // Get total count of records
        $recordsTotal = Detection::count();
        $recordsFiltered = $query->count();

        // Explicitly set the perPage option for pagination
        $detections = $query->paginate($length);

        // Map the data for DataTables
        $data = $detections->map(function ($detection) {
            return [
                'file_name' => $detection->fileUpload ? $detection->fileUpload->file_name : 'N/A',
                'analysis_id' => $detection->analysis_id,
                'detectionStatus' => $detection->detected ? 'Detected' : 'Undetected',
                'malware_type' => $detection->malware_type,
                'certainty' => $detection->certainty,
                'source' => $detection->source
            ];
        });

        // Return JSON response
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }


}
