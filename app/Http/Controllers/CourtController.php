<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Booking;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CourtController extends Controller
{
    public function index(Request $request)
    {
        $query = Court::query();

        if ($request->filled('floor_type')) {
            $query->where('floor_type', 'like', '%' . $request->floor_type . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $courts = $query->with('reviews')->get();

        return view('courts.index', compact('courts'));
    }

    public function show(Court $court, Request $request)
    {
        $selectedDate = $request->get('date', Carbon::today()->toDateString());
        
        $court->load(['reviews.user', 'maintenances']);
        
        // Compute hourly availability for the selected date
        $openHour = (int) Carbon::createFromTimeString($court->open_time)->format('H');
        $closeHour = (int) Carbon::createFromTimeString($court->close_time)->format('H');

        $bookings = Booking::where('court_id', $court->id)
            ->where('booking_date', $selectedDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        $maintenances = Maintenance::where('court_id', $court->id)
            ->where('start_date', '<=', $selectedDate)
            ->where('end_date', '>=', $selectedDate)
            ->get();

        $timeSlots = [];
        for ($hour = $openHour; $hour < $closeHour; $hour++) {
            $slotStart = sprintf('%02d:00:00', $hour);
            $slotEnd = sprintf('%02d:00:00', $hour + 1);
            $slotLabel = sprintf('%02d:00 - %02d:00', $hour, $hour + 1);

            $status = 'available';
            $info = '';

            // Check maintenance
            foreach ($maintenances as $m) {
                if ($m->start_time < $slotEnd && $m->end_time > $slotStart) {
                    $status = 'maintenance';
                    $info = $m->title;
                    break;
                }
            }

            // Check booking
            if ($status === 'available') {
                foreach ($bookings as $b) {
                    if ($b->start_time < $slotEnd && $b->end_time > $slotStart) {
                        $status = 'booked';
                        $info = 'มีผู้จองแล้ว';
                        break;
                    }
                }
            }

            $timeSlots[] = [
                'start' => sprintf('%02d:00', $hour),
                'end' => sprintf('%02d:00', $hour + 1),
                'label' => $slotLabel,
                'status' => $status,
                'info' => $info,
            ];
        }

        return view('courts.show', compact('court', 'selectedDate', 'timeSlots'));
    }
}
