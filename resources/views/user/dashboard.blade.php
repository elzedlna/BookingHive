<x-app-layout>
    {{-- @dd($recommendedHotels); --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="mb-6 bg-white overflow-hidden shadow-sm rounded-lg p-4">
                <p class="text-sm text-gray-600">
                    Last login:
                    {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->setTimezone('Asia/Kuala_Lumpur')->format('M d, Y H:i') : 'First time login' }}
                </p>
            </div>

            <div
                class="flex flex-col md:flex-row items-center justify-between bg-blue-200/30 rounded-lg overflow-hidden mb-10">
                <div class="flex-1 p-8 space-y-4">
                    <h1 class="text-4xl font-bold text-gray-900">
                        Find your perfect place to stay
                    </h1>
                    <p class="text-lg text-gray-600">
                        Browse for more hotels according to your preference
                    </p>
                </div>
                <div class="flex-1 relative w-full h-64 md:h-auto">
                    <img src="https://images.unsplash.com/photo-1445991842772-097fea258e7b?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        alt="House with a clear sky" layout="fill" objectFit="cover" class="rounded-r-lg" />
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-6">My Bookings</h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Hotel</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dates</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rooms</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Price</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($bookings as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->hotel->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $booking->hotel->address }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                Check-in: {{ $booking->check_in->format('M d, Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Check-out: {{ $booking->check_out->format('M d, Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $booking->number_of_rooms }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            RM{{ number_format($booking->total_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $booking->status === 'confirmed'
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($booking->status === 'cancelled'
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if ($booking->status === 'pending')
                                                <form action="{{ route('user.booking.cancel', $booking) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @elseif($booking->status === 'confirmed')
                                                @if (!$booking->review)
                                                    <a href="{{ route('user.review.create', $booking) }}"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        Write Review
                                                    </a>
                                                @else
                                                    <a href="{{ route('user.booking.invoice', $booking) }}"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        View Invoice
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No bookings found.
                                            <a href="{{ route('user.booking') }}"
                                                class="text-blue-600 hover:underline">Book a hotel now!</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">
                            @if ($bookings->count() < 5)
                                Popular Hotels
                            @else
                                Suggested Hotels
                            @endif
                        </h2>
                        @if ($recommendedHotels->isNotEmpty())
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                Based on your booking history
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @forelse ($recommendedHotels as $hotel)
                            <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
                                <div class="relative h-48">
                                    @if ($hotel->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $hotel->images->first()->image_path) }}"
                                            alt="{{ $hotel->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                    @endif

                                </div>

                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $hotel->name }}</h3>
                                    <p class="mt-2 text-sm text-gray-600">{{ Str::limit($hotel->description, 100) }}
                                    </p>

                                    <div class="mt-4">
                                        <a href="{{ route('user.booking.create', $hotel) }}"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-4 text-gray-500">
                                @if ($bookings->count() < 5)
                                    Book more hotels to receive personalized recommendations!
                                @else
                                    No recommendations available at the moment.
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>