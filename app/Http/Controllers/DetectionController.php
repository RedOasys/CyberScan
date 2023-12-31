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
        $searchValue = $request->input('search.value'); // Get the search value

        // Build the query
        $query = Detection::with('fileUpload')
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->whereHas('fileUpload', function ($q) use ($searchValue) {
                    $q->where('file_name', 'like', '%' . $searchValue . '%');
                });
            });

        // Get total count of records
        $recordsTotal = Detection::count();
        $recordsFiltered = $query->count();

        // Retrieve all detections without pagination
        $detections = $query->get();

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

        // Return JSON response with all data
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }


}
