<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function getTotals(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user) {
            if ($user->role === 'dev') {
                $totalProjects = Project::where('user_id',$user->id)->count();
                $totalTasks = Task::where('assigned_to',$user->id)->count();
            } else {
                $totalUsers = User::count();
                $totalProjects = Project::count();
                $totalTasks = Task::count();
            }
        }else{
            $totalProjects = 0;
            $totalTasks = 0;
        }

        $totalUsers = User::count();


        return response()->json([
            'success' => true,
            'totals' => [
                'users' => $totalUsers,
                'projects' => $totalProjects,
                'tasks' => $totalTasks,
            ]
        ], 200);
    }
}
