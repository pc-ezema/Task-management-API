<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_task_can_be_created()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a task for the user
        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test Description'
        ]);

        // Assert that the task was created and has the correct attributes
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function a_task_can_be_created_and_marked_as_completed()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a task for the user
        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test Description'
        ]);

        // Assert that the task was created and has the correct attributes
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'user_id' => $user->id,
        ]);

        // Mark the task as completed
        $task->markAsCompleted();

        // Assert that the task is marked as completed
        $this->assertTrue($task->completed);
    }
}
