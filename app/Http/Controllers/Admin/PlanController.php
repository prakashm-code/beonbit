<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;use App\Http\Controllers\Controller;


use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'min_amount'    => 'required|numeric|min:0',
            'max_amount'    => 'required|numeric|min:0|gte:min_amount',
            'daily_roi'     => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'total_return'  => 'required|numeric|min:0',
            'status'        => 'required|in:1,0',
            'type'          => 'required|in:1,2,3,4,5,6'
        ]);

        $plan = new Plan();
        $plan->name          = $validated['name'];
        $plan->description   = $validated['description'] ?? null;
        $plan->min_amount    = $validated['min_amount'];
        $plan->max_amount    = $validated['max_amount'];
        $plan->daily_roi     = $validated['daily_roi'];
        $plan->duration_days = $validated['duration_days'];
        $plan->total_return  = $validated['total_return'];
        $plan->status        = $validated['status'];
        $plan->type          = $validated['type'];
        $plan->save();

        return redirect()->back()->with('success', 'Plan added successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the plan
        $plan = Plan::findOrFail($id);

        // Validate request
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'min_amount'    => 'required|numeric|min:0',
            'max_amount'    => 'required|numeric|min:0|gte:min_amount',
            'daily_roi'     => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'total_return'  => 'required|numeric|min:0',
            'status'        => 'required|in:1,0',
            'type'          => 'required|in:1,2,3,4,5,6'
        ]);

        // Update data
        $plan->name          = $validated['name'];
        $plan->description   = $validated['description'] ?? null;
        $plan->min_amount    = $validated['min_amount'];
        $plan->max_amount    = $validated['max_amount'];
        $plan->daily_roi     = $validated['daily_roi'];
        $plan->duration_days = $validated['duration_days'];
        $plan->total_return  = $validated['total_return'];
        $plan->status        = $validated['status'];
        $plan->type          = $validated['type'];

        $plan->save();

        return redirect()->back()->with('success', 'Plan updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return redirect()->back()->with('success', 'Plan deleted successfully!');
    }
}
