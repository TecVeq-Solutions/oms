<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Workspaces</h2>

                    @can('create workspace')
                        <a href="{{ route('workspaces.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Create Workspace
                        </a>
                    @endcan
                </div>

                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if($workspaces->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Creator</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workspaces as $workspace)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">{{ $workspace->name }}</td>
                                        <td class="px-4 py-3">{{ $workspace->creator->name ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            {{ $workspace->is_active ? 'Active' : 'Inactive' }}
                                        </td>
                                        <td class="px-4 py-3 flex gap-2">
                                            <a href="{{ route('workspaces.show', $workspace) }}"
                                               class="text-blue-600 hover:underline">View</a>

                                            @can('edit workspace')
                                                <a href="{{ route('workspaces.edit', $workspace) }}"
                                                   class="text-yellow-600 hover:underline">Edit</a>
                                            @endcan

                                            @can('delete workspace')
                                                <form action="{{ route('workspaces.destroy', $workspace) }}" method="POST"
                                                      onsubmit="return confirm('Delete this workspace?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $workspaces->links() }}
                    </div>
                @else
                    <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                        No workspaces found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>