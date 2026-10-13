@extends('layouts.app')

@section('title', 'การจองของฉัน')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">การจองของฉัน</h2>
            <p class="text-muted small mb-0">ตรวจสอบสถานะการจอง อัปโหลดสลิป เปิดหาก๊วนเพื่อน หรือรีวิวสนาม</p>
        </div>
        <div>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-calendar-plus me-1"></i> จองสนามใหม่
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 bg-white p-2 rounded-4 shadow-sm">
        <li class="nav-item">
            <a class="nav-link rounded-pill {{ !$status ? 'active' : '' }}" href="{{ route('bookings.my') }}">
                ทั้งหมด (All)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('bookings.my', ['status' => 'pending']) }}">
                รอตรวจสอบ (Pending)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill {{ $status === 'confirmed' ? 'active' : '' }}" href="{{ route('bookings.my', ['status' => 'confirmed']) }}">
                ยืนยันแล้ว (Confirmed)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill {{ $status === 'completed' ? 'active' : '' }}" href="{{ route('bookings.my', ['status' => 'completed']) }}">
                เสร็จสิ้น (Completed)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill {{ $status === 'cancelled' ? 'active' : '' }}" href="{{ route('bookings.my', ['status' => 'cancelled']) }}">
                ยกเลิก (Cancelled)
            </a>
        </li>
    </ul>

    <!-- Bookings List -->
    <div class="row g-4">
        @forelse($bookings as $booking)
            <div class="col-12">
                <div class="card p-4 shadow-sm card-hover border-start border-4 border-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : ($booking->status === 'completed' ? 'info' : 'danger')) }}">
                    <div class="row align-items-center g-3">
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">รหัสการจอง: #BK-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <h5 class="fw-bold text-dark mb-1">{{ $booking->court->name }}</h5>
                            <span class="badge bg-light text-dark border small">{{ $booking->court->floor_type }}</span>
                        </div>
                        <div class="col-md-3">
                            <div class="small fw-semibold text-dark"><i class="bi bi-calendar-event me-1 text-success"></i> {{ date('d M Y', strtotime($booking->booking_date)) }}</div>
                            <div class="small text-muted"><i class="bi bi-clock me-1 text-primary"></i> {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} น. ({{ $booking->total_hours }} ชม.)</div>
                        </div>
                        <div class="col-md-2">
                            <div class="small text-muted">ยอดชำระ:</div>
                            <div class="fw-bold text-success fs-5">฿{{ number_format($booking->grand_total, 2) }}</div>
                            <span class="badge bg-{{ $booking->payment?->status === 'paid' ? 'success' : ($booking->payment?->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ $booking->payment?->status === 'paid' ? 'ชำระแล้ว' : ($booking->payment?->status === 'pending' ? 'รอตรวจสลิป' : 'ยังไม่ชำระ') }}
                            </span>
                        </div>
                        <div class="col-md-2 text-md-center">
                            <div class="small text-muted mb-1">สถานะการจอง:</div>
                            <span class="badge badge-{{ $booking->status }} px-3 py-2 rounded-pill fs-6">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div class="col-md-2 text-md-end">
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-primary btn-sm rounded-pill w-100 mb-2">
                                <i class="bi bi-eye me-1"></i> ดูรายละเอียด
                            </a>

                            @if($booking->payment && $booking->payment->status === 'unpaid')
                                <a href="{{ route('payments.show', $booking) }}" class="btn btn-warning btn-sm rounded-pill w-100">
                                    <i class="bi bi-upload me-1"></i> แนบสลิป
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">ไม่พบประวัติการจองในสนาม</h5>
                <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm mt-2 rounded-pill">จองสนามตอนนี้เลย</a>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $bookings->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
