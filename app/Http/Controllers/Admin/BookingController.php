<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'court', 'payment'])->latest('booking_date')->latest('start_time');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('court_id')) {
            $query->where('court_id', $request->court_id);
        }

        if ($request->filled('date')) {
            $query->where('booking_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(15)->withQueryString();
        $courts = Court::all();

        return view('admin.bookings.index', compact('bookings', 'courts'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'court', 'payment', 'equipmentRentals.equipment', 'playerMatch.members.user', 'review']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
        ]);

        $oldStatus = $booking->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($booking, $oldStatus, $newStatus) {
            $booking->status = $newStatus;
            $booking->save();

            // If cancelled, restore stock
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($booking->equipmentRentals as $rental) {
                    if ($rental->equipment) {
                        $rental->equipment->increment('available_stock', $rental->quantity);
                    }
                }
            }

            // If changing from cancelled back to active, re-deduct
            if ($oldStatus === 'cancelled' && in_array($newStatus, ['pending', 'confirmed'])) {
                foreach ($booking->equipmentRentals as $rental) {
                    if ($rental->equipment) {
                        $rental->equipment->decrement('available_stock', $rental->quantity);
                    }
                }
            }
        });

        return back()->with('success', "อัปเดตสถานะการจองเป็น {$newStatus} เรียบร้อยแล้ว");
    }
}
