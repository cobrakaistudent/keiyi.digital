<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoutInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'detected_trends',
        'recommended_actions',
        'raw_sources_used',
    ];

    protected $casts = [
        'detected_trends' => 'array',
        'recommended_actions' => 'array',
        'report_date' => 'date',
    ];
}
