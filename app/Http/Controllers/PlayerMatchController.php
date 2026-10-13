<?php

namespace App\Http\Controllers;

use App\Models\PlayerMatch;
use App\Models\MatchMember;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlayerMatchController extends Controller
{
    public function index(Request $request)
    {
        $skill = $request->get('skill');
        $query = PlayerMatch::with(['booking.court', 'booking.user', 'members.user'])
            ->whereHas('booking', function ($q) {
                $q->where('booking_date', '>=', now()->toDateString())
                  ->whereIn('status', ['confirmed', 'completed']);
            })
            ->latest();

        if ($skill && in_array($skill, ['beginner', 'intermediate', 'advanced'])) {
            $query->where(function ($q) use ($skill) {
                $q->where('skill_level', $skill)->orWhere('skill_level', 'any');
            });
        }

        $matches = $query->paginate(9)->withQueryString();

        return view('matches.index', compact('matches', 'skill'));
    }

    public function create(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์เปิดห้องสำหรับรายการจองนี้');
        }

        if ($booking->playerMatch) {
            return redirect()->route('bookings.show', $booking)->with('info', 'รายการจองนี้เปิดหาเพื่อนร่วมก๊วนอยู่แล้ว');
        }

        return view('matches.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์เปิดห้องสำหรับรายการจองนี้');
        }

        $validated = $request->validate([
            'slots_needed' => ['required', 'integer', 'min:1', 'max:8'],
            'skill_level' => ['required', 'in:beginner,intermediate,advanced,any'],
            'details' => ['nullable', 'string', 'max:500'],
        ], [
            'slots_needed.required' => 'กรุณาระบุจำนวนคนที่ต้องการ',
            'slots_needed.min' => 'ต้องการอย่างน้อย 1 คน',
            'skill_level.required' => 'กรุณาเลือกระดับฝีมือ',
        ]);

        PlayerMatch::create([
            'booking_id' => $booking->id,
            'slots_needed' => $validated['slots_needed'],
            'slots_filled' => 0,
            'skill_level' => $validated['skill_level'],
            'details' => $validated['details'],
            'status' => 'open',
        ]);

        return redirect()->route('bookings.show', $booking)->with('success', 'เปิดรับสมัครเพื่อนร่วมก๊วนสำเร็จ');
    }

    public function join(Request $request, PlayerMatch $match)
    {
        $user = Auth::user();

        // Prevent host from joining their own match
        if ($match->booking->user_id === $user->id) {
            return back()->with('error', 'คุณเป็นเจ้าของรอบนี้อยู่แล้ว');
        }

        // Check if already applied
        $existing = MatchMember::where('player_match_id', $match->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'คุณได้ส่งคำขอเข้าร่วมรอบนี้ไปแล้ว (สถานะ: ' . $existing->status . ')');
        }

        if ($match->status !== 'open' || $match->slots_filled >= $match->slots_needed) {
            return back()->with('error', 'รอบนี้มีผู้เข้าร่วมเต็มแล้ว');
        }

        $request->validate([
            'message' => ['nullable', 'string', 'max:255'],
        ]);

        MatchMember::create([
            'player_match_id' => $match->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'message' => $request->message,
        ]);

        return back()->with('success', 'ส่งคำขอเข้าร่วมก๊วนเรียบร้อยแล้ว รอเจ้าของรอบตอบรับ');
    }

    public function respond(Request $request, MatchMember $member)
    {
        $match = $member->playerMatch;

        // Only host can respond
        if ($match->booking->user_id !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์ตอบรับสมาชิก');
        }

        $action = $request->validate([
            'action' => ['required', 'in:accept,reject'],
        ])['action'];

        DB::transaction(function () use ($member, $match, $action) {
            if ($action === 'accept') {
                if ($match->slots_filled < $match->slots_needed) {
                    $member->status = 'accepted';
                    $member->save();
                    $match->increment('slots_filled');

                    if ($match->slots_filled >= $match->slots_needed) {
                        $match->status = 'full';
                        $match->save();
                    }
                }
            } else {
                if ($member->status === 'accepted') {
                    $match->decrement('slots_filled');
                    if ($match->status === 'full') {
                        $match->status = 'open';
                        $match->save();
                    }
                }
                $member->status = 'rejected';
                $member->save();
            }
        });

        return back()->with('success', 'อัปเดตสถานะคำขอสมาชิกเรียบร้อยแล้ว');
    }
}
