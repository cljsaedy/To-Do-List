<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // paramater accepts null or string, defaulting to 'todo'
    public function index(string $filter = null, int $taskToEditId = null) 
    {
        // defines current tab || defaults to 'todo' if $filter is null or empty
        $currentTab = $filter ?? 'todo';

        $query = Task::query()->orderBy('deadline');

        switch ($currentTab) {
            case 'low':
            case 'medium':
            case 'high':
                $query->where('priority', ucfirst($currentTab))
                      ->where('is_completed', false) // exclude completed tasks from priority views
                      ->whereNull('deleted_at'); // exclude soft deleted tasks
                break;

            case 'completed':
                $query->where('is_completed', true)
                      ->whereNull('deleted_at');
                break;

            case 'deleted':
                // retrieving soft delete data
                $query->onlyTrashed(); // only retrieve soft deleted tasks
                break;

            case 'todo':
            default:
                // defailt view
                $query->where('is_completed', false)
                      ->whereNull('deleted_at');
                break;
        }

        // retrieve records || order tasks by deadline
        $tasks = $query->get();
        
        $taskToEdit = null; // Initialize the variable for the edit form

        // fetch the task model if an ID was passed in the URL
        if ($taskToEditId) {
            // fetch the task to edit, using findOrFail to handle missing IDs
            $taskToEdit = Task::findOrFail($taskToEditId); 
        }

        // pass all variables including $tasks, $currentTab, AND $taskToEdit to the view
        return view('tasks.index', compact('tasks', 'currentTab', 'taskToEdit'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'priority' => 'required|in:Low,Medium,High',
            'deadline' => 'required|date',
            // is_completed is not needed || defaults to false
        ]);

        // inserting/saving data (Lesson 4) & mass assignment (Lesson 7)
        Task::create($validatedData);
        
        return redirect()->route('tasks.filter', ['filter' => 'todo'])->with('success', 'Task added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task) 
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'priority' => 'required|in:Low,Medium,High',
            'deadline' => 'required|date',
        ]);

        // update data (Lesson 5)
        $task->update($validatedData);

        return redirect()->route('tasks.filter', ['filter' => 'todo'])->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // soft delete
        $task->delete();

        return redirect()->route('tasks.filter', ['filter' => 'todo'])->with('success', 'Task removed successfully.');
    }

    /**
     * Restores a soft deleted task.
     */
    public function restore($taskId)
    {
        // using where and restore() because route model binding doesn't fetch trashed items by default
        Task::withTrashed()->findOrFail($taskId)->restore();

        return redirect()->route('tasks.filter', ['filter' => 'deleted'])->with('success', 'Task restored successfully!');
    }

    /**
     * Permanently deletes a task from the database.
     */
    public function forceDelete($taskId)
    {
        // forceDelete() to permanently remove the record
        Task::withTrashed()->findOrFail($taskId)->forceDelete();

        return redirect()->route('tasks.filter', ['filter' => 'deleted'])->with('success', 'Task permanently deleted.');
    }
    
    public function toggleComplete(Task $task)
    {
        // eloquent Update (Lesson 5): toggles boolean status
        $task->update(['is_completed' => !$task->is_completed]);

        $status = $task->is_completed ? 'completed' : 'moved back to To-Do';
        
        return redirect()->route('tasks.filter', ['filter' => 'todo'])->with('success', "Task successfully $status.");
    }
}