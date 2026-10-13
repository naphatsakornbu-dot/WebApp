<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Court;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_courts' => Court::count(),
            'bookings_today' => Booking::where('booking_date', $today)->count(),
            'bookings_this_month' => Booking::whereMonth('booking_date', $thisMonth)->whereYear('booking_date', $thisYear)->count(),
            'revenue_this_month' => Payment::where('status', 'paid')
                ->whereMonth('created_at', $thisMonth)
                ->whereYear('created_at', $thisYear)
                ->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'equipments_total' => Equipment::sum('total_stock'),
            'equipments_rented' => Equipment::sum(DB::raw('total_stock - available_stock')),
        ];

        // Top popular courts (Join courts & bookings)
        $topCourts = Court::withCount(['bookings' => function ($q) {
            $q->whereIn('status', ['confirmed', 'completed']);
        }])->orderByDesc('bookings_count')->take(5)->get();

        // Recent Bookings (with User, Court, Payment)
        $recentBookings = Booking::with(['user', 'court', 'payment'])
            ->latest()
            ->take(8)
            ->get();

        // Pending Slip Approvals
        $pendingSlips = Payment::with(['booking.user', 'booking.court'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'topCourts', 'recentBookings', 'pendingSlips'));
    }
}
