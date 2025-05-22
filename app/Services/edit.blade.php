<form action="{{ route('jira.task.update', $task['id']) }}" method="POST">
    @csrf
    <label>Summary</label>
    <input type="text" name="summary" value="{{ $task['fields']['summary'] }}" />

    <label>Status</label>
    <select name="status">
        @foreach ($task['fields']['status']['statusCategory']['statuses'] ?? [] as $status)
        <option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
        @endforeach
    </select>

    <button type="submit">Update</button>
</form>