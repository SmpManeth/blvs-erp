<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class AgentController extends Controller
{
    public function index()
    {
        $agents = User::where('role', 'agent')
            ->select('id','name','email','role')
            ->get()
            ->map(function ($agent) {
                return [
                    'id' => (string) $agent->id,
                    'name' => $agent->name,
                    'email' => $agent->email,
                    'role' => $agent->role,
                    'isActive' => true, // you can later add a real flag
                ];
            });

        return response()->json($agents);
    }
}
