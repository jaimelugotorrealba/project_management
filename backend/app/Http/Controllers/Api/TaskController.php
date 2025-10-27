<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Task\StoreRequest;
use App\Http\Requests\Api\Task\UpdateRequest;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['assignee', 'project']);

        if ($request->has('user_id')) {
            $userId = $request->input('user_id');


            $userRole = User::where('id', $userId)->value('role');

            if ($userRole === 'dev') {

                $query->where('assigned_to', $userId);
            }

        }

        $tasks = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $tasks
        ], 200);
    }


    public function store(StoreRequest $request)
    {
        $data = array_merge($request->validated(), ['status' => 'pending', 'weight' => 0]);
        $task = Task::create($data);
        $this->updatedProject($task);
        return response()->json([
            'success' => true,
            'message' => 'Tarea creada exitosamente',
            'data' => $task,
        ], 201);
    }

    public function show(Task $task)
    {
        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    public function update(UpdateRequest $request, Task $task)
    {
        if ($request->status === 'completed') {
            $weight = 1;
        } else {
            $weight = 0;
        }
        $data = array_merge($request->validated(), ['weight' => $weight]);
        $task->update($data);
        $this->updatedProject($task);
        return response()->json([
            'success' => true,
            'message' => 'Tarea actualizada exitosamente',
            'data' => $task,
        ]);
    }
    public function destroy(Task $task)
    {
        $project = $task->project()->first();

        $task->delete();
        $this->updatedProjectByModel($project);
        return response()->json([
            'success' => true,
            'message' => 'Tarea eliminada exitosamente',
        ]);
    }

    private function updatedProject(Task $task)
    {
        $project = Project::with('tasks')->find($task->project_id);

        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'completed')->count();

        $project->progress = $totalTasks ? round(($completedTasks / $totalTasks) * 100) : 0;
        $project->saveQuietly();
    }

    private function updatedProjectByModel(Project $project)
    {

        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'completed')->count();

        $project->progress = $totalTasks ? round(($completedTasks / $totalTasks) * 100) : 0;
        $project->saveQuietly();
    }
}
