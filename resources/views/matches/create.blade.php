@extends('layouts.app')

@section('title', 'เปิดรับสมัครเพื่อนร่วมก๊วน')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle p-3 mb-2">
                        <i class="bi bi-people-fill fs-1"></i>
                    </div>
                    <h3 class="fw-bold">เปิดรับสมัครเพื่อนร่วมก๊วน</h3>
                    <p class="text-muted small">โพสต์ลงกระดาน Community เพื่อหาผู้เล่นเข้าร่วมรอบของคุณ</p>
                </div>

                <div class="p-3 bg-light rounded-3 small mb-4">
                    <div><strong>สนาม:</strong> {{ $booking->court->name }}</div>
                    <div><strong>วันที่:</strong> {{ date('d M Y', strtotime($booking->booking_date)) }}</div>
                    <div><strong>เวลา:</strong> {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} น.</div>
                </div>

                <form action="{{ route('matches.store', $booking) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="slots_needed" class="form-label fw-semibold">จำนวนคนที่ต้องการเพิ่ม (คน) <span class="text-danger">*</span></label>
                        <input type="number" name="slots_needed" id="slots_needed" class="form-control" value="{{ old('slots_needed', 2) }}" min="1" max="8" required>
                    </div>

                    <div class="mb-3">
                        <label for="skill_level" class="form-label fw-semibold">ระดับฝีมือที่ต้องการ <span class="text-danger">*</span></label>
                        <select name="skill_level" id="skill_level" class="form-select" required>
                            <option value="any" {{ old('skill_level') == 'any' ? 'selected' : '' }}>ทุกระดับฝีมือ (Any Level)</option>
                            <option value="beginner" {{ old('skill_level') == 'beginner' ? 'selected' : '' }}>มือใหม่ / ตีออกกำลังกาย (Beginner)</option>
                            <option value="intermediate" {{ old('skill_level') == 'intermediate' ? 'selected' : '' }}>ระดับกลาง / ตีเกมได้ (Intermediate)</option>
                            <option value="advanced" {{ old('skill_level') == 'advanced' ? 'selected' : '' }}>ระดับแข่งขัน / เข้มข้น (Advanced)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="details" class="form-label fw-semibold">รายละเอียด / เงื่อนไขก๊วน</label>
                        <textarea name="details" id="details" class="form-control" rows="3" placeholder="เช่น แชร์ค่าสนามคนละ 50 บาท, เตรียมลูกแบดมาเอง หรือเน้นเล่นสนุกๆ">{{ old('details') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="bi bi-megaphone me-1"></i> โพสต์เปิดรับสมัครทันที
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
