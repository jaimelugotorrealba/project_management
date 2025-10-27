<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Project\StoreRequest;
use App\Http\Requests\Api\Project\UpdateRequest;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user && $user->role === 'dev') {
            $query = Project::where('user_id', $user->id)->with('user');
        } else {
            $query = Project::with('user');
        }
        if ($request->filled('sort_by') && $request->filled('sort_dir')) {
            $sortBy = $request->sort_by;
            $sortDir = $request->sort_dir;

            if ($sortBy === 'user') {
                $query = $query->join('users', 'projects.user_id', '=', 'users.id')
                    ->orderBy('users.first_name', $sortDir);
            } else {
                $query = $query->orderBy($sortBy, $sortDir);
            }
        }

        $projects = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $projects
        ], 200);
    }

    public function getAllProjects(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user && $user->role === 'dev') {
            $query = Project::where('user_id', $user->id)
                ->select('id', 'name')
                ->with('user')
                ->get();
        } else {
            $query = Project::select('id', 'name')->get();
        }

        return response()->json([
            'success' => true,
            'data' => $query
        ], 200);
    }


    public function store(StoreRequest $request)
    {
        $project = Project::create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Proyecto creado exitosamente',
            'data' => $project
        ], 201);
    }
    public function show(Project $project)
    {
        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }
    public function update(UpdateRequest $request, Project $project)
    {
        $project->update($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Proyecto actualizado exitosamente',
            'data' => $project,
        ]);
    }
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json([
            'success' => true,
            'message' => 'Project eliminado exitosamente',
        ]);
    }
}
