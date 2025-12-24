<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\ReferralSettingDataTable;
use App\Models\ReferralSetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    public function referralSetting(ReferralSettingDataTable $DataTable)
    {
        $title = 'Referral Setting';
        $page = 'admin.referral_setting.list';
        $js = ['referral'];
        return $DataTable->render('layouts.admin.layout', compact('title', 'page', 'js'));
    }

    public function create()
    {
        $title = 'Add Referral Setting';
        $page = 'admin.referral_setting.add';
        $js = ['referral'];


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
        try {
            $validated = $request->validate([
                'level'          => 'required',
                'percentage'    => 'required',
            ]);

            $level = new ReferralSetting();
            $level->level          = $validated['level'];
            $level->percentage    = $validated['percentage'];
            $level->save();

            return redirect()->route('admin.referral_setting')->with('msg_success', 'Commission added successfully!');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('msg_error', 'Commission not added' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            $id = decrypt($id);
            $level = ReferralSetting::findOrFail($id);

            $title = 'Edit Referral Setting';
            $page = 'admin.referral_setting.edit';
            $js = ['referral'];

            return view("layouts.admin.layout", compact(
                'title',
                'page',
                'js',
                'level'
            ));
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('msg_error', 'Commission not fetched' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $id = decrypt($id);
            $level = ReferralSetting::findOrFail($id);
            $validated = $request->validate([
                'level'          => 'required',
                'percentage'    => 'required',
            ]);

            $level->level          = $validated['level'];
            $level->percentage    = $validated['percentage'];

            $level->save();

            return redirect()->route('admin.referral_setting')->with('msg_success', 'Commission updated successfully!');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('msg_error', 'Commission not Updated' . $e->getMessage());
        }
    }
    public function updateStatus(Request $request)
    {
        try {
            $plan = ReferralSetting::findOrFail($request->id);
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
        $id = decrypt($id);
        $plan = ReferralSetting::findOrFail($id);
        $plan->delete();

        return redirect()->back()->with('msg_success', 'Commission deleted successfully!');
    }
}
