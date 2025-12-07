<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebPages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class TermConditionController extends Controller
{
    public function index()
    {
        $title = 'Add Term Conditions';
        $page = 'admin.term_condition.add';
        $term_condition = WebPages::where('type', "1")->first();
        $js = ['web_pages'];

        return view("layouts.admin.layout", compact(
            'title',
            'page',
            'js',
            'term_condition'
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

            $term_condition = new WebPages();
            $term_condition->title = $request->title;
            $term_condition->content = $request->description;
            $term_condition->type = '1';
            $term_condition->save();
            DB::commit();
            return redirect()->route('admin.term_conditions')->with('msg_success', 'Term & Conditions Updated successfully !');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('admin.term_conditions')->with('msg_error', 'Category not added' . $e->getMessage());
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
            $term_condition = WebPages::where('id', $request->id)->first();
            $term_condition->title = $request->title;
            $term_condition->content = $request->description;
            $term_condition->type = '1';
            $term_condition->save();
            DB::commit();
            return redirect()->route('admin.term_conditions')->with('msg_success', 'Term & Conditions Updated successfully !');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('admin.term_conditions')->with('msg_error', 'Category not added' . $e->getMessage());
        }
    }
}
