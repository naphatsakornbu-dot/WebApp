@extends('layouts.admin')

@section('page-title', 'ภาพรวมระบบและสถิติ (Admin Dashboard)')

@section('content')
<!-- KPI Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">รายได้เดือนนี้</div>
                    <h3 class="fw-bold text-dark mb-0">฿{{ number_format($stats['revenue_this_month'], 2) }}</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">การจองวันนี้</div>
                    <h3 class="fw-bold text-dark mb-0">{{ $stats['bookings_today'] }} ครั้ง</h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle">
                    <i class="bi bi-calendar2-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">สลิปรอตรวจสอบ</div>
                    <h3 class="fw-bold text-warning-emphasis mb-0">{{ $stats['pending_payments'] }} รายการ</h3>
                </div>
                <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-circle">
                    <i class="bi bi-receipt-cutoff fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">สมาชิกทั้งหมด</div>
                    <h3 class="fw-bold text-dark mb-0">{{ $stats['total_users'] }} คน</h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle">
                    <i class="bi bi-people fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Pending Slip Approvals List -->
    <div class="col-lg-6">
        <div class="card shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-hourglass-split text-warning me-2"></i>สลิปรอการตรวจสอบด่วน</h5>
                <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-warning">ดูทั้งหมด</a>
            </div>

            @forelse($pendingSlips as $payment)
                <div class="p-3 bg-light rounded-3 mb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark">#BK-{{ str_pad($payment->booking->id, 5, '0', STR_PAD_LEFT) }} — {{ $payment->booking->user->name }}</div>
                        <div class="small text-muted">{{ $payment->booking->court->name }} | ยอด: ฿{{ number_format($payment->amount, 2) }}</div>
                    </div>
                    <div class="d-flex gap-1">
                        <form action="{{ route('admin.payments.approve', $payment) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success px-3 rounded-pill" title="อนุมัติ">
                                <i class="bi bi-check-circle me-1"></i> อนุมัติ
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4 small">ไม่มีสลิปค้างรอตรวจสอบในขณะนี้ 👍</div>
            @endforelse
        </div>
    </div>

    <!-- Top Popular Courts -->
    <div class="col-lg-6">
        <div class="card shadow-sm p-4 h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-trophy text-warning me-2"></i>สนามยอดนิยมที่มีการจองสูงสุด</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>สนาม</th>
                            <th>ประเภทพื้น</th>
                            <th>ราคา/ชม.</th>
                            <th class="text-center">ยอดจองสำเร็จ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCourts as $court)
                            <tr>
                                <td class="fw-bold">{{ $court->name }}</td>
                                <td>{{ $court->floor_type }}</td>
                                <td>฿{{ number_format($court->price_per_hour, 0) }}</td>
                                <td class="text-center fw-bold text-success">{{ $court->bookings_count }} ครั้ง</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings Table -->
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>รายการจองล่าสุดในระบบ</h5>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">จัดการการจองทั้งหมด</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>รหัส</th>
                    <th>ผู้จอง</th>
                    <th>สนาม</th>
                    <th>วันและเวลาที่จอง</th>
                    <th>ยอดรวม</th>
                    <th>สถานะการจอง</th>
                    <th>Payment</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $b)
                    <tr>
                        <td class="fw-bold">#BK-{{ str_pad($b->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="fw-semibold">{{ $b->user->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $b->user->phone }}</div>
                        </td>
                        <td>{{ $b->court->name }}</td>
                        <td>
                            <div>{{ date('d/m/Y', strtotime($b->booking_date)) }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }} น.</div>
                        </td>
                        <td class="fw-bold text-success">฿{{ number_format($b->grand_total, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $b->status }} px-2 py-1 rounded-pill">
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $b->payment?->status === 'paid' ? 'success' : ($b->payment?->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($b->payment?->status ?? 'unpaid') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.bookings.show', $b) }}" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
