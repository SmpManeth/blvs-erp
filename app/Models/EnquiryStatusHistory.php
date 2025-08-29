<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryStatusHistory extends Model
{
    /** @use HasFactory<\Database\Factories\EnquiryStatusHistoryFactory> */
    use HasFactory;

    protected $fillable = ['enquiry_id','previous_status','new_status','changed_by','notes','changed_at'];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }
}
