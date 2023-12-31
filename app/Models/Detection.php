<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detection extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_upload_id',
        'analysis_id',
        'detected',
        'malware_type',
        'certainty',
        'source',
    ];

    public function fileUpload()
    {
        return $this->belongsTo(FileUpload::class, 'file_upload_id');
    }

}
