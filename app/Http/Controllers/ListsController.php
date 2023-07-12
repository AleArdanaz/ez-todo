<?php

namespace App\Http\Controllers;

use App\Models\Lists;
use App\Models\Task;
use Illuminate\Http\Request;

class ListsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = Lists::where('user_id', auth()->user()->id)->orderBy('order', 'desc')->get();

        $list = Lists::where('user_id', auth()->user()->id)->firstOrFail();

        return inertia('Lists/Index', [
            'lists' => $lists,
            'status' => session('status'),
            'currentList' => $list,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $lastOrder = Lists::where('user_id', auth()->user()->id)->max('order');

        $list = Lists::create([
            'name' => 'Untitled 😎',
            'user_id' => auth()->user()->id,
            'order' => $lastOrder + 1,
        ]);

        $task = Task::create([
            'name' => 'Test Task',
            'list_id' => $list->id,
            'completed' => false,
            'completed_at' => null,
            'due_date' => null,
            'remind_at' => null,
            'notes' => null,
            'priority' => 1,
        ]);

        $list->tasks = [$task];

        return response()->json([
            'success' => true,
            'message' => 'List created successfully',
            'list' => $list,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $list = Lists::with('tasks')->where('id', $id)->firstOrFail();

        return redirect()->route('dashboard')->with('currentList', $list);
    }

    /**
     * Main update method of lists and tasks
     */
    public function updateListAndTasks(Request $request, string $list_id): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'list' => 'required|array',
                'list.tasks' => 'array',
            ]);

            $list = $request->input('list');
            $old_list = Lists::find($list_id);

            if (auth()->user()->cannot('update', $old_list)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized',
                ], 403);
            }

            $originalTasks = $old_list->tasks;
            $tasksData = $list['tasks'];

            $allowedTasksCount = 9;

            if (count($tasksData) > $allowedTasksCount) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only have 9 tasks in a list',
                ], 422);
            }

            $updatedTasks = [];

            foreach ($tasksData as $taskData) {
                if (!empty($taskData['name'])) {
                    $task = $originalTasks->find($taskData['id']);
                    if ($task) {
                        $task->update($taskData);
                    } else {
                        $task = $old_list->tasks()->create($taskData);
                    }
                    $updatedTasks[] = $task;
                }
            }

            $deletedTaskIds = $originalTasks->pluck('id')->diff(collect($updatedTasks)->pluck('id'))->all();
            Task::destroy($deletedTaskIds);

            $old_list->update($list);

            return response()->json([
                'success' => true,
                'message' => 'Saved successfully',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Delete the specified resource in storage.
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $list = Lists::find($id);

            if (auth()->user()->cannot('update', $list)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized',
                ], 403);
            }

            $list->delete();

            return response()->json([
                'success' => true,
                'message' => 'List deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while deleting the list',
            ], 500);
        }
    }
}
