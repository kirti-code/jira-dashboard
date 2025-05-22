<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Jira Tasks') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                @if ($tasks)
                @include('Tasks.listing')
                @else
                <p>No tasks found or failed to connect to Jira.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>