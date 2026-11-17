<?php

namespace App\Repositories\Interfaces;


use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

interface ProjectRepositoryInterface
{
  // fonction pour récupérer tous les projets avec des filtres optionnels (ex: statut, priorité, etc.)
  public function all(array $filters = []): Collection;
  // fonction pour récupérer un projet par son ID
  public function findById(int $id): ?Project;
  // fonction pour créer un nouveau projet
  public function create(array $data): Project;
  // fonction pour mettre à jour un projet existant
  public function update(Project $project, array $data): Project;
  // fonction pour supprimer un projet
  public function delete(Project $project): bool;
  // fonction pour récupérer les projets dont l'utilisateur est propriétaire ou membre
  public function getAccessibleBy(int $userId, array $filters = []): Collection;
  // fonction pour récupérer les projets par utilisateur assigné
  public function getByAssignee(int $userId): Collection;
  //fonction pour supprimer un projet par son ID
  public function deleteById(int $id): bool;


  
}