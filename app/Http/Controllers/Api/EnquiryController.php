<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::query();

        // 🔍 Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->agentId) {
            $query->where('assigned_agent', $request->agentId);
        }
        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customerName' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'source' => ['required', Rule::in(['website','google_ads','meta','referral','walk_in','other'])],
            'utmData' => 'nullable|array',
            'assignedAgent' => 'nullable|string',
            'status' => ['required', Rule::in(['new','contacted','in_progress','closed_won','closed_lost','spam'])],
            'notes' => 'nullable|string',
        ]);

        $enquiry = Enquiry::create([
            'customer_name' => $data['customerName'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'source' => $data['source'],
            'utm_data' => $data['utmData'] ?? null,
            'assigned_agent' => $data['assignedAgent'] ?? null,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($enquiry, 201);
    }

    public function update(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $data = $request->validate([
            'customerName' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:50',
            'source' => ['sometimes', Rule::in(['website','google_ads','meta','referral','walk_in','other'])],
            'utmData' => 'sometimes|array',
            'assignedAgent' => 'sometimes|string',
            'status' => ['sometimes', Rule::in(['new','contacted','in_progress','closed_won','closed_lost','spam'])],
            'notes' => 'sometimes|string',
        ]);

        $enquiry->update([
            'customer_name' => $data['customerName'] ?? $enquiry->customer_name,
            'email' => $data['email'] ?? $enquiry->email,
            'phone' => $data['phone'] ?? $enquiry->phone,
            'source' => $data['source'] ?? $enquiry->source,
            'utm_data' => $data['utmData'] ?? $enquiry->utm_data,
            'assigned_agent' => $data['assignedAgent'] ?? $enquiry->assigned_agent,
            'status' => $data['status'] ?? $enquiry->status,
            'notes' => $data['notes'] ?? $enquiry->notes,
        ]);

        return response()->json($enquiry);
    }

    public function destroy($id)
    {
        Enquiry::findOrFail($id)->delete();
        return response()->noContent();
    }
}
