<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $booking->load(['court', 'payment', 'equipmentRentals.equipment']);

        return view('payments.show', compact('booking'));
    }

    public function uploadSlip(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'คุณไม่มีสิทธิ์ดำเนินการ');
        }

        $request->validate([
            'slip_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'slip_image.required' => 'กรุณาเลือกไฟล์รูปภาพสลิปโอนเงิน',
            'slip_image.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพเท่านั้น',
            'slip_image.mimes' => 'รองรับไฟล์ภาพนามสกุล jpeg, png, jpg, webp เท่านั้น',
            'slip_image.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 5 MB',
        ]);

        $path = $request->file('slip_image')->store('slips', 'public');

        $payment = $booking->payment;
        if (!$payment) {
            $payment = new Payment();
            $payment->booking_id = $booking->id;
            $payment->amount = $booking->grand_total;
        }

        $payment->slip_image = $path;
        $payment->payment_date = now();
        $payment->status = 'pending';
        $payment->admin_note = null; // Clear previous reject note if re-uploading
        $payment->save();

        return redirect()->route('bookings.show', $booking)->with('success', 'อัปโหลดสลิปโอนเงินเรียบร้อยแล้ว รอเจ้าหน้าที่ตรวจสอบและยืนยันการจอง');
    }
}
