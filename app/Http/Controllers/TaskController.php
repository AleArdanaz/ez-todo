<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Lists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $list_id = $request->list_id;

        Task::create([
            'name' => $request->name,
            'list_id' => $request->list_id,
            'completed' => false,
            'completed_at' => null,
            'due_date' => null,
            'remind_at' => null,
            'notes' => null,
            'priority' => 0,
        ]);

        return Redirect::back();
    }

    /**
     * Update the status of one or more tasks.
     */
    public function changeStatus(Request $request): \Illuminate\Http\JsonResponse
    {

        try {
            $validatedData = $request->validate([
                'tasks' => 'required|array',
            ]);

            $tasksId = $request->input('tasks');

            foreach ($tasksId as $taskId) {
                $task = Task::find($taskId);
                $task->completed = !$task->completed;
                $task->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
