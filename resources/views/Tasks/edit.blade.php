<x-app-layout>
    <x-slot name="header">
        <h3>Edit Task: <span class="text-primary">{{ $task['key'] }}</span></h3>
    </x-slot>

    <div class="container py-4">
        <div class="card shadow-sm mx-auto" style="max-width: 720px;">
            <div class="card-body">
                <form action="{{ route('jira.task.update', $task['id']) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="summary" class="form-label">Summary</label>
                        <input type="text" name="summary" id="summary"
                            value="{{ $task['fields']['summary'] }}"
                            class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="4"
                            class="form-control">{{ $task['fields']['description']['content'][0]['content'][0]['text'] ?? '' }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            @foreach ($statuses as $status)
                            <option value="{{ $status['id'] }}"
                                {{ $status['name'] == $task['fields']['status']['name'] ? 'selected' : '' }}>
                                {{ $status['name'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label">New Comment</label>
                        <textarea name="comment" id="comment" rows="3" class="form-control"></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Update Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>