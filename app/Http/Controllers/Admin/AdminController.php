<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Admin Dashboard Overview
     */
    public function dashboard(): View
    {
        $today = Carbon::today()->format('Y-m-d');

        $pendingPaymentsCount = Payment::where('status', 'pending')->count();
        $todayBookingsCount = Booking::where('booking_date', $today)->count();
        $totalRevenue = Payment::where('status', 'verified')->sum('amount');
        $activeCourtsCount = Court::where('is_active', true)->count();

        // Bank Accounts with pending incoming slips count for each account
        $bankAccounts = BankAccount::withCount(['payments as pending_payments_count' => function ($query) {
            $query->where('status', 'pending');
        }])->get();

        // Recent pending payments awaiting verification
        $pendingPayments = Payment::with(['booking.court', 'bankAccount'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Today's bookings
        $recentBookings = Booking::with(['court', 'payment.bankAccount'])
            ->where('booking_date', $today)
            ->orderBy('start_time', 'asc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'pendingPaymentsCount',
            'todayBookingsCount',
            'totalRevenue',
            'activeCourtsCount',
            'bankAccounts',
            'pendingPayments',
            'recentBookings'
        ));
    }

    /**
     * Dedicated Payment Verification Center (ตรวจสอบยอดเงินและสลิป)
     * Clearly presents venue bank accounts and matches incoming payments.
     */
    public function payments(Request $request): View
    {
        $status = $request->query('status', 'pending'); // Default to pending for immediate attention
        $bankAccountId = $request->query('bank_account_id');
        $search = $request->query('q');

        // Bank accounts with pending counts for quick selection & balance checking
        $bankAccounts = BankAccount::withCount(['payments as pending_count' => function ($q) {
            $q->where('status', 'pending');
        }])->get();

        $query = Payment::with(['booking.court', 'bankAccount', 'verifiedBy'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($bankAccountId) {
            $query->where('bank_account_id', $bankAccountId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_code', 'LIKE', "%{$search}%")
                  ->orWhere('transaction_reference', 'LIKE', "%{$search}%")
                  ->orWhere('sender_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('booking', function ($b) use ($search) {
                      $b->where('booking_code', 'LIKE', "%{$search}%")
                        ->orWhere('customer_name', 'LIKE', "%{$search}%")
                        ->orWhere('customer_phone', 'LIKE', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(12)->withQueryString();

        // Selected bank account details for admin's quick reference
        $selectedAccount = $bankAccountId ? BankAccount::find($bankAccountId) : null;

        return view('admin.payments.index', compact('payments', 'bankAccounts', 'status', 'bankAccountId', 'selectedAccount', 'search'));
    }

    /**
     * Approve Payment (ยืนยันยอดเงินเข้าบัญชี)
     */
    public function approvePayment(Request $request, int $id): RedirectResponse
    {
        $payment = Payment::with('booking')->findOrFail($id);

        $payment->update([
            'status' => 'verified',
            'verified_by_user_id' => Auth::id(),
            'verified_at' => Carbon::now(),
            'admin_notes' => $request->input('admin_notes', 'ตรวจสอบยอดเงินเข้าบัญชีถูกต้องแล้ว'),
        ]);

        // Update corresponding booking status to confirmed
        if ($payment->booking) {
            $payment->booking->update([
                'status' => 'confirmed',
            ]);
        }

        return back()->with('success', "ยืนยันยอดเงินรายการ {$payment->payment_code} สำเร็จ! การจองได้รับการยืนยันแล้ว");
    }

    /**
     * Reject Payment (ปฏิเสธสลิป / ยอดเงินไม่ถูกต้อง)
     */
    public function rejectPayment(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $payment = Payment::with('booking')->findOrFail($id);

        $payment->update([
            'status' => 'rejected',
            'verified_by_user_id' => Auth::id(),
            'verified_at' => Carbon::now(),
            'rejection_reason' => $request->rejection_reason,
            'admin_notes' => $request->input('admin_notes'),
        ]);

        // Revert booking status back to pending_payment so customer can re-upload or cancel
        if ($payment->booking) {
            $payment->booking->update([
                'status' => 'pending_payment',
            ]);
        }

        return back()->with('warning', "ปฏิเสธสลิปรายการ {$payment->payment_code} แล้ว พร้อมส่งเหตุผลให้ผู้จองทราบ");
    }

    /**
     * Dedicated Bookings Management (ตารางการจองสนาม แยกจากตารางชำระเงิน)
     */
    public function bookings(Request $request): View
    {
        $date = $request->query('date');
        $courtId = $request->query('court_id');
        $status = $request->query('status');
        $search = $request->query('q');

        $courts = Court::orderBy('court_number')->get();

        $query = Booking::with(['court', 'payment.bankAccount'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc');

        if ($date) {
            $query->where('booking_date', $date);
        }

        if ($courtId) {
            $query->where('court_id', $courtId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%");
            });
        }

        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings', 'courts', 'date', 'courtId', 'status', 'search'));
    }

    /**
     * Update booking status manually by admin.
     */
    public function updateBookingStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending_payment,payment_submitted,confirmed,cancelled,completed',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return back()->with('success', "อัปเดตสถานะการจอง {$booking->booking_code} เรียบร้อยแล้ว");
    }

    /**
     * Manage Receiving Bank Accounts (จัดการเลขบัญชีรับเงินของสนาม)
     */
    public function bankAccounts(): View
    {
        $bankAccounts = BankAccount::withCount('payments')
            ->orderBy('display_order')
            ->get();

        return view('admin.bank-accounts.index', compact('bankAccounts'));
    }

    /**
     * Store new bank account.
     */
    public function storeBankAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'bank_code' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:150',
            'branch' => 'nullable|string|max:100',
            'promptpay_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        BankAccount::create($request->all());

        return back()->with('success', 'เพิ่มบัญชีธนาคารรับเงินเรียบร้อยแล้ว');
    }

    /**
     * Update bank account.
     */
    public function updateBankAccount(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'bank_code' => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:150',
            'branch' => 'nullable|string|max:100',
            'promptpay_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $account = BankAccount::findOrFail($id);
        $account->update($request->all());

        return back()->with('success', 'บันทึกการแก้ไขบัญชีธนาคารเรียบร้อยแล้ว');
    }

    /**
     * Toggle Bank Account active status.
     */
    public function toggleBankAccount(int $id): RedirectResponse
    {
        $account = BankAccount::findOrFail($id);
        $account->update(['is_active' => !$account->is_active]);

        $state = $account->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
        return back()->with('success', "{$state} บัญชี {$account->bank_name} ({$account->account_number}) แล้ว");
    }

    /**
     * Manage Courts.
     */
    public function courts(): View
    {
        $courts = Court::withCount(['bookings as active_bookings_count' => function ($q) {
            $q->whereIn('status', ['confirmed', 'payment_submitted']);
        }])->orderBy('court_number')->get();

        return view('admin.courts.index', compact('courts'));
    }

    /**
     * Toggle Court active/maintenance status.
     */
    public function toggleCourt(int $id): RedirectResponse
    {
        $court = Court::findOrFail($id);
        $court->update([
            'status' => $court->status === 'available' ? 'maintenance' : 'available',
        ]);

        return back()->with('success', "อัปเดตสถานะคอร์ท {$court->court_number} เรียบร้อยแล้ว");
    }
}
