<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        // Check if files are uploaded
        if (!$request->hasFile('files')) {
            return response()->json(['error' => 'No files provided.'], 400);
        }

        $files = $request->file('files');
        $totalSize = array_sum(array_map(function ($file) { return $file->getSize(); }, $files));

        if ($totalSize > 100 * 1024 * 1024) {
            return response()->json(['error' => 'Total file size exceeds 100MB.'], 400);
        }

        $uploadedFiles = [];
        $skippedFiles = [];

        foreach ($files as $file) {
            if ($file->getClientOriginalExtension() == 'exe') {
                $md5Hash = md5_file($file);

                // Check if the file's MD5 hash already exists in the database
                $duplicate = \App\Models\FileUpload::where('md5_hash', $md5Hash)->first();
                if ($duplicate) {
                    $skippedFiles[] = $file->getClientOriginalName() . ' (Duplicate)';
                    continue; // Skip this file as it's a duplicate
                }

                // Construct the new file name
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $uploaderName = str_replace(' ', '_', auth()->user()->name); // Replace spaces with underscores
                $date = date('dmY');
                $newFileName = "{$originalName}_{$md5Hash}_{$uploaderName}_{$date}.exe";

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
            } else {
                $skippedFiles[] = $file->getClientOriginalName() . ' (Invalid Type)';
            }
        }

        return response()->json([
            'uploaded' => $uploadedFiles,
            'skipped' => $skippedFiles,
        ]);
    }
}
