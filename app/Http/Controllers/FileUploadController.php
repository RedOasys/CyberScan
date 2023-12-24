<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use Illuminate\Http\Request;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        $md5Hash = md5_file($file->getRealPath());

        // Check if the file already exists in the database
        $existingFile = FileUpload::where('md5_hash', $md5Hash)->first();
        if ($existingFile) {
            // File already exists, return a response or handle as needed
            return response()->json(['message' => 'File already exists', 'id' => $existingFile->id]);
        }

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Use the username instead of the user ID
        $username = Auth::user()->username; // Replace with your username field

        // Truncate the original filename to the first 25 characters if it's longer than 25
        $name = strlen($originalName) > 25 ? substr($originalName, 0, 25) . 'xxx' : $originalName;

        // Construct the new filename
        $filename = $name . '_' . $username . '_' . $md5Hash . '.' . $extension;

        // Store the file
        $path = $file->storeAs('uploads', $filename, 'public');
        $fileSize = $file->getSize();

        // Save file details to the database
        $upload = FileUpload::create([
            'user_id' => Auth::id(),
            'file_name' => $filename,
            'file_path' => $path,
            'md5_hash' => $md5Hash,
            'file_size_kb' => $fileSize / 1024,
        ]);


        return response()->json(['id' => $upload->id]);
    }






}
