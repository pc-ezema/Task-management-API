<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    /** @test */
    public function task_creation_requires_authentication()
    {
        // Attempt to create a task without authentication
        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
        ]);

        $response->assertStatus(401); // Unauthorized

        // Authenticate user and retrieve token
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $loginResponse->json('token');

        // Use the token to create a task
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/tasks', [
            'user_id' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
        ]);

        $response->assertStatus(200); // Created
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'user_id' => $user->id, // Ensure the task is associated with the authenticated user
        ]);
    }

    /**
     * Test that a task can be marked as completed.
     */
    public function test_task_can_be_marked_as_completed()
    {
        // Authenticate user and retrieve token
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $loginResponse->json('token');

        // Use the token to create a task
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/tasks', [
            'user_id' => $user->id,
            'title' => 'Incomplete Task',
            'description' => 'This task is not yet completed',
        ]);

        $response->assertStatus(200); // Created
        $taskData = $response->json('data');
        $taskId = $taskData['id'];

        // Mark the task as completed
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson("/api/complete-task/{$taskId}", [
            'completed' => true,
        ]);

        $response->assertStatus(200); // OK

        // Assert that the task is marked as completed
        $this->assertDatabaseHas('tasks', [
            'id' => $taskId,
            'completed' => true,
        ]);
    }
}
