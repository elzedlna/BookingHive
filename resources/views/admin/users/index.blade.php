<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-3">Manage Users</h2>
                    <div class="mb-2">
                        <a href="{{ route('admin.users.create') }}" class="text-white bg-blue-600 px-4 py-1 text-lg rounded">Add
                            User</a>
                    </div>
                    <!-- Search Form -->
                    <div class="mb-6">
                        <form action="{{ route('admin.users') }}" method="GET">
                            <div class="flex gap-4">
                                <!-- Search Input -->
                                <x-text-input id="search" name="search" type="text" class="flex-1"
                                    placeholder="Search users..." value="{{ request('search') }}" />

                                <!-- Role Dropdown -->
                                <select id="role" name="role"
                                    class="flex border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All Roles</option>
                                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User
                                    </option>
                                    <option value="hotel" {{ request('role') === 'hotel' ? 'selected' : '' }}>Hotel
                                    </option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin
                                    </option>
                                </select>

                                <!-- Search Button -->
                                <x-primary-button>Search</x-primary-button>
                            </div>
                        </form>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Last Login
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ ucfirst($user->role) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="text-blue-600 hover:text-blue-900">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
