<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Equipment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $courtId = $request->get('court_id');

        // 1. Report: Bookings (Join: users + bookings + courts + payments)
        $bookingQuery = DB::table('bookings')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('courts', 'bookings.court_id', '=', 'courts.id')
            ->leftJoin('payments', 'bookings.id', '=', 'payments.booking_id')
            ->select(
                'bookings.id as booking_id',
                'users.name as user_name',
                'users.email as user_email',
                'users.phone as user_phone',
                'courts.name as court_name',
                'bookings.booking_date',
                'bookings.start_time',
                'bookings.end_time',
                'bookings.total_hours',
                'bookings.court_price',
                'bookings.equipment_price',
                'bookings.grand_total',
                'bookings.status as booking_status',
                'payments.status as payment_status',
                'payments.amount as paid_amount'
            )
            ->whereBetween('bookings.booking_date', [$startDate, $endDate]);

        if ($courtId) {
            $bookingQuery->where('bookings.court_id', $courtId);
        }

        $bookingReports = $bookingQuery->orderByDesc('bookings.booking_date')->get();

        // 2. Report: Revenue by Court (Join: courts + bookings + payments)
        $revenueByCourt = DB::table('courts')
            ->leftJoin('bookings', 'courts.id', '=', 'bookings.court_id')
            ->leftJoin('payments', function ($join) use ($startDate, $endDate) {
                $join->on('bookings.id', '=', 'payments.booking_id')
                     ->where('payments.status', '=', 'paid')
                     ->whereBetween('bookings.booking_date', [$startDate, $endDate]);
            })
            ->select(
                'courts.id',
                'courts.name as court_name',
                DB::raw('COUNT(DISTINCT bookings.id) as total_bookings'),
                DB::raw('COALESCE(SUM(payments.amount), 0) as total_revenue'),
                DB::raw('COALESCE(SUM(bookings.total_hours), 0) as total_hours')
            )
            ->groupBy('courts.id', 'courts.name')
            ->orderByDesc('total_revenue')
            ->get();

        // 3. Report: Equipment Rentals (Join: equipments + equipment_rentals + bookings + courts)
        $equipmentReports = DB::table('equipments')
            ->leftJoin('equipment_rentals', 'equipments.id', '=', 'equipment_rentals.equipment_id')
            ->leftJoin('bookings', function ($join) use ($startDate, $endDate) {
                $join->on('equipment_rentals.booking_id', '=', 'bookings.id')
                     ->whereBetween('bookings.booking_date', [$startDate, $endDate]);
            })
            ->select(
                'equipments.id',
                'equipments.name as equipment_name',
                'equipments.category',
                'equipments.rental_price',
                DB::raw('COALESCE(SUM(equipment_rentals.quantity), 0) as total_rented_qty'),
                DB::raw('COALESCE(SUM(equipment_rentals.subtotal), 0) as total_rental_income')
            )
            ->groupBy('equipments.id', 'equipments.name', 'equipments.category', 'equipments.rental_price')
            ->orderByDesc('total_rental_income')
            ->get();

        // Summary KPI Totals
        $summary = [
            'total_bookings' => $bookingReports->count(),
            'total_hours' => $bookingReports->sum('total_hours'),
            'total_revenue' => $bookingReports->where('payment_status', 'paid')->sum('grand_total'),
            'total_equipment_income' => $equipmentReports->sum('total_rental_income'),
        ];

        $courts = Court::all();

        return view('admin.reports.index', compact(
            'bookingReports',
            'revenueByCourt',
            'equipmentReports',
            'summary',
            'startDate',
            'endDate',
            'courtId',
            'courts'
        ));
    }
}
