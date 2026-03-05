<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'status',
    ];

    public function projects()
    {
        return $this->hasMany(AgencyProject::class, 'client_id');
    }
}
