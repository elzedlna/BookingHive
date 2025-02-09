<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Search and Filter Form -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form action="{{ route('user.booking') }}" method="GET" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Search by name or location -->
                                <div>
                                    <x-input-label for="search" value="Search Hotels" />
                                    <x-text-input id="search" name="search" type="text" 
                                        class="mt-1 block w-full"
                                        placeholder="Hotel name or location..."
                                        value="{{ request('search') }}" />
                                </div>

                                <!-- Price Range -->
                                <div>
                                    <x-input-label for="price_range" value="Price Range" />
                                    <select id="price_range" name="price_range" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Any Price</option>
                                        <option value="0-100" {{ request('price_range') === '0-100' ? 'selected' : '' }}>Under RM100</option>
                                        <option value="100-200" {{ request('price_range') === '100-200' ? 'selected' : '' }}>RM100 - RM200</option>
                                        <option value="200-500" {{ request('price_range') === '200-500' ? 'selected' : '' }}>RM200 - RM500</option>
                                        <option value="500+" {{ request('price_range') === '500+' ? 'selected' : '' }}>Over RM500</option>
                                    </select>
                                </div>

                                <!-- Rating -->
                                <div>
                                    <x-input-label for="rating" value="Minimum Rating" />
                                    <select id="rating" name="rating" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Any Rating</option>
                                        @foreach(range(5, 1) as $rating)
                                            <option value="{{ $rating }}" {{ request('rating') == $rating ? 'selected' : '' }}>
                                                {{ $rating }}+ Stars
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Amenities -->
                                <div class="md:col-span-3">
                                    <x-input-label value="Amenities" class="mb-2" />
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        @php
                                            $amenities = ['wifi', 'pool', 'spa', 'gym', 'restaurant', 'parking', 'bar', 'room_service'];
                                            $selectedAmenities = request('amenities', []);
                                        @endphp
                                        @foreach($amenities as $amenity)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                                                    {{ in_array($amenity, $selectedAmenities) ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm">
                                                <span class="ml-2">{{ ucfirst($amenity) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('user.booking') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                                    Clear Filters
                                </a>
                                <x-primary-button>
                                    Search Hotels
                                </x-primary-button>
                            </div>
                        </form>
                    </div>

                    <h2 class="text-2xl font-semibold mb-6">Available Hotels</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($hotels as $hotel)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col h-full">
                                <!-- Hotel Image -->
                                <div class="h-48 overflow-hidden relative">
                                    @if ($hotel->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $hotel->images->first()->image_path) }}"
                                            alt="{{ $hotel->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400">No image available</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Hotel Info -->
                                <div class="p-4 flex flex-col flex-grow">
                                    <!-- Location -->
                                    <div class="text-xs text-gray-500 mb-1">{{ $hotel->address }}</div>

                                    <!-- Hotel Name -->
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        <a href="{{ route('user.hotel.show', $hotel) }}" class="hover:text-blue-600">
                                            {{ $hotel->name }}
                                        </a>
                                    </h3>

                                    <!-- Price and Rating Row -->
                                    <div class="flex justify-between items-center mb-4">
                                        <div class="text-gray-900">
                                            @if ($hotel->roomTypes->count() > 0)
                                                <span class="font-semibold">From
                                                    RM{{ number_format($hotel->roomTypes->min('price_per_night'), 2) }}</span>
                                            @else
                                                <span
                                                    class="font-semibold">RM{{ number_format($hotel->price_per_night, 2) }}</span>
                                            @endif
                                            <span class="text-sm text-gray-500">per night</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="flex">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $hotel->rating)
                                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-300" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span
                                                class="ml-1 text-sm font-semibold">{{ number_format($hotel->rating, 1) }}</span>
                                        </div>
                                    </div>

                                    <div class="flex-grow"></div>

                                    <!-- Book Now Button -->
                                    <div class="mt-auto">
                                        <a href="{{ route('user.hotel.show', $hotel) }}"
                                            class="block border w-full text-center bg-white px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-150 mb-2">
                                            View Hotel
                                        </a>
                                        <a href="{{ route('user.booking.create', $hotel) }}"
                                            class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-150">
                                            Book Now
                                        </a>
                                   </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-4">
                                <p class="text-gray-500">No hotels available at the moment.</p>
                            </div>
                        @endforelse
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
