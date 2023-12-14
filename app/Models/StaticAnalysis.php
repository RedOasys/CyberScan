<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticAnalysis extends Model
{
    use HasFactory;

    protected $fillable = ['file_upload_id', 'analysis_id', 'score', 'details'];

    public function fileUpload()
    {
        return $this->belongsTo(FileUpload::class);
    }
}
