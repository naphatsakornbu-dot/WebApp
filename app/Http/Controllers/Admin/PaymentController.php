<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = Payment::with(['booking.user', 'booking.court'])->latest();

        if ($status && in_array($status, ['unpaid', 'pending', 'paid', 'rejected'])) {
            $query->where('status', $status);
        }

        $payments = $query->paginate(15)->withQueryString();

        $stats = [
            'pending_count' => Payment::where('status', 'pending')->count(),
            'pending_amount' => Payment::where('status', 'pending')->sum('amount'),
            'paid_today_count' => Payment::where('status', 'paid')->whereDate('updated_at', today())->count(),
            'paid_today_amount' => Payment::where('status', 'paid')->whereDate('updated_at', today())->sum('amount'),
            'total_paid_amount' => Payment::where('status', 'paid')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'status', 'stats'));
    }

    public function approve(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            $payment->status = 'paid';
            $payment->admin_note = 'ตรวจสอบยอดเงินและสลิปเรียบร้อยแล้ว';
            $payment->save();

            // Auto-confirm the booking
            if ($payment->booking) {
                $payment->booking->status = 'confirmed';
                $payment->booking->save();
            }
        });

        return back()->with('success', 'อนุมัติการชำระเงินและยืนยันการจองสนามเรียบร้อยแล้ว');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'admin_note' => ['required', 'string', 'max:255'],
        ], [
            'admin_note.required' => 'กรุณาระบุเหตุผลในการปฏิเสธสลิป',
        ]);

        $payment->status = 'rejected';
        $payment->admin_note = $request->admin_note;
        $payment->save();

        return back()->with('success', 'ปฏิเสธสลิปการชำระเงินเรียบร้อยแล้ว');
    }
}
