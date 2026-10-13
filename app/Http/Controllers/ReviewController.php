<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์รีวิวรายการจองนี้');
        }

        if ($booking->status !== 'completed') {
            return back()->with('error', 'สามารถเขียนรีวิวได้เฉพาะรอบการเล่นที่เสร็จสิ้น (Completed) แล้วเท่านั้น');
        }

        if ($booking->review) {
            return back()->with('error', 'คุณได้เขียนรีวิวสำหรับรอบการจองนี้ไปแล้ว');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ], [
            'rating.required' => 'กรุณาให้คะแนนดาว 1-5 ดาว',
            'rating.min' => 'คะแนนอย่างน้อย 1 ดาว',
            'rating.max' => 'คะแนนสูงสุด 5 ดาว',
        ]);

        Review::create([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'court_id' => $booking->court_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'ขอบคุณสำหรับรีวิวและความคิดเห็นของคุณ!');
    }
}
