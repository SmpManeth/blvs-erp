<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryStatusHistory;
use App\Models\EnquiryInteraction;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    /**
     * GET /api/enquiries
     */
    public function index(Request $request)
    {
        $query = Enquiry::query();

        // 🔍 Search
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        // 🏷️ Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->source) {
            $query->where('source', $request->source);
        }

        if ($request->assignedAgent) {
            $query->where('assigned_agent', $request->assignedAgent);
        }

        if ($request->dateFrom) {
            $query->whereDate('created_at', '>=', $request->dateFrom);
        }

        if ($request->dateTo) {
            $query->whereDate('created_at', '<=', $request->dateTo);
        }

        // 📊 Sorting
        $sortBy = $request->get('sortBy', 'created_at');
        $sortOrder = $request->get('sortOrder', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // 📄 Pagination
        $pageSize = $request->get('pageSize', 50);
        $data = $query->paginate($pageSize);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'page' => $data->currentPage(),
            'pageSize' => $data->perPage()
        ]);
    }

    /**
     * POST /api/enquiries
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customerName' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'source' => 'required|string',
            'assignedAgent' => 'nullable|integer|exists:users,id',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'utmData' => 'nullable|array'
        ]);

        $enquiry = Enquiry::create([
            'customer_name' => $validated['customerName'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'source' => $validated['source'],
            'assigned_agent' => $validated['assignedAgent'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'utm_data' => $validated['utmData'] ?? null,
            'created_by' => auth()->id()
        ]);

        // Initialize status history
        EnquiryStatusHistory::create([
            'enquiry_id' => $enquiry->id,
            'previous_status' => null,
            'new_status' => $enquiry->status,
            'changed_by' => auth()->id(),
            'notes' => 'Initial status',
        ]);

        return response()->json($enquiry, 201);
    }

    /**
     * GET /api/enquiries/{id}
     */
    public function show(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        // Handle ?include=statusHistory,interactions,calls
        $includes = explode(',', $request->get('include', ''));

        if (in_array('statusHistory', $includes)) {
            $enquiry->load('statusHistory');
        }
        if (in_array('interactions', $includes)) {
            $enquiry->load('interactions');
        }
        if (in_array('calls', $includes)) {
            $enquiry->load('calls');
        }

        return response()->json($enquiry);
    }

    /**
     * PATCH /api/enquiries/{id}
     */
    public function update(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $data = $request->all();
        $oldStatus = $enquiry->status;

        $enquiry->fill([
            'customer_name' => $data['customerName'] ?? $enquiry->customer_name,
            'email' => $data['email'] ?? $enquiry->email,
            'phone' => $data['phone'] ?? $enquiry->phone,
            'source' => $data['source'] ?? $enquiry->source,
            'assigned_agent' => $data['assignedAgent'] ?? $enquiry->assigned_agent,
            'status' => $data['status'] ?? $enquiry->status,
            'notes' => $data['notes'] ?? $enquiry->notes,
            'utm_data' => $data['utmData'] ?? $enquiry->utm_data,
        ]);

        $enquiry->updated_at = now();
        $enquiry->save();

        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            // Add status history
            EnquiryStatusHistory::create([
                'enquiry_id' => $enquiry->id,
                'previous_status' => $oldStatus,
                'new_status' => $data['status'],
                'changed_by' => auth()->id(),
            ]);

            // Add timeline interaction
            EnquiryInteraction::create([
                'enquiry_id' => $enquiry->id,
                'type' => 'status_change',
                'agent_id' => auth()->id(),
                'title' => "Status changed to {$data['status']}",
                'description' => null,
            ]);
        }

        return response()->json($enquiry);
    }

    /**
     * DELETE /api/enquiries/{id}
     */
    public function destroy($id)
    {
        Enquiry::findOrFail($id)->delete();
        return response()->noContent();
    }
}
