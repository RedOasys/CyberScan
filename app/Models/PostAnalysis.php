<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAnalysis extends Model
{
    protected $table = 'post_analyses'; // Specify the table name


    protected $fillable = [
        'static_analysis_id', // Update the column name
        'data',
    ];
    // Define other model properties and methods as needed
}
