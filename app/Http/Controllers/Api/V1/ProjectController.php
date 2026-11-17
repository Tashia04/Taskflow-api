<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {}

    /**
     * Liste de tous les projets accessible à l'utilisateur
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        $filters = $request->only(['name']);
        $projects = $this->projectService->getAllProjects($filters, $request->user());

        return response()->json([
            'message' => 'Projets récupérés avec succès',
            'data'    => ProjectResource::collection($projects),
        ], 200);
    }

    /**
     * Détails d'un projet par son ID
     */
    public function show(int $id): JsonResponse
    {
        $project = $this->projectService->getProjectById($id);
        if (!$project) {
            return response()->json([
                'message' => 'Projet non trouvé',
            ], 404);
        }

        $this->authorize('view', $project);

        return response()->json([
            'message' => 'Projet récupéré avec succès',
            'data'    => new ProjectResource($project),
        ], 200);
    }

    /**
     * Création d'un nouveau projet
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $this->authorize('create', Project::class);

        $project = $this->projectService->createProject($request->validated(), $request->user());

        return response()->json([
            'message' => 'Projet créé avec succès',
            'data'    => new ProjectResource($project),
        ], 201);
    }

    /**
     * Mise à jour d'un projet existant
     */
    public function update(UpdateProjectRequest $request, int $id): JsonResponse
    {
        $project = $this->projectService->getProjectById($id);
        if (!$project) {
            return response()->json([
                'message' => 'Projet non trouvé',
            ], 404);
        }

        $this->authorize('update', $project);

        $updatedProject = $this->projectService->updateProject($project, $request->validated());

        return response()->json([
            'message' => 'Projet mis à jour avec succès',
            'data'    => new ProjectResource($updatedProject),
        ], 200);
    }

    /**
     * Suppression d'un projet
     */
    public function destroy(int $id): JsonResponse
    {
        $project = $this->projectService->getProjectById($id);
        if (!$project) {
            return response()->json([
                'message' => 'Projet non trouvé ou déjà supprimé',
            ], 404);
        }

        $this->authorize('delete', $project);

        $this->projectService->deleteProject($project);

        return response()->json([
            'message' => 'Projet supprimé avec succès',
        ], 200);
    }
}
