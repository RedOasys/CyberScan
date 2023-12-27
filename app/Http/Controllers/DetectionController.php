<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use App\Models\FileUpload;

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

}
