<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\DataTables\PlanDataTable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PlanDataTable $DataTable)
    {
        $title = 'Plans';
        $page = 'admin.plan.list';
        $js = ['plans'];
        return $DataTable->render('layouts.admin.layout', compact('title', 'page', 'js'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Add Plan';
        $page = 'admin.plan.add';
        $js = ['plans'];


        return view("layouts.admin.layout", compact(
            'title',
            'page',
            'js'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        try {

            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'min_amount'    => 'required|numeric|min:0',
                'max_amount'    => 'required|numeric|min:0|gte:min_amount',
                'daily_roi'     => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
                'type'          => 'required|in:1,2,3,4,5,6'
            ]);

            $plan = new Plan();
            $plan->name          = $validated['name'];
            $plan->min_amount    = $validated['min_amount'];
            $plan->max_amount    = $validated['max_amount'];
            $plan->daily_roi     = $validated['daily_roi'];
            $plan->duration_days = $validated['duration_days'];
            $plan->type          = $validated['type'];
            $plan->save();

            return redirect()->route('admin.plan_index')->with('msg_success', 'Plan added successfully!');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('msg_error', 'Plan not added' . $e->getMessage());
        }
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
        try {

            $id = decrypt($id);
            $plan = Plan::findOrFail($id);

            $title = 'Edit Plan';
            $page = 'admin.plan.edit';
            $js = ['plans'];


            return view("layouts.admin.layout", compact(
                'title',
                'page',
                'js',
                'plan'
            ));
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('msg_error', 'Plan not fetched' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $id = decrypt($id);
            $plan = Plan::findOrFail($id);
            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'min_amount'    => 'required|numeric|min:0',
                'max_amount'    => 'required|numeric|min:0|gte:min_amount',
                'daily_roi'     => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
                'type'          => 'required|in:1,2,3,4,5,6'
            ]);

            $plan->name          = $validated['name'];
            $plan->min_amount    = $validated['min_amount'];
            $plan->max_amount    = $validated['max_amount'];
            $plan->daily_roi     = $validated['daily_roi'];
            $plan->duration_days = $validated['duration_days'];
            $plan->type          = $validated['type'];

            $plan->save();

            return redirect()->route('admin.plan_index')->with('msg_success', 'Plan updated successfully!');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('msg_error', 'Plan not Updated' . $e->getMessage());
        }
    }
    public function updateStatus(Request $request)
    {
        try {
            $plan = Plan::findOrFail($request->id);
            $plan->status = $request->status;
            $plan->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return redirect()->back()->with('msg_success', 'Plan deleted successfully!');
    }
}
