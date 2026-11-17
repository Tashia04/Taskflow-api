<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
  // fonction pour récupérer toutes les tâches avec des filtres optionnels (ex: statut, priorité, etc.)
  public function all(array $filters = []): Collection;
  // fonction pour récupérer une tâche par son ID
  public function findById(int $id): ?Task;
  // fonction pour créer une nouvelle tâche
  public function create(array $data): Task;
  // fonction pour mettre à jour une tâche existante
  public function update(Task $task, array $data): Task;
  // fonction pour supprimer une tâche
  public function delete(Task $task): bool;
  // fonctions pour récupérer les tâches par projet ou par utilisateur assigné
  public function getByProject(int $projectId): Collection;
  // fonction pour récupérer les tâches assignées à un utilisateur spécifique
  public function getByAssignee(int $userId): Collection;
  //fonction pour supprimer une tâche par son ID
  public function deleteById(int $id): bool;
}