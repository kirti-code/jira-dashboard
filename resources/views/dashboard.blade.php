<x-app-layout>
    <x-slot name="header">

    </x-slot>

    <div class="card">
        <div class="card-header bg-gray">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Jira Tasks') }}
            </h2>
        </div>
        <div class="card-body">
            @if (count($tasks) > 0)
            @include('Tasks.listing')

            @else
            <div class="alert alert-warning">No tasks found or failed to connect to Jira.</div>
            @endif
        </div>
    </div>

</x-app-layout>