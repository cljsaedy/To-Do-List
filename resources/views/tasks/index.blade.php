<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .status-button-update {
            background-color: #48bb78; 
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-button-complete {
            background-color: #4299e1; 
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-button-remove {
            background-color: #f56565; 
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-50 p-8">

    <div class="max-w-6xl mx-auto bg-white border border-gray-200 shadow-xl rounded-lg overflow-hidden">
        
        <div class="bg-gray-800 text-white flex items-center p-4">
            <h1 class="text-2xl font-bold tracking-tight">To-Do List</h1>
        </div>

        <div class="border-b border-gray-200 flex text-center text-sm font-medium text-gray-500">
            @php
                $tabs = [
                    'todo' => 'To-Do',
                    'low' => 'Low Priority',
                    'medium' => 'Medium Priority',
                    'high' => 'High Priority',
                    'completed' => 'Completed',
                    'deleted' => 'Deleted',
                ];
            @endphp

            @foreach ($tabs as $key => $label)
                <a href="{{ route('tasks.filter', ['filter' => $key]) }}"
                   class="py-3 px-6 transition duration-150 ease-in-out 
                          @if ($currentTab === $key)
                              text-gray-800 bg-gray-100 border-b-2 border-indigo-600 font-semibold
                          @else
                              hover:bg-gray-50 hover:text-gray-700 
                          @endif">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        
        <div class="p-6">

            @if (!isset($taskToEdit)) 
                
                @if ($currentTab !== 'deleted')
                    <button onclick="document.getElementById('addTaskForm').classList.toggle('hidden')" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg mb-6 shadow-md transition duration-150">
                        Add Task
                    </button>
                
                    <form id="addTaskForm" action="{{ route('tasks.store') }}" method="POST" class="bg-gray-100 p-5 rounded-lg mb-8 shadow-inner hidden border border-gray-200">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <input type="text" name="name" placeholder="Task Name" required class="col-span-2 p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <select name="priority" class="p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="High">High Priority</option>
                                <option value="Medium" selected>Medium Priority</option>
                                <option value="Low">Low Priority</option>
                            </select>
                            <input type="date" name="deadline" required class="p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg transition duration-150 shadow-md">
                                Save Task
                            </button>
                        </div>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </form>
                @endif

                <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-md">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Task Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th> 
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($tasks as $index => $task)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-3 whitespace-nowrap">{{ $index + 1 }}.</td>
                                    <td class="px-6 py-3 whitespace-nowrap font-medium text-gray-900">{{ $task->name }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if ($task->priority == 'High') bg-red-100 text-red-800 border border-red-300
                                            @elseif ($task->priority == 'Medium') bg-yellow-100 text-yellow-800 border border-yellow-300
                                            @else bg-green-100 text-green-800 border border-green-300 @endif">
                                            {{ $task->priority }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ $task->deadline }}</td>
                                    
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-medium">
                                        <div class="flex justify-end space-x-2"> 
                                            @if ($currentTab !== 'deleted')
                                                
                                                <a href="{{ route('tasks.filter', ['filter' => $currentTab, 'taskToEditId' => $task->id]) }}" class="status-button-update hover:bg-green-600">Update</a>

                                                @if (!$task->is_completed)
                                                    <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="status-button-complete hover:bg-blue-700">Complete</button> 
                                                    </form>
                                                @else
                                                    <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="status-button-complete bg-yellow-600 hover:bg-yellow-700">Mark as To-Do</button> 
                                                    </form>
                                                @endif
                                                
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Confirm soft delete?')" class="status-button-remove hover:bg-red-600">Remove</button>
                                                </form>

                                            @else
                                                <form action="{{ route('tasks.restore', $task->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="status-button-update hover:bg-green-600">Restore</button>
                                                </form>

                                                <form action="{{ route('tasks.forceDelete', $task->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('WARNING: Permanently delete this record?')" class="status-button-remove hover:bg-red-600">Delete Permanently</button> {{-- Re-used remove style --}}
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 bg-gray-50">
                                        No tasks found in the '{{ $currentTab }}' list.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @else 
                
                <div class="max-w-xl mx-auto bg-gray-100 p-8 border border-gray-300 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Task: <span class="text-indigo-600">{{ $taskToEdit->name }}</span></h2>

                    <form action="{{ route('tasks.update', $taskToEdit) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT') 
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Task Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $taskToEdit->name) }}" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority" id="priority" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="High" @selected(old('priority', $taskToEdit->priority) == 'High')>High</option>
                                <option value="Medium" @selected(old('priority', $taskToEdit->priority) == 'Medium')>Medium</option>
                                <option value="Low" @selected(old('priority', $taskToEdit->priority) == 'Low')>Low</option>
                            </select>
                            @error('priority')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                            <input type="date" name="deadline" id="deadline" value="{{ old('deadline', \Carbon\Carbon::parse($taskToEdit->deadline)->format('Y-m-d')) }}" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @error('deadline')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex justify-between pt-4">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 shadow-md">
                                Save Changes
                            </button>
                            <a href="{{ route('tasks.filter', ['filter' => $currentTab]) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-150 shadow-md">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
                
            @endif
        </div>
    </div>
</body>
</html>