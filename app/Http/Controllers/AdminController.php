<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hotel;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Mail\SeasonalEmail;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalHotels = Hotel::count();
        $totalReviews = Review::count();

        return view('admin.dashboard', compact('totalUsers', 'totalHotels', 'totalReviews'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Fetch paginated results
        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        // Set the default role to 'user'
        $defaultRole = 'user';
        return view('admin.users.create', compact('defaultRole'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|in:admin,hotel,user'
        ]);

        // Set default role to 'user' if not provided
        $validated['role'] = $validated['role'] ?? 'user';

        // Hash the password
        $validated['password'] = bcrypt($validated['password']);

        // Create the user
        User::create($validated);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,hotel,user'
        ]);

        $user->update($validated);

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully');
    }

    public function hotels(Request $request)
    {
        $query = Hotel::with(['user', 'roomTypes']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $hotels = $query->latest()->paginate(10);
        return view('admin.hotels.index', compact('hotels'));
    }

    public function createHotel()
    {
        $defaultRole = 'hotel';
        return view('admin.hotels.create', compact('defaultRole'));
    }

    public function storeHotel(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|in:admin,hotel,user'
        ]);

        // Set default role to 'hotel' if not provided
        $validated['role'] = $validated['role'] ?? 'hotel';

        // Hash the password
        $validated['password'] = bcrypt($validated['password']);

        // Create the user
        User::create($validated);

        return redirect()->route('admin.users')
            ->with('success', 'Hotel created successfully');
    }

    public function editHotel(Hotel $hotel)
    {
        return view('admin.hotels.edit', compact('hotel'));
    }

    public function updateHotel(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'description' => 'required|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|in:wifi,pool,spa,gym,restaurant,parking,bar,room_service,conference_room,laundry',
        ]);

        $hotel->update($validated);

        return redirect()->route('admin.hotels')
            ->with('success', 'Hotel updated successfully');
    }

    public function sendSeasonalEmail()
    {
        try {
            // Fetch only users with the role of 'user'
            $users = User::where('role', 'user')->get();

            foreach ($users as $user) {
                // Queue email with a delay to avoid rate-limiting issues
                Mail::to($user->email)
                    ->later(now()->addSeconds($users->search($user) * 5), new SeasonalEmail($user));
            }

            return redirect()->back()
                ->with('success', 'Seasonal emails queued for ' . $users->count() . ' users');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to queue emails: ' . $e->getMessage());
        }
    }
}
