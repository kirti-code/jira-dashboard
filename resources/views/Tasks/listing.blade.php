<table class="min-w-full table-auto">
    <thead>
        <tr>
            <th class="border px-4 py-2">Project</th>
            <th class="border px-4 py-2">Task Summary</th>
            <th class="border px-4 py-2">Task Key</th>
            <th class="border px-4 py-2">Status</th>
            <th class="border px-4 py-2">Priority</th>
            <th class="border px-4 py-2">Sprint</th>
            <th class="border px-4 py-2">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tasks as $task)
        <tr>
            <td class="border px-4 py-2">{{ $task['fields']['project']['name'] ?? '-' }}</td>
            <td class="border px-4 py-2">{{ $task['fields']['summary'] }}</td>
            <td class="border px-4 py-2">{{ $task['key'] }}</td>
            <td class="border px-4 py-2">{{ $task['fields']['status']['name'] ?? '-' }}</td>
            <td class="border px-4 py-2">{{ $task['fields']['priority']['name'] ?? '-' }}</td>
            <td class="border px-4 py-2">
                @php
                $sprint = $task['fields']['customfield_10007'][0]['name'] ?? '-';
                @endphp
                {{ $sprint }}
            </td>
            <td>
                <a href="{{ route('jira.task.edit', $task['id']) }}">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>