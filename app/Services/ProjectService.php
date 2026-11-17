<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Interfaces\ProjectRepositoryInterface;

class ProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * Récupérer tous les projets selon les filtres et le rôle de l'utilisateur
     */
    public function getAllProjects(array $filters, User $user)
    {
        // Admin voit tout ; manager et développeur voient les projets dont ils sont propriétaires ou membres
        // (même règle que ProjectPolicy::view)
        if ($user->isAdmin()) {
            return $this->projectRepository->all($filters);
        }

        return $this->projectRepository->getAccessibleBy($user->id, $filters);
    }

    /**
     * Obtenir un projet par son ID
     */
    public function getProjectById(int $id): ?Project
    {
        return $this->projectRepository->findById($id);
    }

    /**
     * Créer un nouveau projet
     */
    public function createProject(array $data, User $user): Project
    {
        $data['owner_id'] = $user->id;

        return $this->projectRepository->create($data);
    }

    /**
     * Mettre à jour un projet
     */
    public function updateProject(Project $project, array $data): Project
    {
        return $this->projectRepository->update($project, $data);
    }

    /**
     * Supprimer un projet
     */
    public function deleteProject(Project $project): bool
    {
        return $this->projectRepository->delete($project);
    }
}
