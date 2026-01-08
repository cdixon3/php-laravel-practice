<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test health check endpoint.
     */
    public function test_health_endpoint_returns_success(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'message' => 'API is running'
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'timestamp'
            ]);
    }

    /**
     * Test getting all tasks when database is empty.
     */
    public function test_can_get_empty_task_list(): void
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    }

    /**
     * Test getting all tasks.
     */
    public function test_can_get_all_tasks(): void
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'completed',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test creating a task with valid data.
     */
    public function test_can_create_task(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'completed' => false
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => [
                    'title' => 'Test Task',
                    'description' => 'This is a test task',
                    'completed' => false
                ]
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'completed',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'completed' => false
        ]);
    }

    /**
     * Test creating a task without required fields fails validation.
     */
    public function test_cannot_create_task_without_title(): void
    {
        $taskData = [
            'description' => 'This is a test task'
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test creating a task with title exceeding max length fails validation.
     */
    public function test_cannot_create_task_with_too_long_title(): void
    {
        $taskData = [
            'title' => str_repeat('a', 256),
            'description' => 'This is a test task'
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test getting a specific task.
     */
    public function test_can_get_specific_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Specific Task',
            'description' => 'This is a specific task'
        ]);

        $response = $this->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'title' => 'Specific Task',
                    'description' => 'This is a specific task'
                ]
            ]);
    }

    /**
     * Test getting a non-existent task returns 404.
     */
    public function test_cannot_get_nonexistent_task(): void
    {
        $response = $this->getJson('/api/tasks/999');

        $response->assertStatus(404);
    }

    /**
     * Test updating a task.
     */
    public function test_can_update_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'completed' => false
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'completed' => true
        ];

        $response = $this->putJson('/api/tasks/' . $task->id, $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task updated successfully',
                'data' => [
                    'id' => $task->id,
                    'title' => 'Updated Title',
                    'description' => 'Updated Description',
                    'completed' => true
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'completed' => true
        ]);
    }

    /**
     * Test partially updating a task.
     */
    public function test_can_partially_update_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
            'completed' => false
        ]);

        $updateData = [
            'completed' => true
        ];

        $response = $this->patchJson('/api/tasks/' . $task->id, $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task updated successfully',
                'data' => [
                    'id' => $task->id,
                    'title' => 'Original Title',
                    'description' => 'Original Description',
                    'completed' => true
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Original Title',
            'completed' => true
        ]);
    }

    /**
     * Test updating a non-existent task returns 404.
     */
    public function test_cannot_update_nonexistent_task(): void
    {
        $updateData = [
            'title' => 'Updated Title'
        ];

        $response = $this->putJson('/api/tasks/999', $updateData);

        $response->assertStatus(404);
    }

    /**
     * Test deleting a task.
     */
    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    /**
     * Test deleting a non-existent task returns 404.
     */
    public function test_cannot_delete_nonexistent_task(): void
    {
        $response = $this->deleteJson('/api/tasks/999');

        $response->assertStatus(404);
    }

    /**
     * Test tasks are returned in descending order by created_at.
     */
    public function test_tasks_are_ordered_by_created_at_desc(): void
    {
        $task1 = Task::factory()->create(['title' => 'First Task']);
        sleep(1);
        $task2 = Task::factory()->create(['title' => 'Second Task']);
        sleep(1);
        $task3 = Task::factory()->create(['title' => 'Third Task']);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);

        $data = $response->json('data');

        $this->assertEquals('Third Task', $data[0]['title']);
        $this->assertEquals('Second Task', $data[1]['title']);
        $this->assertEquals('First Task', $data[2]['title']);
    }

    /**
     * Test creating task with boolean completed field.
     */
    public function test_can_create_task_with_boolean_completed(): void
    {
        $taskData = [
            'title' => 'Completed Task',
            'description' => 'Already done',
            'completed' => true
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'completed' => true
                ]
            ]);
    }
}
