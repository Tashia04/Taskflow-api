<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository implements ProjectRepositoryInterface
{
  public function all(array $filters = []): Collection
  {
    $query = Project::with(['owner', 'users', 'tasks'])->withCount('tasks');

    // Filtre par nom si fourni
    if (!empty($filters['name'])) {
      $query->where('name', 'like', '%' . $filters['name'] . '%');
    }

    return $query->get();
  }

  public function findById(int $id): ?Project
  {
    return Project::with(['owner', 'users', 'tasks'])->withCount('tasks')->find($id);
  }

  public function create(array $data): Project
  {
    $project = Project::create($data);
    if (!empty($data['user_ids'])) {
      $project->users()->sync($data['user_ids']);
    }
    return $project->load(['owner', 'users', 'tasks'])->loadCount('tasks');
  }

  public function update(Project $project, array $data): Project
  {
    $project->update($data);
    if (isset($data['user_ids'])) {
      $project->users()->sync($data['user_ids']);
    }
    return $project->fresh(['owner', 'users', 'tasks'])->loadCount('tasks');
  }

  public function delete(Project $project): bool
  {
    return $project->delete();
  }

  //fonction pour supprimer un projet par son ID
  public function deleteById(int $id): bool
  {
    $project = Project::find($id);
    if ($project) {
      return $project->delete();
    }
    return false;
  }

  // fonction pour récupérer les projets dont l'utilisateur est propriétaire ou membre
  public function getAccessibleBy(int $userId, array $filters = []): Collection
  {
    $query = Project::where(function ($query) use ($userId) {
      $query->where('owner_id', $userId)
        ->orWhereHas('users', fn ($q) => $q->where('user_id', $userId));
    })->with(['owner', 'users'])->withCount('tasks');

    // Filtre par nom si fourni
    if (!empty($filters['name'])) {
      $query->where('name', 'like', '%' . $filters['name'] . '%');
    }

    return $query->get();
  }

  // fonction pour récupérer les projets par utilisateur assigné
  public function getByAssignee(int $userId): Collection
  {
    return Project::whereHas('users', function ($query) use ($userId) {
      $query->where('user_id', $userId);
    })->with(['owner', 'users', 'tasks'])->withCount('tasks')->get();
  }
}
