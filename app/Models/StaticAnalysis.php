<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_upload_id',
        'analysis_id',
        'score',
        'kind',
        'state',
        'media_type',
        'md5',
        'sha1',
        'sha256',

    ];

    public function fileUpload()
    {
        return $this->belongsTo(FileUpload::class);
    }
    public function staticAnalysis()
    {
        return $this->hasOne(StaticAnalysis::class, 'file_upload_id');
    }

}
