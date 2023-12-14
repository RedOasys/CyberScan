<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'file_name', 'file_path', 'md5_hash', 'file_size_kb'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staticAnalysis()
    {
        return $this->hasOne(StaticAnalysis::class);
    }
}
