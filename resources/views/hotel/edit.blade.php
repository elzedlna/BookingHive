<x-app-layout>
    <div class="min-h-screen">
        <!-- Main Content -->
        <div class="container mx-auto px-4 py-8">
            <!-- Header Section -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-800">Edit Hotel</h1>
                <p class="text-gray-600 mt-2">Update your hotel information</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-lg max-w-4xl mx-auto">
                <div class="p-6">
                    <form action="{{ route('hotel.update', $hotel->id) }}" method="POST" class="space-y-8"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information Section -->
                        <div class="border-b pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">
                                Basic Information
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hotel Name</label>
                                    <input type="text" name="name" value="{{ old('name', $hotel->name) }}"
                                        required
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $hotel->email) }}"
                                        required
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="text" name="phone" value="{{ old('phone', $hotel->phone) }}"
                                        required
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Rooms</label>
                                    <input type="number" name="total_rooms"
                                        value="{{ old('total_rooms', $hotel->total_rooms) }}" required min="1"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Price per Night
                                        (RM)</label>
                                    <input type="number" name="price_per_night"
                                        value="{{ old('price_per_night', $hotel->price_per_night) }}" required
                                        step="0.01" min="0"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Location Section -->
                        <div class="border-b pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">Location & Description</h2>
                            <div class="space-y-6">
                                
                                <!-- Hotel Category Dropdown -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hotel Category</label>
                                    <select name="category_id" required class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                        <option value="">Select a Category</option> <!-- Default option -->
                                        @foreach(App\Models\Category::all() as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $hotel->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- State Input -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                    <input type="text" name="state" 
                                        value="{{ old('state', $hotel->state) }}" required
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="E.g., Selangor, Johor">
                                </div>

                                <!-- City Input -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                    <input type="text" name="city" 
                                        value="{{ old('city', $hotel->city) }}" required
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="E.g., Kuala Lumpur, Penang">
                                </div>

                                <!-- Address Input -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <input type="text" name="address" 
                                        value="{{ old('address', $hotel->address) }}" required
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Description Textarea -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea name="description" rows="4" required
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Describe your hotel...">{{ old('description', $hotel->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Amenities Section -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">
                                Hotel Amenities
                            </h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @php
                                    $amenities = [
                                        'wifi' => 'Wi-Fi',
                                        'pool' => 'Swimming Pool',
                                        'spa' => 'Spa Services',
                                        'gym' => 'Fitness Center',
                                        'restaurant' => 'Restaurant',
                                        'parking' => 'Parking',
                                        'bar' => 'Bar/Lounge',
                                        'room_service' => 'Room Service',
                                        'conference_room' => 'Conference Room',
                                        'laundry' => 'Laundry',
                                    ];
                                @endphp

                                @foreach ($amenities as $value => $label)
                                    <div class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50">
                                        <input type="checkbox" name="amenities[]" value="{{ $value }}"
                                            class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                            {{ in_array($value, old('amenities', $hotel->amenities ?? [])) ? 'checked' : '' }}>
                                        <label class="text-sm text-gray-700">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Current Images Section -->
                        @if ($hotel->images->count() > 0)
                            <div class="border-b pb-6">
                                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                                    Current Images
                                </h2>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach ($hotel->images as $image)
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Hotel image"
                                                class="w-full h-32 object-cover rounded-lg">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Add New Images Section -->
                        <div class="border-b pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">
                                Add New Images
                            </h2>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload New
                                        Images</label>
                                    <input type="file" name="images[]" multiple accept="image/*"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <p class="text-sm text-gray-500 mt-1">You can select multiple images</p>
                                </div>
                            </div>
                        </div>

                        <!-- Room Types Section -->
                        <div class="border-b pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">
                                Room Types
                            </h2>
                            <div id="room-types-container" class="space-y-6">
                                @foreach($hotel->roomTypes as $index => $roomType)
                                    <div class="p-4 border rounded-lg room-type">
                                        <input type="hidden" name="room_types[{{ $index }}][id]" value="{{ $roomType->id }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Room Name</label>
                                                <input type="text" name="room_types[{{ $index }}][name]" 
                                                    value="{{ old('room_types.'.$index.'.name', $roomType->name) }}" required
                                                    class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Capacity</label>
                                                <input type="number" name="room_types[{{ $index }}][capacity]" 
                                                    value="{{ old('room_types.'.$index.'.capacity', $roomType->capacity) }}" required min="1"
                                                    class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Price per Night</label>
                                                <input type="number" name="room_types[{{ $index }}][price_per_night]" 
                                                    value="{{ old('room_types.'.$index.'.price_per_night', $roomType->price_per_night) }}" 
                                                    required step="0.01" min="0"
                                                    class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Total Rooms</label>
                                                <input type="number" name="room_types[{{ $index }}][total_rooms]" 
                                                    value="{{ old('room_types.'.$index.'.total_rooms', $roomType->total_rooms) }}" 
                                                    required min="1"
                                                    class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                                <textarea name="room_types[{{ $index }}][description]" required
                                                    class="w-full px-4 py-2 rounded-lg border border-gray-300" rows="3">{{ old('room_types.'.$index.'.description', $roomType->description) }}</textarea>
                                            </div>
                                        </div>
                                        <button type="button" onclick="removeRoomType(this)"
                                            class="mt-4 text-red-600 hover:text-red-800">Remove Room Type</button>
                                    </div>
                                @endforeach

                                <!-- Template for new room types -->
                                <template id="room-type-template">
                                    <div class="p-4 border rounded-lg room-type">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Room Name</label>
                                                <input type="text" name="room_types[INDEX][name]" required
                                                    class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Capacity</label>
                                                <input type="number" name="room_types[INDEX][capacity]" required min="1"
                                                    class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Price per Night</label>
                                                <input type="number" name="room_types[INDEX][price_per_night]" required step="0.01" min="0"
                                                    class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Total Rooms</label>
                                                <input type="number" name="room_types[INDEX][total_rooms]" required min="1"
                                                    class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                            </div>
                                            <div class="col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                                <textarea name="room_types[INDEX][description]" required
                                                    class="w-full px-4 py-2 rounded-lg border border-gray-300" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <button type="button" onclick="removeRoomType(this)"
                                            class="mt-4 text-red-600 hover:text-red-800">Remove Room Type</button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" onclick="addRoomType()"
                                class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Add Room Type
                            </button>
                        </div>

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <p class="text-red-800 font-medium">Please correct the following errors:</p>
                                </div>
                                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-6">
                            <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Update Hotel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Add initial room type when page loads
    document.addEventListener('DOMContentLoaded', function() {
        addRoomType();
    });

    function addRoomType() {
        const container = document.getElementById('room-types-container');
        const template = document.getElementById('room-type-template');
        const clone = template.content.cloneNode(true);
        
        // Update indices
        const index = container.children.length;
        const elements = clone.querySelectorAll('[name*="INDEX"]');
        elements.forEach(element => {
            element.name = element.name.replace('INDEX', index);
        });

        container.appendChild(clone);
    }

    function removeRoomType(button) {
        const container = document.getElementById('room-types-container');
        if (container.children.length > 1) {
            button.closest('.room-type').remove();
            // Reindex remaining room types
            const roomTypes = container.children;
            Array.from(roomTypes).forEach((roomType, index) => {
                const inputs = roomType.querySelectorAll('[name*="room_types"]');
                inputs.forEach(input => {
                    input.name = input.name.replace(/room_types\[\d+\]/, `room_types[${index}]`);
                });
            });
        }
    }
</script>
