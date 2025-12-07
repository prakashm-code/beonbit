<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\WebPages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        $title = 'Add Privacy Policy';
        $page = 'admin.privacy_policy.add';
        $privacy_policy = WebPages::where('type', "0")->first();
        $js = ['web_pages'];

        return view("layouts.admin.layout", compact(
            'title',
            'page',
            'js',
            'privacy_policy'
        ));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
            ]);

            DB::beginTransaction();

            $privacy_policy = new WebPages();
            $privacy_policy->title = $request->title;
            $privacy_policy->content = $request->description;
            $privacy_policy->type = '0';
            $privacy_policy->save();
            DB::commit();
            return redirect()->route('admin.privacy_policy')->with('msg_success', 'Privacy & Policy Updated successfully !');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('admin.privacy_policy')->with('msg_error', 'Privacy & Policy not added' . $e->getMessage());
        }
    }

    public function edit(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
            ]);
            DB::beginTransaction();
            $privacy_policy = WebPages::where('id', $request->id)->first();
            $privacy_policy->title = $request->title;
            $privacy_policy->content = $request->description;
            $privacy_policy->type = '0';
            $privacy_policy->save();
            DB::commit();
            return redirect()->route('admin.privacy_policy')->with('msg_success', 'Privacy & Policy Updated successfully !');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('admin.privacy_policy')->with('msg_error', 'Privacy & Policy not added' . $e->getMessage());
        }
    }
}
