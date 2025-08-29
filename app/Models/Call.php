<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    /** @use HasFactory<\Database\Factories\CallFactory> */
    use HasFactory;
    protected $fillable = [
        'customer_name','customer_phone','agent_id',
        'type','status','duration','notes','enquiry_id','date_time'
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }
}
