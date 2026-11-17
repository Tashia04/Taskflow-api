<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'project_id',
        'assigned_id',
        'due_date',
    ];
    //cast pour les dates 
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    //une tâche appartient à un projet
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    //une tâche peut être assignée à un utilisateur
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_id');
    }    
}
