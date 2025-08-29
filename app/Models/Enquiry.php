<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    /** @use HasFactory<\Database\Factories\EnquiryFactory> */
    use HasFactory;
    protected $fillable = [
        'customer_name',
        'email',
        'phone',
        'source',
        'utm_data',
        'assigned_agent',
        'status',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'utm_data' => 'array',
        'created_at' => 'datetime:c',
        'updated_at' => 'datetime:c',
    ];
    

    public function interactions()
    {
        return $this->hasMany(EnquiryInteraction::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(EnquiryStatusHistory::class);
    }

    public function calls()
    {
        return $this->hasMany(Call::class);
    }
}
