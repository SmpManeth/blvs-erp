<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnquiryInteraction;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $interactions = EnquiryInteraction::where('enquiry_id', $id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($interaction) {
                return [
                    'id' => (string) $interaction->id,
                    'enquiryId' => (string) $interaction->enquiry_id,
                    'type' => $interaction->type,
                    'agentId' => (string) $interaction->agent_id,
                    'title' => $interaction->title,
                    'description' => $interaction->description,
                    'metadata' => $interaction->metadata ?? null,
                    'createdAt' => $interaction->created_at->toIso8601String(),
                ];
            });

        return response()->json($interactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
