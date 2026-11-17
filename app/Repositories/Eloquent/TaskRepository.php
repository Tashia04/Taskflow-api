<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
  public function all(array $filters = []): Collection
  {
    $query = Task::with(['assignedUser', 'project']);

    // Filtre par status si fourni
    if (!empty($filters['status'])) {
      $query->where('status', $filters['status']);
    }

    // Filtre par priority si fourni
    if (!empty($filters['priority'])) {
      $query->where('priority', $filters['priority']);
    }

    // Filtre par projet si fourni
    if (!empty($filters['project_id'])) {
      $query->where('project_id', $filters['project_id']);
    }

    // Filtre par utilisateur assigné si fourni
    if (!empty($filters['assigned_id'])) {
      $query->where('assigned_id', $filters['assigned_id']);
    }

    // Filtre par propriétaire du projet si fourni
    if (!empty($filters['project_owner_id'])) {
      $query->whereHas('project', fn ($q) => $q->where('owner_id', $filters['project_owner_id']));
    }

    return $query->orderBy('created_at', 'desc')->get();
  }

  public function findById(int $id): ?Task
  {
    return Task::with(['assignedUser', 'project'])->find($id);
  }

  public function create(array $data): Task
  {
    return Task::create($data)->load(['assignedUser', 'project']);
  }

  public function update(Task $task, array $data): Task
  {
    $task->update($data);
    return $task->fresh(['assignedUser', 'project']);
  }

  public function delete(Task $task): bool
  {
    return (bool) $task->delete();
  }

  public function getByProject(int $projectId): Collection
  {
    return Task::with('assignedUser')
      ->where('project_id', $projectId)
      ->orderBy('priority')
      ->get();
  }

  public function getByAssignee(int $userId): Collection
  {
    return Task::with('project')
      ->where('assigned_id', $userId)
      ->get();
  }
  //fonction pour supprimer une tâche par son ID
  public function deleteById(int $id): bool
  {
    $task = Task::find($id);
    if ($task) {
      return $task->delete();
    }
    return false;
  }
}
