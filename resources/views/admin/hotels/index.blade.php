<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-3">Manage Hotels</h2>
                    <div class="mb-2">
                        <a href="{{ route('admin.hotels.create') }}"
                            class="text-white bg-blue-600 px-4 py-1 text-lg rounded">Add
                            Hotel</a>
                    </div>

                    <!-- Search Form -->
                    <div class="mb-6">
                        <form action="{{ route('admin.hotels') }}" method="GET">
                            <div class="flex gap-4">
                                <x-text-input id="search" name="search" type="text" class="flex-1"
                                    placeholder="Search hotels by name, address, or owner..."
                                    value="{{ request('search') }}" />
                                <x-primary-button>Search</x-primary-button>
                            </div>
                        </form>
                    </div>

                    <!-- Hotels Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Hotel Name
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Owner
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Address
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rating
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($hotels as $hotel)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $hotel->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $hotel->user->name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $hotel->address }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="text-sm">{{ number_format($hotel->rating, 1) }}</span>
                                                <svg class="w-4 h-4 text-yellow-400 ml-1" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('admin.hotels.edit', $hotel) }}"
                                                class="text-blue-600 hover:text-blue-900">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $hotels->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
