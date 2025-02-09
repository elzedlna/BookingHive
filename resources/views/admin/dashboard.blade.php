<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-semibold mb-6">Admin Dashboard</h1>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900">Total Users</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $totalUsers }}</p>
                            <a href="{{ route('admin.users') }}" class="text-sm text-blue-600 hover:underline">View All Users</a>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900">Total Hotels</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $totalHotels }}</p>
                            <a href="{{ route('admin.hotels') }}" class="text-sm text-blue-600 hover:underline">View All Hotels</a>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900">Total Reviews</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $totalReviews }}</p>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="{{ route('admin.users') }}" 
                                    class="block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                                    Manage Users
                                </a>
                                <a href="{{ route('admin.hotels') }}" 
                                    class="block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                                    Manage Hotels
                                </a>
                                <a href="{{ route('admin.send-seasonal-email') }}" 
                                    class="block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                                    Send Seasonal Email
                                </a>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
