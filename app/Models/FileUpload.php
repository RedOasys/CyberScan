<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $fillable = ['user_id', 'file_name', 'file_path'];

    // Define the relationship with the User model, if necessary
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


