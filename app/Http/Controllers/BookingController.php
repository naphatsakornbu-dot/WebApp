<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Equipment;
use App\Models\EquipmentRental;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $courtId = $request->get('court_id');
        $courts = Court::where('status', 'available')->get();
        $selectedCourt = $courtId ? Court::find($courtId) : $courts->first();
        $equipments = Equipment::where('status', 'active')->where('available_stock', '>', 0)->get();

        $defaultDate = $request->get('date', Carbon::today()->toDateString());
        $defaultStartTime = $request->get('start_time', '18:00');
        $defaultEndTime = $request->get('end_time', '19:00');

        return view('bookings.create', compact('courts', 'selectedCourt', 'equipments', 'defaultDate', 'defaultStartTime', 'defaultEndTime'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id' => ['required', 'exists:courts,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'notes' => ['nullable', 'string', 'max:500'],
            'equipments' => ['nullable', 'array'],
            'equipments.*' => ['nullable', 'integer', 'min:0'],
        ], [
            'court_id.required' => 'กรุณาเลือกสนาม',
            'booking_date.required' => 'กรุณาเลือกวันที่ต้องการจอง',
            'booking_date.after_or_equal' => 'วันที่จองต้องเป็นวันนี้หรือวันข้างหน้าเท่านั้น (ห้ามจองย้อนหลัง)',
            'start_time.required' => 'กรุณาระบุเวลาเริ่มเล่น',
            'end_time.required' => 'กรุณาระบุเวลาสิ้นสุด',
            'end_time.after' => 'เวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น',
        ]);

        $court = Court::findOrFail($validated['court_id']);

        // Check court status
        if ($court->status !== 'available') {
            return back()->withInput()->with('error', 'ขออภัย สนามนี้กำลังปิดปรับปรุงหรือไม่พร้อมให้บริการ');
        }

        // Format times
        $startTime = $validated['start_time'] . ':00';
        $endTime = $validated['end_time'] . ':00';

        // Check Operating Hours
        $courtOpen = Carbon::createFromTimeString($court->open_time)->format('H:i:s');
        $courtClose = Carbon::createFromTimeString($court->close_time)->format('H:i:s');

        if ($startTime < $courtOpen || $endTime > $courtClose) {
            return back()->withInput()->with('error', "เวลาที่เลือกอยู่นอกเวลาทำการของสนาม ({$court->open_time} - {$court->close_time} น.)");
        }

        // 1. Check Time Overlap against existing Bookings
        $conflictBooking = Booking::where('court_id', $court->id)
            ->whereDate('booking_date', $validated['booking_date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->first();

        if ($conflictBooking) {
            $formattedStart = substr($conflictBooking->start_time, 0, 5);
            $formattedEnd = substr($conflictBooking->end_time, 0, 5);
            return back()->withInput()->with('error', "ขออภัย ช่วงเวลานี้มีการจองแล้ว ({$formattedStart} - {$formattedEnd} น.) กรุณาเลือกช่วงเวลาอื่น");
        }

        // 2. Check Time Overlap against Maintenance
        $conflictMaintenance = Maintenance::where('court_id', $court->id)
            ->whereDate('start_date', '<=', $validated['booking_date'])
            ->whereDate('end_date', '>=', $validated['booking_date'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })
            ->first();

        if ($conflictMaintenance) {
            return back()->withInput()->with('error', "ขออภัย สนามปิดปรับปรุงในช่วงเวลานี้ ({$conflictMaintenance->title})");
        }

        // Calculate hours and court price
        $startCarbon = Carbon::createFromFormat('H:i', $validated['start_time']);
        $endCarbon = Carbon::createFromFormat('H:i', $validated['end_time']);
        $totalMinutes = abs($startCarbon->diffInMinutes($endCarbon));
        $totalHours = round($totalMinutes / 60, 2);

        if ($totalHours <= 0) {
            return back()->withInput()->with('error', 'ระยะเวลาการจองไม่ถูกต้อง');
        }

        $courtPrice = $totalHours * (float)$court->price_per_hour;
        $equipmentPrice = 0.00;
        $rentalItems = [];

        // Check and validate equipment rentals
        if (!empty($validated['equipments'])) {
            foreach ($validated['equipments'] as $eqId => $quantity) {
                $qty = (int)$quantity;
                if ($qty > 0) {
                    $eq = Equipment::find($eqId);
                    if (!$eq || $eq->status !== 'active') {
                        return back()->withInput()->with('error', 'มีรายการอุปกรณ์ที่ไม่พร้อมให้บริการ');
                    }
                    if ($eq->available_stock < $qty) {
                        return back()->withInput()->with('error', "อุปกรณ์ \"{$eq->name}\" มีจำนวนคงเหลือไม่พอ (เหลือ {$eq->available_stock} ชิ้น)");
                    }

                    $subtotal = $qty * (float)$eq->rental_price;
                    $equipmentPrice += $subtotal;
                    $rentalItems[] = [
                        'equipment' => $eq,
                        'quantity' => $qty,
                        'unit_price' => $eq->rental_price,
                        'subtotal' => $subtotal,
                    ];
                }
            }
        }

        $grandTotal = $courtPrice + $equipmentPrice;

        // Perform Database Transaction
        $booking = DB::transaction(function () use ($validated, $startTime, $endTime, $totalHours, $courtPrice, $equipmentPrice, $grandTotal, $rentalItems) {
            $b = Booking::create([
                'user_id' => Auth::id(),
                'court_id' => $validated['court_id'],
                'booking_date' => $validated['booking_date'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'total_hours' => $totalHours,
                'court_price' => $courtPrice,
                'equipment_price' => $equipmentPrice,
                'grand_total' => $grandTotal,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create initial payment record
            Payment::create([
                'booking_id' => $b->id,
                'amount' => $grandTotal,
                'status' => 'unpaid',
            ]);

            // Save equipment rentals and reduce stock
            foreach ($rentalItems as $item) {
                EquipmentRental::create([
                    'booking_id' => $b->id,
                    'equipment_id' => $item['equipment']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Decrement available stock
                $item['equipment']->decrement('available_stock', $item['quantity']);
            }

            return $b;
        });

        return redirect()->route('payments.show', $booking)->with('success', 'บันทึกการจองสนามเรียบร้อยแล้ว กรุณาชำระเงินและแนบสลิปเพื่อยืนยันการจอง');
    }

    public function myBookings(Request $request)
    {
        $status = $request->get('status');
        $query = Booking::where('user_id', Auth::id())
            ->with(['court', 'payment', 'equipmentRentals.equipment', 'playerMatch', 'review'])
            ->latest('booking_date')
            ->latest('start_time');

        if ($status && in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('bookings.my', compact('bookings', 'status'));
    }

    public function show(Booking $booking)
    {
        // Authorization: only the owner or admin can view
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงรายการจองนี้');
        }

        $booking->load(['court', 'payment', 'equipmentRentals.equipment', 'playerMatch.members.user', 'review']);

        return view('bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        // Authorization
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'คุณไม่มีสิทธิ์ยกเลิกรายการจองนี้');
        }

        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'ไม่สามารถยกเลิกรายการจองที่เสร็จสิ้นหรือยกเลิกไปแล้วได้');
        }

        DB::transaction(function () use ($booking) {
            $booking->status = 'cancelled';
            $booking->save();

            // Restore equipment stock
            foreach ($booking->equipmentRentals as $rental) {
                if ($rental->equipment) {
                    $rental->equipment->increment('available_stock', $rental->quantity);
                }
            }

            // If match was opened, cancel it too
            if ($booking->playerMatch) {
                $booking->playerMatch->status = 'cancelled';
                $booking->playerMatch->save();
            }
        });

        return back()->with('success', 'ยกเลิกรายการจองสนามและคืนสต็อกอุปกรณ์เรียบร้อยแล้ว');
    }
}
