<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Règles d'accès par rôle (admin / manager / developer)
 */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    // ---------- Inscription et rôles ----------

    public function test_register_ignores_role_and_creates_developer(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Pirate',
            'email'                 => 'pirate@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'admin',
        ])->assertCreated()->assertJsonPath('user.role', 'developer');

        $this->assertDatabaseHas('users', ['email' => 'pirate@example.com', 'role' => 'developer']);
    }

    public function test_login_is_rate_limited(): void
    {
        $credentials = ['email' => 'nobody@example.com', 'password' => 'wrongpassword'];

        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/v1/auth/login', $credentials)->assertUnauthorized();
        }

        $this->postJson('/api/v1/auth/login', $credentials)->assertStatus(429);
    }

    public function test_only_admin_can_change_roles(): void
    {
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->manager()->create();
        $developer = User::factory()->create();

        Sanctum::actingAs($manager);
        $this->patchJson("/api/v1/users/{$developer->id}/role", ['role' => 'admin'])->assertForbidden();

        Sanctum::actingAs($admin);
        $this->patchJson("/api/v1/users/{$developer->id}/role", ['role' => 'manager'])
            ->assertOk()
            ->assertJsonPath('data.role', 'manager');
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        Sanctum::actingAs($admin);
        $this->patchJson("/api/v1/users/{$admin->id}/role", ['role' => 'developer'])->assertForbidden();
    }

    public function test_developer_cannot_list_users(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/v1/users')->assertForbidden();

        Sanctum::actingAs(User::factory()->manager()->create());
        $this->getJson('/api/v1/users')->assertOk();
    }

    // ---------- Visibilité ----------

    public function test_developer_only_sees_assigned_tasks(): void
    {
        $developer = User::factory()->create();
        $mine = Task::factory()->create(['assigned_id' => $developer->id]);
        Task::factory()->create(); // tâche de quelqu'un d'autre

        Sanctum::actingAs($developer);
        $ids = collect($this->getJson('/api/v1/tasks')->assertOk()->json('data'))->pluck('id');

        $this->assertEquals([$mine->id], $ids->all());
    }

    public function test_developer_cannot_bypass_filter_with_assigned_id(): void
    {
        $developer = User::factory()->create();
        $other = User::factory()->create();
        Task::factory()->create(['assigned_id' => $other->id]);

        Sanctum::actingAs($developer);
        $this->getJson("/api/v1/tasks?assigned_id={$other->id}")
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_manager_only_sees_own_projects_and_their_tasks(): void
    {
        $manager = User::factory()->manager()->create();
        $ownProject = Project::factory()->create(['owner_id' => $manager->id]);
        $otherProject = Project::factory()->create();
        $ownTask = Task::factory()->create(['project_id' => $ownProject->id]);
        Task::factory()->create(['project_id' => $otherProject->id]);

        Sanctum::actingAs($manager);

        $projectIds = collect($this->getJson('/api/v1/projects')->assertOk()->json('data'))->pluck('id');
        $this->assertEquals([$ownProject->id], $projectIds->all());

        $taskIds = collect($this->getJson('/api/v1/tasks')->assertOk()->json('data'))->pluck('id');
        $this->assertEquals([$ownTask->id], $taskIds->all());
    }

    public function test_developer_sees_projects_where_member(): void
    {
        $developer = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($developer->id);
        Project::factory()->create();

        Sanctum::actingAs($developer);
        $ids = collect($this->getJson('/api/v1/projects')->assertOk()->json('data'))->pluck('id');

        $this->assertEquals([$project->id], $ids->all());
    }

    // ---------- Création / modification de tâches ----------

    private function taskPayload(Project $project, array $overrides = []): array
    {
        return array_merge([
            'title'      => 'Nouvelle tâche',
            'status'     => 'a_faire',
            'priority'   => 'medium',
            'project_id' => $project->id,
        ], $overrides);
    }

    public function test_manager_cannot_create_task_in_another_managers_project(): void
    {
        $manager = User::factory()->manager()->create();
        $otherProject = Project::factory()->create();

        Sanctum::actingAs($manager);
        $this->postJson('/api/v1/tasks', $this->taskPayload($otherProject))->assertForbidden();
    }

    public function test_manager_can_create_task_in_own_project(): void
    {
        $manager = User::factory()->manager()->create();
        $project = Project::factory()->create(['owner_id' => $manager->id]);

        Sanctum::actingAs($manager);
        $this->postJson('/api/v1/tasks', $this->taskPayload($project))
            ->assertCreated()
            ->assertJsonPath('data.project_id', $project->id)
            ->assertJsonPath('data.project.id', $project->id);
    }

    public function test_developer_cannot_create_task(): void
    {
        $developer = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($developer->id);

        Sanctum::actingAs($developer);
        $this->postJson('/api/v1/tasks', $this->taskPayload($project))->assertForbidden();
    }

    public function test_task_cannot_be_assigned_to_non_member(): void
    {
        $manager = User::factory()->manager()->create();
        $project = Project::factory()->create(['owner_id' => $manager->id]);
        $outsider = User::factory()->create();

        Sanctum::actingAs($manager);
        $this->postJson('/api/v1/tasks', $this->taskPayload($project, ['assigned_id' => $outsider->id]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('assigned_id');
    }

    public function test_task_can_be_assigned_to_member(): void
    {
        $manager = User::factory()->manager()->create();
        $project = Project::factory()->create(['owner_id' => $manager->id]);
        $member = User::factory()->create();
        $project->users()->attach($member->id);

        Sanctum::actingAs($manager);
        $this->postJson('/api/v1/tasks', $this->taskPayload($project, ['assigned_id' => $member->id]))
            ->assertCreated()
            ->assertJsonPath('data.assigned_id', $member->id);
    }

    public function test_manager_cannot_move_task_to_another_managers_project(): void
    {
        $manager = User::factory()->manager()->create();
        $ownProject = Project::factory()->create(['owner_id' => $manager->id]);
        $otherProject = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $ownProject->id]);

        Sanctum::actingAs($manager);
        $this->putJson("/api/v1/tasks/{$task->id}", ['project_id' => $otherProject->id])->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'project_id' => $ownProject->id]);
    }

    public function test_moving_task_checks_current_assignee_membership(): void
    {
        $manager = User::factory()->manager()->create();
        $projectA = Project::factory()->create(['owner_id' => $manager->id]);
        $projectB = Project::factory()->create(['owner_id' => $manager->id]);
        $member = User::factory()->create();
        $projectA->users()->attach($member->id);
        $task = Task::factory()->create(['project_id' => $projectA->id, 'assigned_id' => $member->id]);

        Sanctum::actingAs($manager);
        $this->putJson("/api/v1/tasks/{$task->id}", ['project_id' => $projectB->id])
            ->assertStatus(422)
            ->assertJsonValidationErrors('assigned_id');
    }

    public function test_developer_can_only_change_status_of_own_task(): void
    {
        $developer = User::factory()->create();
        $task = Task::factory()->create([
            'assigned_id' => $developer->id,
            'status'      => 'a_faire',
            'title'       => 'Titre original',
        ]);
        $otherTask = Task::factory()->create(['status' => 'a_faire']);

        Sanctum::actingAs($developer);

        $this->putJson("/api/v1/tasks/{$task->id}", ['status' => 'en_cours', 'title' => 'Piraté'])
            ->assertOk()
            ->assertJsonPath('data.status', 'en_cours')
            ->assertJsonPath('data.title', 'Titre original');

        $this->putJson("/api/v1/tasks/{$otherTask->id}", ['status' => 'termine'])->assertForbidden();
    }
}
