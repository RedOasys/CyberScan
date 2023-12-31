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
        $start = $request->input('start' ); // Starting point of records
        $length = $request->input('length' ); // Number of records to fetch
        $searchValue = $request->input('search.value'); // Get the search value

        // Build the initial query
        $query = Detection::with('fileUpload')
            ->orderBy('id', 'desc');

        // Filter query based on the search value
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->whereHas('fileUpload', function ($q) use ($searchValue) {
                    $q->where('file_name', 'like', '%' . $searchValue . '%');
                });
            });
        }

        // Get total count of records
        $recordsTotal = Detection::count();
        $recordsFiltered = $query->count();

        // Apply pagination manually
        $detections = $query->skip($start)->take($length)->get();

        // Map the data for DataTables
        $data = $detections->map(function ($detection) {
            return [
                'file_name' => $detection->fileUpload ? $detection->fileUpload->file_name : 'N/A',
                'analysis_id' => $detection->analysis_id,
                'detectionStatus' => $detection->detected ? 'Detected' : 'Undetected',
                'malware_type' => $detection->malware_type,
                'certainty' => $detection->certainty,
                'source' => $detection->source,
            ];
        });

        // Return JSON response
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }


}
