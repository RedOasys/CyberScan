<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FileUploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'files' => [
                'required',
                'array',
                'max:10', // Limit to 10 files
            ],
            'files.*' => [
                'file',
                function ($attribute, $file, $fail) {
                    $allowedExtensions = ['exe'];
                    if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions)) {
                        $fail('Only .exe files are allowed.');
                    }
                },
            ],
        ]);

        $files = $request->file('files');
        $uploadedCount = 0;
        $uploadedFiles = [];
        $skippedFiles = [];

        foreach ($files as $file) {
            $md5Hash = md5_file($file);

            // Check if file is already uploaded
            $duplicate = \App\Models\FileUpload::where('md5_hash', $md5Hash)->first();
            if ($duplicate) {
                $skippedFiles[] = $file->getClientOriginalName() . ' (Duplicate)';
                continue; // Skip this file as it's a duplicate
            }

            // Check if reached the upload limit
            if ($uploadedCount >= 10) {
                $skippedFiles[] = $file->getClientOriginalName() . ' (Maximum files reached)';
                continue; // Skip this file as the limit is reached
            }

            // Construct the new file name
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $uploaderName = str_replace(' ', '_', auth()->user()->name); // Replace spaces with underscores
            $date = date('dmY');
            $newFileName = "{$originalName}_{$md5Hash}_{$uploaderName}_{$date}.{$file->getClientOriginalExtension()}";

            $path = $file->storeAs('uploads', $newFileName);

            // Save file information to the database
            \App\Models\FileUpload::create([
                'user_id' => auth()->id(),
                'file_name' => $newFileName,
                'file_path' => $path,
                'md5_hash' => $md5Hash,
                'file_size_kb' => $file->getSize() / 1024,
            ]);

            $uploadedFiles[] = $file->getClientOriginalName();
            $uploadedCount++;
        }

        return response()->json([
            'uploaded' => $uploadedFiles,
            'skipped' => $skippedFiles,
            'message' => $uploadedCount > 0 ? 'Files uploaded successfully.' : 'No files uploaded.',
        ]);
    }
}
