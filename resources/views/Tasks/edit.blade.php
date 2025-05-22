<x-app-layout>
    <x-slot name="header">Edit Task: {{ $task['key'] }}</x-slot>

    <div class="max-w-2xl mx-auto mt-5 p-5 bg-white rounded shadow">
        <form action="{{ route('jira.task.update', $task['id']) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Summary</label>
                <input type="text" name="summary" value="{{ $task['fields']['summary'] }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $task['fields']['description']['content'][0]['content'][0]['text'] ?? '' }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">New Comment</label>
                <textarea name="comment" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>

            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update Task</button>
        </form>
    </div>
</x-app-layout>