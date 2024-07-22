<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $token = $loginResponse->json('token');

        // Use the token to create a task
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
        ]);

        $response->assertStatus(200); // Created
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'completed' => false,
            'user_id' => $user->id, // Ensure the task is associated with the authenticated user
        ]);
    }
}
