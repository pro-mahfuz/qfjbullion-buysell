<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Validator;
class ProjectController extends Controller
{
    public function projectList()
    {
        $result = Project::all();
        return view('admin.project.list', compact('result'));
    }


    public function projectCreate()
    {
        return view('admin.project.create');
    }


    public function editProject($id)
    {
        $project = Project::find($id);
        return view('admin.project.edit', compact('project'));
    }

    public function storeProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'estimated_revenue' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // dd($request->all());
        if ($request->hasFile('image')) {
            $image = FileUploadService::handleFileUpload($request, 'image', 'projects/');
        }

        $project = new Project();
        $project->title = $request->title;
        $project->description = $request->description;
        $project->estimated_revenue = $request->estimated_revenue;
        $project->image = $image;
        $project->save();

        return redirect()->route('admin.project.list');
    }


    public function updateProject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'estimated_revenue' => 'required',


        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('image')) {
            $image = FileUploadService::handleFileUpload($request, 'image', 'projects/', $project->image);
        }
        $project = Project::find($id);

        $project->title = $request->title;
        $project->description = $request->description;
        $project->estimated_revenue = $request->estimated_revenue;
        if (isset($image)) {
            $project->image = $image;
        }
        $project->save();

        return redirect()->route('admin.project.list');
    }
}
