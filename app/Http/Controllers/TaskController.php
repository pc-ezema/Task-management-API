<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())->latest()->paginate(20);

        return response()->json([
            'code' => 200,
            'message' => 'All tasks retrieved successfully.',
            'data' => $tasks
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $task = Task::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        return response()->json([
            'code' => 200,
            'message' => 'Task added successfully.',
            'data' => $task
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'code' => 403,
                'message' => 'Unauthorized access.',
                'data' => null
            ], 403);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Task retrieved successfully.',
            'data' => $task
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'code' => 403,
                'message' => 'Unauthorized access.',
                'data' => null
            ], 403);
        }

        $task->update($request->validated());

        return response()->json([
            'code' => 200,
            'message' => 'Task updated successfully.',
            'data' => $task
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'code' => 403,
                'message' => 'Unauthorized access.',
                'data' => null
            ], 403);
        }

        $task->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Task deleted successfully.',
        ], 200);
    }

    /**
     * Complete the specified resource from storage.
     */
    public function complete($id)
    {
        $task = Task::find($id);

        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'code' => 403,
                'message' => 'Unauthorized access.',
                'data' => null
            ], 403);
        }

        $task->markAsCompleted();

        return response()->json([
            'code' => 200,
            'message' => 'Task completed successfully.',
        ], 200);
    }
}
