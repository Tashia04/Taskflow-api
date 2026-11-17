<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            //le propriétaire du projet
            'owner' => new UserResource($this->whenLoaded('owner')),
            //liste des menbres assignés au projet
            'users' => UserResource::collection($this->whenLoaded('users')),
            //Nombre de taches sans charger toute la relation
            'tasks_count' => $this->whenCounted('tasks') ,
            
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
