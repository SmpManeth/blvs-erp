<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Call;

class CallController extends Controller
{
    public function index($enquiryId)
    {
        return response()->json(
            Call::where('enquiry_id', $enquiryId)->get()
        );
    }
}
