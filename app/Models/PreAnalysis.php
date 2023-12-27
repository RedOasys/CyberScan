<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'static_analysis_id',
        'pe_id_signatures',
        'pe_imports',
        'pe_sections',
        'pe_resources',
        'pe_version_info',
        'pe_timestamp',
        'signatures',
        'errors',
    ];

    public function staticAnalysis()
    {
        return $this->belongsTo(StaticAnalysis::class);
    }
}
