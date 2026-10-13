@extends('layouts.admin')

@section('page-title', 'จัดการรายการจองสนามทั้งหมด')

@section('content')
<!-- Filter Card -->
<div class="card shadow-sm p-4 mb-4">
    <form action="{{ route('admin.bookings.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label for="search" class="form-label small fw-semibold">ค้นหาชื่อ/อีเมล/เบอร์โทร</label>
            <input type="text" name="search" id="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="พิมพ์คำค้นหา...">
        </div>
        <div class="col-md-3">
            <label for="court_id" class="form-label small fw-semibold">สนาม</label>
            <select name="court_id" id="court_id" class="form-select form-select-sm">
                <option value="">-- ทุกสนาม --</option>
                @foreach($courts as $c)
                    <option value="{{ $c->id }}" {{ request('court_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="date" class="form-label small fw-semibold">วันที่จอง</label>
            <input type="date" name="date" id="date" class="form-control form-control-sm" value="{{ request('date') }}">
        </div>
        <div class="col-md-2">
            <label for="status" class="form-label small fw-semibold">สถานะ</label>
            <select name="status" id="status" class="form-select form-select-sm">
                <option value="">-- ทุกสถานะ --</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> กรอง</button>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="card shadow-sm p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>รหัส</th>
                    <th>ผู้จอง</th>
                    <th>สนาม</th>
                    <th>วันที่จอง</th>
                    <th>ช่วงเวลา</th>
                    <th>ยอดรวม</th>
                    <th>Payment</th>
                    <th>สถานะการจอง</th>
                    <th class="text-center">จัดการสถานะ</th>
                    <th class="text-center">ดู</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                    <tr>
                        <td class="fw-bold">#BK-{{ str_pad($b->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $b->user->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $b->user->email }} | {{ $b->user->phone }}</div>
                        </td>
                        <td>{{ $b->court->name }}</td>
                        <td>{{ date('d/m/Y', strtotime($b->booking_date)) }}</td>
                        <td>{{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }} น. ({{ $b->total_hours }} ชม.)</td>
                        <td class="fw-bold text-success">฿{{ number_format($b->grand_total, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $b->payment?->status === 'paid' ? 'success' : ($b->payment?->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($b->payment?->status ?? 'unpaid') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $b->status }} px-2 py-1 rounded-pill">
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.bookings.status', $b) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                    <option value="pending" {{ $b->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $b->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="completed" {{ $b->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $b->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.bookings.show', $b) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">ไม่พบข้อมูลการจองตามเงื่อนไขที่เลือก</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $bookings->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
