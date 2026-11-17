<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    // public function run(): void
    // {
    //     // User::factory(10)->create();

    //     User::factory()->create([
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //     ]);

    //     //Creer un utilisateur admin
    //     $admin =User::factory()->create([
    //         'name' => 'Admin TaskFlow',
    //         'email' => 'admin@taskflow.com',
    //         'password' => bcrypt('password'),
    //         'role' => 'admin',
    //     ]);

    //     //Creer un utilisateur manager
    //     $manager =User::factory()->create([
    //         'name' => 'Manager TaskFlow',
    //         'email' => 'manager@taskflow.com',
    //         'password' => bcrypt('password'),
    //         'role' => 'manager',
    //     ]);
    //     //Creer des utilisateur developer
    //     $dev1 =User::factory()->create([
    //         'name' => 'Dev1 Mohamed',
    //         'email' => 'developer@taskflow.com',
    //         'password' => bcrypt('password'),
    //         'role' => 'developer',
    //     ]);

    //     $dev2 =User::factory()->create([
    //             'name' => 'Dev2 Yassine',
    //             'email' => 'developer2@taskflow.com',
    //             'password' => bcrypt('password'),
    //             'role' => 'developer',
    //         ]);
        
    //     $dev3 =User::factory()->create([
    //             'name' => 'Dev3 Youness',
    //             'email' => 'developer3@taskflow.com',
    //             'password' => bcrypt('password'),
    //             'role' => 'developer',
    //         ]);

    //     //Creer un projet    
    //     $project = Project::factory()->create([
    //         'name'        => 'Refonte Site Web',
    //         'description' => 'Refonte complète du site corporate',
    //         'status'      => 'en_cours',
    //         'owner_id'    => $manager->id,
    //     ]);
    //     //Assigner le projet au manager et aux développeurs
    //     $project->users()->attach([
    //         $manager->id,
    //         $dev1->id,
    //         $dev2->id,
    //         $dev3->id
    //     ]);

    //     //Creer des tâches pour le projet
    //     Task::factory()->create([
    //         'title' => 'Design de la nouvelle interface',
    //         'description' => 'Créer des maquettes pour la nouvelle interface utilisateur',
    //         'status' => 'en_cours',
    //         'project_id' => $project->id,
    //         'assigned_id' => $dev1->id,
    //         'due_date' => now()->addDays(7),
    //     ]);

    //     Task::factory()->create([
    //         'title' => 'Développement du backend',
    //         'description' => 'Mettre en place l\'API et la logique métier',
    //         'status' => 'a_faire',
    //         'project_id' => $project->id,
    //         'assigned_id' => $dev2->id,
    //         'due_date' => now()->addDays(14),
    //     ]);

    //     Task::factory()->create([
    //         'title' => 'Tests et déploiement',
    //         'description' => 'Réaliser les tests unitaires et déployer sur le serveur de production',
    //         'status' => 'a_faire',
    //         'project_id' => $project->id,
    //         'assigned_id' => $dev3->id,
    //         'due_date' => now()->addDays(21),
    //     ]);

    // }
}
