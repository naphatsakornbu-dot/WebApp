@extends('layouts.admin')

@section('page-title', 'รายงานและสถิติการวิเคราะห์ (Multi-Table Reports)')

@section('content')
<!-- Filter Date & Court -->
<div class="card shadow-sm p-4 mb-4">
    <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label for="start_date" class="form-label small fw-semibold">วันที่เริ่มต้น</label>
            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label small fw-semibold">วันที่สิ้นสุด</label>
            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
        </div>
        <div class="col-md-4">
            <label for="court_id" class="form-label small fw-semibold">เลือกสนาม</label>
            <select name="court_id" id="court_id" class="form-select form-select-sm">
                <option value="">-- รวมทุกสนาม --</option>
                @foreach($courts as $c)
                    <option value="{{ $c->id }}" {{ $courtId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-filter me-1"></i> ประมวลผล</button>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
        </div>
    </form>
</div>

<!-- KPI Summary Highlights -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-success">
            <div class="text-muted small">รายได้รวมที่ชำระแล้ว</div>
            <h3 class="fw-bold text-success mb-0">฿{{ number_format($summary['total_revenue'], 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-primary">
            <div class="text-muted small">ยอดการจองทั้งหมด</div>
            <h3 class="fw-bold text-dark mb-0">{{ $summary['total_bookings'] }} รายการ</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-info">
            <div class="text-muted small">ชั่วโมงการเล่นรวม</div>
            <h3 class="fw-bold text-dark mb-0">{{ number_format($summary['total_hours'], 1) }} ชม.</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-warning">
            <div class="text-muted small">รายได้จากค่าเช่าอุปกรณ์</div>
            <h3 class="fw-bold text-warning-emphasis mb-0">฿{{ number_format($summary['total_equipment_income'], 2) }}</h3>
        </div>
    </div>
</div>

<!-- Report 1: Booking Audit Report (4-Table Join: users + bookings + courts + payments) -->
<div class="card shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-table text-primary me-2"></i> 1. รายงานการจองสนามอย่างละเอียด
            </h5>
            <div class="text-muted small">Join ตาราง: <code>users</code> + <code>bookings</code> + <code>courts</code> + <code>payments</code></div>
        </div>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> พิมพ์รายงาน
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>รหัสจอง</th>
                    <th>ชื่อผู้จอง</th>
                    <th>เบอร์โทร</th>
                    <th>สนาม</th>
                    <th>วันที่</th>
                    <th>เวลา</th>
                    <th>ชม.</th>
                    <th>ค่าสนาม</th>
                    <th>ค่าอุปกรณ์</th>
                    <th>ยอดรวม</th>
                    <th>สถานะจอง</th>
                    <th>สถานะเงิน</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookingReports as $row)
                    <tr>
                        <td class="fw-bold">#BK-{{ str_pad($row->booking_id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $row->user_name }}</td>
                        <td>{{ $row->user_phone ?? '-' }}</td>
                        <td>{{ $row->court_name }}</td>
                        <td>{{ date('d/m/Y', strtotime($row->booking_date)) }}</td>
                        <td>{{ substr($row->start_time, 0, 5) }} - {{ substr($row->end_time, 0, 5) }}</td>
                        <td class="text-center">{{ $row->total_hours }}</td>
                        <td class="text-end">฿{{ number_format($row->court_price, 2) }}</td>
                        <td class="text-end">฿{{ number_format($row->equipment_price, 2) }}</td>
                        <td class="text-end fw-bold text-success">฿{{ number_format($row->grand_total, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $row->booking_status }} px-2 py-1 rounded-pill">
                                {{ ucfirst($row->booking_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $row->payment_status === 'paid' ? 'success' : ($row->payment_status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($row->payment_status ?? 'unpaid') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center py-4 text-muted">ไม่พบข้อมูลตามช่วงเวลาที่กำหนด</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row g-4">
    <!-- Report 2: Revenue by Court (3-Table Join: courts + bookings + payments) -->
    <div class="col-lg-6">
        <div class="card shadow-sm p-4 h-100">
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-graph-up-arrow text-success me-2"></i> 2. รายงานสรุปรายได้แยกตามสนาม
            </h5>
            <div class="text-muted small mb-3">Join ตาราง: <code>courts</code> + <code>bookings</code> + <code>payments</code></div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>ชื่อสนาม</th>
                            <th class="text-center">จำนวนการจอง</th>
                            <th class="text-center">ชม. รวม</th>
                            <th class="text-end">รายได้รวม (บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByCourt as $rc)
                            <tr>
                                <td class="fw-bold">{{ $rc->court_name }}</td>
                                <td class="text-center">{{ $rc->total_bookings }} ครั้ง</td>
                                <td class="text-center">{{ number_format($rc->total_hours, 1) }} ชม.</td>
                                <td class="text-end fw-bold text-success">฿{{ number_format($rc->total_revenue, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Report 3: Equipment Utilization (4-Table Join: equipments + equipment_rentals + bookings + courts) -->
    <div class="col-lg-6">
        <div class="card shadow-sm p-4 h-100">
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-box-seam text-warning me-2"></i> 3. รายงานการเช่าอุปกรณ์กีฬา
            </h5>
            <div class="text-muted small mb-3">Join ตาราง: <code>equipments</code> + <code>equipment_rentals</code> + <code>bookings</code> + <code>courts</code></div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>อุปกรณ์</th>
                            <th>หมวดหมู่</th>
                            <th class="text-center">ยอดเช่าสะสม</th>
                            <th class="text-end">รายได้ค่าเช่า</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipmentReports as $eqr)
                            <tr>
                                <td class="fw-bold">{{ $eqr->equipment_name }}</td>
                                <td>{{ $eqr->category }}</td>
                                <td class="text-center fw-bold">{{ $eqr->total_rented_qty }} ชิ้น</td>
                                <td class="text-end fw-bold text-success">฿{{ number_format($eqr->total_rental_income, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
