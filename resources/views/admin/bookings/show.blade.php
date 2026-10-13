@extends('layouts.admin')

@section('page-title', 'รายละเอียดการจอง #' . $booking->id)

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h5 class="fw-bold text-dark mb-0">รายการจอง #BK-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</h5>
            <div class="text-muted small">สร้างเมื่อ: {{ $booking->created_at->format('d/m/Y H:i') }} น.</div>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">ย้อนกลับ</a>
    </div>

    <div class="row g-4">
        <!-- Customer & Court Info -->
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 mb-3">
                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-person me-1"></i> ข้อมูลผู้จอง</h6>
                <div><strong>ชื่อ:</strong> {{ $booking->user->name }}</div>
                <div><strong>อีเมล:</strong> {{ $booking->user->email }}</div>
                <div><strong>เบอร์โทรศัพท์:</strong> {{ $booking->user->phone }}</div>
            </div>

            <div class="p-3 bg-light rounded-3 mb-3">
                <h6 class="fw-bold text-success mb-2"><i class="bi bi-grid-3x3 me-1"></i> ข้อมูลสนามและเวลา</h6>
                <div><strong>สนาม:</strong> {{ $booking->court->name }} ({{ $booking->court->floor_type }})</div>
                <div><strong>วันที่:</strong> {{ date('d/m/Y', strtotime($booking->booking_date)) }}</div>
                <div><strong>เวลา:</strong> {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} น. ({{ $booking->total_hours }} ชม.)</div>
                @if($booking->notes)
                    <div class="mt-2 text-muted small"><strong>หมายเหตุ:</strong> {{ $booking->notes }}</div>
                @endif
            </div>

            <!-- Change Booking Status -->
            <div class="p-3 border rounded-3">
                <h6 class="fw-bold mb-2">เปลี่ยนสถานะการจอง</h6>
                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    @method('PUT')
                    <select name="status" class="form-select">
                        <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending (รอตรวจสอบ)</option>
                        <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed (ยืนยันแล้ว)</option>
                        <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed (ใช้งานเสร็จสิ้น)</option>
                        <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled (ยกเลิก)</option>
                    </select>
                    <button type="submit" class="btn btn-primary px-3">บันทึก</button>
                </form>
            </div>
        </div>

        <!-- Payment & Equipment Details -->
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-credit-card me-1"></i> การชำระเงิน</h6>
                    <span class="badge bg-{{ $booking->payment?->status === 'paid' ? 'success' : ($booking->payment?->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($booking->payment?->status ?? 'unpaid') }}
                    </span>
                </div>
                <div><strong>ยอดชำระ:</strong> ฿{{ number_format($booking->grand_total, 2) }}</div>
                @if($booking->payment && $booking->payment->slip_image)
                    <div class="mt-2 text-center p-2 bg-white rounded-3 border">
                        <img src="{{ asset('storage/' . $booking->payment->slip_image) }}" alt="Slip" class="img-fluid rounded-2" style="max-height: 180px;">
                    </div>
                @endif
            </div>

            <!-- Equipment Rentals Table -->
            <div class="p-3 bg-light rounded-3">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-box-seam me-1"></i> อุปกรณ์ที่เช่า</h6>
                @if($booking->equipmentRentals->isNotEmpty())
                    <ul class="list-group list-group-flush small">
                        @foreach($booking->equipmentRentals as $r)
                            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                <span>{{ $r->equipment?->name ?? 'อุปกรณ์' }} x {{ $r->quantity }}</span>
                                <span class="fw-semibold">฿{{ number_format($r->subtotal, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-muted small">ไม่มีการเช่าอุปกรณ์</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
