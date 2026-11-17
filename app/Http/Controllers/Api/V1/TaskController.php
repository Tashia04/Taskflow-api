<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private ProjectService $projectService
    ) {}

    /**
     * Récupérer toutes les tâches avec filtres
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $filters = $request->only(['status', 'priority', 'project_id', 'assigned_id']);
        $tasks = $this->taskService->getAllTasks($filters, $request->user());

        return response()->json([
            'message' => 'Tâches récupérées avec succès',
            'data'    => TaskResource::collection($tasks),
        ], 200);
    }

    /**
     * Récupérer une tâche par son ID
     */
    public function show(int $id): JsonResponse
    {
        $task = $this->taskService->getTaskById($id);
        if (!$task) {
            return response()->json([
                'message' => 'Tâche non trouvée',
            ], 404);
        }

        $this->authorize('view', $task);

        return response()->json([
            'message' => 'Tâche récupérée avec succès',
            'data'    => new TaskResource($task),
        ], 200);
    }

    /**
     * Créer une nouvelle tâche
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();

        // project_id existe (validé par StoreTaskRequest)
        $project = $this->projectService->getProjectById($data['project_id']);
        $this->authorize('create', [Task::class, $project]);

        $task = $this->taskService->createTask($data, $project);

        return response()->json([
            'message' => 'Tâche créée avec succès',
            'data'    => new TaskResource($task),
        ], 201);
    }

    /**
     * Mettre à jour une tâche
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $task = $this->taskService->getTaskById($id);
        if (!$task) {
            return response()->json([
                'message' => 'Tâche non trouvée',
            ], 404);
        }

        $this->authorize('update', $task);

        $data = $request->validated();
        if ($request->user()->isDeveloper()) {
            // Un développeur ne peut modifier que le statut de la tâche qui lui est assignée
            $data = array_intersect_key($data, ['status' => true]);
        }

        // Déplacer la tâche vers un autre projet exige le droit d'y créer des tâches
        $targetProject = null;
        if (isset($data['project_id']) && (int) $data['project_id'] !== $task->project_id) {
            $targetProject = $this->projectService->getProjectById($data['project_id']);
            $this->authorize('create', [Task::class, $targetProject]);
        }

        $updatedTask = $this->taskService->updateTask($task, $data, $targetProject);

        return response()->json([
            'message' => 'Tâche mise à jour avec succès',
            'data'    => new TaskResource($updatedTask),
        ], 200);
    }

    /**
     * Supprimer une tâche
     */
    public function destroy(int $id): JsonResponse
    {
        $task = $this->taskService->getTaskById($id);
        if (!$task) {
            return response()->json([
                'message' => 'Tâche non trouvée ou déjà supprimée',
            ], 404);
        }

        // Vérification de l'autorisation AVANT la suppression
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return response()->json([
            'message' => 'Tâche supprimée avec succès',
        ], 200);
    }
}
