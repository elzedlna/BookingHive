<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller
{
    public function dashboard()
    {
        $hotels = Hotel::where('user_id', Auth::id())
            ->with('images')
            ->latest()
            ->get();

        return view('hotel.dashboard', compact('hotels'));
    }

   public function store(Request $request)
    {
        $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'total_rooms' => 'required|integer|min:1',
        'address' => 'required|string|max:255',
        'description' => 'required|string',
        'amenities' => 'nullable|array',
        'amenities.*' => 'string|in:wifi,pool,spa,gym,restaurant,parking,bar,room_service,conference_room,laundry',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        'price_per_night' => 'required|numeric|min:0',
        'room_types' => 'required|array|min:1',
        'room_types.*.name' => 'required|string|max:255',
        'room_types.*.description' => 'required|string',
        'room_types.*.capacity' => 'required|integer|min:1',
        'room_types.*.price_per_night' => 'required|numeric|min:0',
        'room_types.*.total_rooms' => 'required|integer|min:1',
        'category_id' => 'required|exists:categories,id', // Ensure category exists
        'state' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['rating'] = 0.0;

        $validated['category_id'] = $request->category_id; // Assign category_id before storing
        $hotel = Hotel::create($validated);

     foreach ($request->room_types as $roomType) {
        $hotel->roomTypes()->create($roomType);
     }

     if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('hotel-images', 'public');
            $hotel->images()->create([
                'image_path' => $path
            ]);
         }
        }

        return redirect()
        ->route('hotel.dashboard')
        ->with('success', 'Hotel registered successfully!');
    }


    public function create()
    {
        return view('hotel.create');
    }

    public function edit(Hotel $hotel)
    {
        // Ensure user can only edit their own hotels
        if ($hotel->user_id !== Auth::id()) {
            abort(403);
        }

        return view('hotel.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        if ($hotel->user_id !== Auth::id()) {
           abort(403);
        }

        $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'total_rooms' => 'required|integer|min:1',
        'address' => 'required|string|max:255',
        'description' => 'required|string',
        'amenities' => 'nullable|array',
        'amenities.*' => 'string|in:wifi,pool,spa,gym,restaurant,parking,bar,room_service,conference_room,laundry',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'price_per_night' => 'required|numeric|min:0',
        'room_types' => 'required|array|min:1',
        'room_types.*.id' => 'nullable|exists:room_types,id',
        'room_types.*.name' => 'required|string|max:255',
        'room_types.*.description' => 'required|string',
        'room_types.*.capacity' => 'required|integer|min:1',
        'room_types.*.price_per_night' => 'required|numeric|min:0',
        'room_types.*.total_rooms' => 'required|integer|min:1',
        'category_id' => 'required|exists:categories,id', // Ensure category exists
        'state' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        ]);

         $validated['category_id'] = $request->category_id; // Assign category_id before updating
         $hotel->update($validated);

        foreach ($request->room_types as $roomTypeData) {
         if (isset($roomTypeData['id'])) {
            $hotel->roomTypes()->where('id', $roomTypeData['id'])->update($roomTypeData);
        }   else {
            $hotel->roomTypes()->create($roomTypeData);
        }
        }

         if ($request->hasFile('images')) {
         foreach ($request->file('images') as $image) {
            $path = $image->store('hotel-images', 'public');
            $hotel->images()->create([
                'image_path' => $path
            ]);
         }
        }

         return redirect()
        ->route('hotel.dashboard')
        ->with('success', 'Hotel updated successfully!');
    }

    public function destroy(Hotel $hotel)
    {
        // Ensure user can only delete their own hotels
        if ($hotel->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete associated images from storage
        foreach ($hotel->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        // Delete the hotel
        $hotel->delete();

        return redirect()
            ->route('hotel.dashboard')
            ->with('success', 'Hotel deleted successfully!');
    }

    public function bookings(Request $request)
    {
        $query = Booking::query()
            ->whereIn('hotel_id', Hotel::where('user_id', Auth::id())->pluck('id'))
            ->with(['user', 'hotel', 'roomType']);

        // Search by guest name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('check_in', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('check_in', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        return view('hotel.bookings', compact('bookings'));
    }

    public function confirmBooking(Booking $booking)
    {
        if ($booking->hotel->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if there are enough rooms available
        if ($booking->hotel->total_rooms < $booking->number_of_rooms) {
            return redirect()
                ->route('hotel.bookings')
                ->with('error', 'Not enough rooms available to confirm this booking.');
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update booking status
            $booking->update(['status' => 'confirmed']);

            // Reduce available rooms
            $booking->hotel->decrement('total_rooms', $booking->number_of_rooms);

            DB::commit();

            return redirect()
                ->route('hotel.bookings')
                ->with('success', 'Booking confirmed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('hotel.bookings')
                ->with('error', 'An error occurred while confirming the booking.');
        }
    }

    public function rejectBooking(Booking $booking)
    {
        if ($booking->hotel->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()
            ->route('hotel.bookings')
            ->with('success', 'Booking rejected successfully');
    }

    public function replyToReview(Request $request, Review $review)
    {
        // Ensure the hotel owner owns the hotel being reviewed
        if ($review->hotel->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'reply' => 'required|string|min:10'
        ]);

        // Create or update reply
        $review->reply()->updateOrCreate(
            ['review_id' => $review->id],
            [
                'user_id' => Auth::id(),
                'reply' => $validated['reply']
            ]
        );

        return back()->with('success', 'Reply posted successfully');
    }

    public function reviews(Request $request)
    {
        $query = Review::query()
            ->whereIn('hotel_id', Hotel::where('user_id', Auth::id())->pluck('id'))
            ->with(['user', 'hotel', 'reply'])
            ->latest();

        // Search by guest name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by has reply
        if ($request->filled('has_reply')) {
            if ($request->has_reply === 'yes') {
                $query->has('reply');
            } else {
                $query->doesntHave('reply');
            }
        }

        $reviews = $query->paginate(10);

        return view('hotel.reviews', compact('reviews'));
    }
}
