<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'deadline',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(AgencyClient::class, 'client_id');
    }
}
