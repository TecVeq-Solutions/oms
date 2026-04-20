<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Workspace Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $workspace->name }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Created By</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $workspace->creator->name ?? '-' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                        <p class="text-sm text-gray-500">Description</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $workspace->description ?? '-' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">
                            {{ $workspace->is_active ? 'Active' : 'Inactive' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Projects Count</p>
                        <p class="text-base font-semibold text-gray-800 mt-1">{{ $workspace->projects->count() }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Members</h3>

                    @if($workspace->users->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($workspace->users as $user)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    <p class="text-sm text-indigo-600 mt-1 capitalize">
                                        {{ $user->pivot->role_in_workspace }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
                            No members found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>