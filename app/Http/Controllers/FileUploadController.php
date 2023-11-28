<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;
class FileUploadController extends Controller
{
    public function index(){
        return view('upload');
    }
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048', // Update validation rules as needed
        ]);
    
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
    
        // Store the file in the storage/app/public/uploads directory
        $filePath = $file->storeAs('uploads', $fileName, 'public');
    
        // Save file details in the database
        FileUpload::create([
            'user_id' => auth()->id(),
            'file_name' => $fileName,
            'file_path' => $filePath,
        ]);
    
        return back()->with('success', 'File uploaded successfully.');
    }
    
}
