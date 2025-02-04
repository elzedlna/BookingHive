<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    private const MIN_BOOKINGS_FOR_RECOMMENDATIONS = 5;
    private const NUM_RECOMMENDATIONS = 3;

    public function index()
    {
       
        $hotels = $this->getRecommendedHotelsForDashboard($user->id);

        $message = null;
        if ($hotels->isEmpty()) {
            $message = "No personalized recommendations yet. Start booking to get tailored suggestions!";
            $hotels = $this->getPopularHotels(self::NUM_RECOMMENDATIONS);
        }

        return view('user.recommend', compact('hotels', 'message'));
    }

    public function getRecommendedHotelsForDashboard($userId)
    {
        $user = auth()->user();
         if (!$user) {
            return response()->json(["error" => "User not authenticated"], 401);
        }

        $numBookings = Booking::where('user_id', $userId)->count();
        if ($numBookings < self::MIN_BOOKINGS_FOR_RECOMMENDATIONS) {
            return $this->getPopularHotels(self::NUM_RECOMMENDATIONS);
        }



        $numBookings = Booking::where('user_id', $userId)->count();
        if ($numBookings < self::MIN_BOOKINGS_FOR_RECOMMENDATIONS) {
            return $this->getPopularHotels(self::NUM_RECOMMENDATIONS);
        }

        $userBookings = Booking::where('user_id', $userId)->with('hotel')->get();
        //dd($userBookings); // Debugging: Check if bookings are retrieved
        
        // Debugging - Check if the count is correct
        //dd($numBookings); 

        // If user has less than 5 bookings, return popular hotels
        if ($numBookings < self::MIN_BOOKINGS_FOR_RECOMMENDATIONS) {
            return $this->getPopularHotels(self::NUM_RECOMMENDATIONS);
        }

        // Extract user preferences (category, state, city)
        $preferences = $this->extractUserPreferences($userBookings);

        // Debugging - Check extracted preferences
        //dd($preferences);

        // Get recommendations using collaborative filtering
        return $this->getCollaborativeRecommendations($userId, $preferences);
    }

    public function getRecommendedHotelsForUser($userId, $limit = 3)
    {
        return $this->getRecommendedHotelsForDashboard($userId, $limit);
    }


    private function extractUserPreferences($userBookings)
    {
        $preferences = [
            'category' => $userBookings->pluck('hotel.category_id')->unique()->toArray(),
            'state' => $userBookings->pluck('hotel.state')->unique()->toArray(),
            'city' => $userBookings->pluck('hotel.city')->unique()->toArray(),
        ];

        //dd($preferences); // Debugging: Check extracted preferences
        return $preferences;
    }

    private function getCollaborativeRecommendations($userId, $preferences)
    {
        // Find similar users based on common bookings
        $similarUsers = $this->findSimilarUsers($userId);
        if ($similarUsers->isEmpty()) {
            return collect();
        }

        // Debugging - Check similar users found
        //dd($similarUsers);

        if ($similarUsers->isEmpty()) {
            return collect();
        }

        $hotels = Hotel::whereIn('category_id', $preferences['category'])
            ->whereIn('state', $preferences['state'])
            ->whereIn('city', $preferences['city'])
            ->whereHas('bookings', function ($query) use ($similarUsers) {
                $query->whereIn('user_id', $similarUsers);
            })
            ->whereDoesntHave('bookings', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit(self::NUM_RECOMMENDATIONS)
            ->get();

        // Debugging - Check recommended hotels
        //dd($hotels);

        return $hotels;
    }

    private function findSimilarUsers($userId)
    {
        $similarUsers = DB::table('bookings as b1')
            ->join('bookings as b2', 'b1.hotel_id', '=', 'b2.hotel_id')
            ->where('b1.user_id', $userId)
            ->where('b2.user_id', '!=', $userId)
            ->groupBy('b2.user_id')
            ->select('b2.user_id', DB::raw('COUNT(*) as common_bookings'))
            ->orderByDesc('common_bookings')
            ->limit(5)
            ->pluck('b2.user_id');

        //dd ($similarUsers);
        return $similarUsers;
    }

    private function getPopularHotels($limit)
    {
        return Hotel::withAvg('reviews', 'rating')
            ->having('reviews_avg_rating', '>=', 4)
            ->orderByDesc('reviews_avg_rating')
            ->take($limit)
            ->get();
    }
}