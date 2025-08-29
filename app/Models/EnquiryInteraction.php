<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryInteraction extends Model
{
    /** @use HasFactory<\Database\Factories\EnquiryInteractionFactory> */
    use HasFactory;

    protected $fillable = ['enquiry_id','type','agent_id','title','description','metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }
}
