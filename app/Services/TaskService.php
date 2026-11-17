<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {}

    /**
     * Récupérer toutes les tâches selon les filtres et le rôle de l'utilisateur
     * (même règle que TaskPolicy::view)
     */
    public function getAllTasks(array $filters, User $user)
    {
        if ($user->isDeveloper()) {
            // Écrase un éventuel assigned_id envoyé par le client
            $filters['assigned_id'] = $user->id;
        } elseif ($user->isManager()) {
            $filters['project_owner_id'] = $user->id;
        }

        return $this->taskRepository->all($filters);
    }

    /**
     * Trouver une tâche par son ID
     */
    public function getTaskById(int $id): ?Task
    {
        return $this->taskRepository->findById($id);
    }

    /**
     * Créer une tâche (les droits sur le projet sont vérifiés par TaskPolicy::create)
     */
    public function createTask(array $data, Project $project): Task
    {
        $this->ensureAssigneeIsMember($project, $data['assigned_id'] ?? null);

        return $this->taskRepository->create($data);
    }

    /**
     * Mettre à jour une tâche, éventuellement en la déplaçant vers $targetProject
     */
    public function updateTask(Task $task, array $data, ?Project $targetProject = null): Task
    {
        $projectChanged = $targetProject && $targetProject->id !== $task->project_id;

        // Vérifie le nouvel assigné, ou l'assigné actuel si la tâche change de projet
        if (array_key_exists('assigned_id', $data) || $projectChanged) {
            $this->ensureAssigneeIsMember(
                $targetProject ?? $task->project,
                array_key_exists('assigned_id', $data) ? $data['assigned_id'] : $task->assigned_id
            );
        }

        return $this->taskRepository->update($task, $data);
    }

    /**
     * Supprimer une tâche
     */
    public function deleteTask(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }

    /**
     * Une tâche ne peut être assignée qu'au propriétaire ou à un membre du projet
     */
    private function ensureAssigneeIsMember(Project $project, ?int $assignedId): void
    {
        if ($assignedId === null) {
            return;
        }

        $isMember = $project->owner_id === $assignedId
            || $project->users()->where('user_id', $assignedId)->exists();

        if (!$isMember) {
            throw ValidationException::withMessages([
                'assigned_id' => 'Cet utilisateur n\'est pas membre du projet.',
            ]);
        }
    }
}
