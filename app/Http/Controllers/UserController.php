<?php

namespace App\Http\Controllers;


use App\Models\Hotel;
use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use App\Http\Controllers\RecommendationController;

class UserController extends Controller
{
    protected $recommendationController;

    public function __construct(RecommendationController $recommendationController)
    {
        $this->recommendationController = $recommendationController;
    }

    public function dashboard()
    {
        $user = auth()->user();
        $bookings = $user->bookings()->with('hotel')->latest()->get();

        // Get recommended hotels
        $recommendationController = new RecommendationController();
        $recommendedHotels = $recommendationController->getRecommendedHotelsForUser($user->id);

        return view('user.dashboard', [
            'bookings' => $bookings,
            'recommendedHotels' => $recommendedHotels
        ]);
    }

    public function booking(Request $request)
    {
        $query = Hotel::query()->with(['images', 'roomTypes']);

        // Search by name or location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        if ($request->filled('price_range')) {
            [$min, $max] = explode('-', $request->price_range);
            if ($max === '+') {
                $query->whereHas('roomTypes', function($q) use ($min) {
                    $q->where('price_per_night', '>=', $min);
                });
            } else {
                $query->whereHas('roomTypes', function($q) use ($min, $max) {
                    $q->whereBetween('price_per_night', [$min, $max]);
                });
            }
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        // Filter by amenities
        if ($request->filled('amenities')) {
            foreach ($request->amenities as $amenity) {
                $query->whereJsonContains('amenities', $amenity);
            }
        }

        $hotels = $query->paginate(9);
        return view('user.booking', compact('hotels'))->with('search', $request->all());
    }

    public function edit()
    {
        return view('user.edit');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = User::find(Auth::id());
        $user->fill($validated);
        $user->save();

        return redirect()->route('user.edit')->with('status', 'profile-updated');
    }

    public function bookingCreate(Request $request, Hotel $hotel)
    {
        $roomType = null;
        if ($request->has('room_type')) {
            $roomType = $hotel->roomTypes()->findOrFail($request->room_type);
        }
        
        return view('user.booking-create', compact('hotel', 'roomType'));
    }

    public function bookingStore(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
            'number_of_rooms' => 'required|integer|min:1'
        ]);

        $roomType = $hotel->roomTypes()->findOrFail($validated['room_type_id']);
        
        // Check if enough rooms are available
        if ($roomType->total_rooms < $validated['number_of_rooms']) {
            return back()->withErrors(['number_of_rooms' => 'Not enough rooms available.']);
        }

        // Calculate total price
        $checkIn = new \DateTime($validated['check_in']);
        $checkOut = new \DateTime($validated['check_out']);
        $nights = $checkIn->diff($checkOut)->days;
        $totalPrice = $roomType->price_per_night * $validated['number_of_rooms'] * $nights;

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'hotel_id' => $hotel->id,
            'room_type_id' => $validated['room_type_id'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'number_of_rooms' => $validated['number_of_rooms'],
            'price_per_night' => $roomType->price_per_night,
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        return redirect()
            ->route('user.payment.show', $booking)
            ->with('success', 'Please complete your payment to confirm the booking.');
    }

    public function cancelBooking(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // If the booking was confirmed, increase the room count back
            if ($booking->status === 'confirmed') {
                $booking->hotel->increment('total_rooms', $booking->number_of_rooms);
            }

            $booking->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()
                ->route('user.dashboard')
                ->with('success', 'Booking cancelled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('user.dashboard')
                ->with('error', 'An error occurred while cancelling the booking.');
        }
    }

    public function reviewCreate(Booking $booking)
    {
        // Check if user owns the booking and it's confirmed
        if ($booking->user_id !== Auth::id() || $booking->status !== 'confirmed') {
            abort(403);
        }

        // Check if review already exists
        if ($booking->review) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You have already reviewed this booking.');
        }

        return view('user.review-create', compact('booking'));
    }

    public function reviewStore(Request $request, Booking $booking)
    {
        // Validate ownership and status
        if ($booking->user_id !== Auth::id() || $booking->status !== 'confirmed') {
            abort(403);
        }

        // Validate input
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10'
        ]);

        try {
            DB::beginTransaction();

            // Create review
            $review = Review::create([
                'user_id' => Auth::id(),
                'hotel_id' => $booking->hotel_id,
                'booking_id' => $booking->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment']
            ]);

            // Update hotel's average rating
            $booking->hotel->updateRating();

            DB::commit();

            return redirect()
                ->route('user.dashboard')
                ->with('success', 'Thank you for your review!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Add debugging information
            return redirect()
                ->route('user.dashboard')
                ->with('error', 'Error creating review: ' . $e->getMessage());
        }
    }

    public function help()
    {
        return view('user.help');
    }

    public function contact()
    {
        return view('user.contact');
    }

    public function showHotel(Hotel $hotel)
    {
        // Load the hotel with its reviews and reviewers
        $hotel->load(['reviews' => function ($query) {
            $query->latest();
        }, 'reviews.user']);

        return view('user.hotel-show', compact('hotel'));
    }

    public function showPayment(Booking $booking)
    {
        // Ensure user owns the booking and it's pending
        if ($booking->user_id !== Auth::id() || $booking->status !== 'pending') {
            abort(403);
        }

        return view('user.payment', compact('booking'));
    }

    public function processPayment(Request $request, Booking $booking)
    {
        // Validate ownership and status
        if ($booking->user_id !== Auth::id() || $booking->status !== 'pending') {
            abort(403);
        }

        // Validate payment details
        $request->validate([
            'card_number' => 'required|string|min:19|max:19',
            'expiry' => 'required|string|size:5',
            'cvv' => 'required|string|size:3',
            'card_holder' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
        ]);

        // Mock successful payment
        // In a real application, you would integrate with a payment gateway here
        
        // Update booking status to confirmed instead of paid
        $booking->update(['status' => 'confirmed']);

        // Decrease available rooms
        $booking->roomType->decrement('total_rooms', $booking->number_of_rooms);

        return redirect()
            ->route('user.dashboard')
            ->with('success', new HtmlString('Payment successful! Your booking has been confirmed. <a href="' . route('user.booking.invoice', $booking) . '" class="underline">View Invoice</a>'));
    }

    public function showInvoice(Booking $booking)
    {
        // Ensure user owns the booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user.invoice', compact('booking'));
    }
}
