<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Review;
use App\Models\PlayerMatch;
use App\Models\Booking;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $courts = Court::where('status', 'available')->with('reviews')->get();
        $recentReviews = Review::with(['user', 'court'])->latest()->take(6)->get();
        $openMatches = PlayerMatch::with(['booking.court', 'booking.user'])
            ->where('status', 'open')
            ->whereHas('booking', function ($q) {
                $q->where('booking_date', '>=', now()->toDateString());
            })
            ->latest()
            ->take(3)
            ->get();

        $stats = [
            'total_courts' => Court::count(),
            'total_bookings' => Booking::whereIn('status', ['confirmed', 'completed'])->count(),
            'average_rating' => round(Review::avg('rating') ?? 5.0, 1),
        ];

        return view('home', compact('courts', 'recentReviews', 'openMatches', 'stats'));
    }
}
