<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>Project</th>
                <th>Task Summary</th>
                <th>Task Key</th>
                <th>Status</th>
                <th>Priority</th>
                <!-- <th>Sprint</th> -->
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
            <tr>
                <td>{{ $task['fields']['project']['name'] ?? '-' }}</td>
                <td>{{ $task['fields']['summary'] }}</td>
                <td>{{ $task['key'] }}</td>
                <td>{{ $task['fields']['status']['name'] ?? '-' }}</td>
                <td>{{ $task['fields']['priority']['name'] ?? '-' }}</td>
                <!-- <td>
                    @php
                    $sprint = $task['fields']['customfield_10007'][0]['name'] ?? '-';
                    @endphp
                    {{ $sprint }}
                </td> -->
                <td>
                    <a href="{{ route('jira.task.edit', $task['id']) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>