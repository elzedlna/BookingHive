<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    private const NUM_RECOMMENDATIONS = 3;

    public function index()
    {
        $user = auth()->user();
        $hotels = $this->getRecommendedHotelsForDashboard($user->id);
        return view('user.recommend', compact('hotels'));
    }

    public function getRecommendedHotelsForUser($userId, $limit = 3)
    {
        return $this->getRecommendedHotelsForDashboard($userId, $limit);
    }

    public function getRecommendedHotelsForDashboard($userId, $limit = 3)
    {
        try {
            // Get user's booking history with hotel information
            $userBookings = Booking::with('hotel')

                ->where('user_id', $userId)
                ->get();

            // If user has any bookings, get recommendations based on their most booked category
            if ($userBookings->isNotEmpty()) {
                // Get the most frequently booked category
                $categoryPreference = $this->getMostBookedCategory($userBookings);

                if ($categoryPreference) {
                    Log::info("User's most booked category: " . $categoryPreference);

                    // Get recommendations from the same category
                    $recommendations = $this->getHotelsFromCategory($categoryPreference, $limit);

                    if ($recommendations->isNotEmpty()) {
                        return $recommendations;
                    }
                }
            }

            // Fallback to popular hotels if no bookings or no recommendations found
            return $this->getPopularHotels($limit);
        } catch (\Exception $e) {
            Log::error("Error in recommendations: " . $e->getMessage());
            return $this->getPopularHotels($limit);
        }
    }

    private function getMostBookedCategory($userBookings)
    {
        $categoryCount = [];

        foreach ($userBookings as $booking) {
            if ($booking->hotel) {
                $categoryId = $booking->hotel->category_id;
                $categoryCount[$categoryId] = ($categoryCount[$categoryId] ?? 0) + 1;
            }
        }

        // Return the category with the most bookings
        if (!empty($categoryCount)) {
            arsort($categoryCount);
            return key($categoryCount);
        }

        return null;
    }

    private function getHotelsFromCategory($categoryId, $limit)
    {
        return Hotel::where('category_id', $categoryId)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->with(['images', 'reviews'])
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->limit($limit)
            ->get();
    }

    private function getPopularHotels($limit)
    {
        return Hotel::withCount('reviews')
            ->with(['images', 'reviews'])  // ✅ Fetch images along with reviews
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->limit($limit)
            ->get();
    }
}