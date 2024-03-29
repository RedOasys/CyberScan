<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreAnalysis extends Model
{
    protected $table = 'pre_analyses'; // Specify the table name


    protected $fillable = [
        'static_analysis_id', // Update the column name
        'data',
    ];
    // Define other model properties and methods as needed
}
