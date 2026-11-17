<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // @use hasApiTokens pour les tokens d'authentification API
    use HasApiTokens;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    //Un utilisateur peut posséder plusieurs projets
    public function ownedProjects()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }
    //Un utilisateur peut être assigné à plusieurs projets
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user');
    }
    //Un utilisateur peut être assigné à plusieurs tâches
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_id');
    }
    //pour vérifier si l'utilisateur est un admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    } 
    //pour vérifier si l'utilisateur est un manager
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }
    //pour vérifier si l'utilisateur est un développeur
    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }
}
