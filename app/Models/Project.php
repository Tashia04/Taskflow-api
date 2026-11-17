<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'status',
        'owner_id',
    ];

    //un projet appartient à un propriétaire (user)
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    //un projet peut avoir plusieurs utilisateurs assignés
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }
    //un projet peut avoir plusieurs tâches
    public function tasks()
    {
        return $this->hasMany(Task::class); 
    }    






}


