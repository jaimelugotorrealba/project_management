<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' =>$this->description,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'assigned_to' =>$this->assigned_to,
            'assigned_user' => $this->whenLoaded('assignee',function(){
                return [
                    'id' => $this->assignee->id,
                    'full_name' => $this->assignee->full_name,
                    'email' => $this->assignee->email
                ];
            }),
            'project' => $this->whenLoaded('project',function(){
                return [
                    'id' => $this->project->id,
                    'name' =>$this->project->name,
                    'progress' => $this->project->progress
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
