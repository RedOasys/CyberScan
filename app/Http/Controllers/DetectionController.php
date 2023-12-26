<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use PDF;

class DetectionController extends Controller
{
    public function index()
    {
        $detections = Detection::all(); // Replace with your logic to fetch detections
        return view('analysis.detections', compact('detections'));
    }

    public function exportPdf()
    {
        $detections = Detection::all();
        $pdf = PDF::loadView('pdf.detections', compact('detections')); // Ensure you have a view for the PDF
        return $pdf->download('detections.pdf');
    }
}
