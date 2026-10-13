@extends('layouts.admin')

@section('page-title', 'จัดการข้อมูลสนามแบดมินตัน')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-0">รายการสนามทั้งหมด ({{ $courts->count() }})</h5>
            <div class="text-muted small">เพิ่ม แก้ไข ปรับปรุงราคา และสถานะความพร้อมของสนาม</div>
        </div>
        <a href="{{ route('admin.courts.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> เพิ่มสนามใหม่
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>ชื่อสนาม</th>
                    <th>ประเภทพื้นผิว</th>
                    <th>ราคา / ชม.</th>
                    <th>เวลาเปิด - ปิด</th>
                    <th>สถานะ</th>
                    <th>ยอดจองทั้งหมด</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courts as $court)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $court->image_url }}" alt="{{ $court->name }}" class="rounded-3 object-fit-cover shadow-sm border" style="width: 54px; height: 42px;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $court->name }}</div>
                                    <div class="text-muted small">{{ Str::limit($court->description, 45) }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $court->floor_type }}</span></td>
                        <td class="fw-bold text-success">฿{{ number_format($court->price_per_hour, 2) }}</td>
                        <td class="small">{{ substr($court->open_time, 0, 5) }} - {{ substr($court->close_time, 0, 5) }} น.</td>
                        <td>
                            <span class="badge bg-{{ $court->status === 'available' ? 'success' : ($court->status === 'maintenance' ? 'warning' : 'danger') }}">
                                {{ ucfirst($court->status) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $court->bookings_count }} ครั้ง</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.courts.edit', $court) }}" class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.courts.destroy', $court) }}" method="POST" onsubmit="return confirm('คุณต้องการลบสนามนี้ใช่หรือไม่?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">ยังไม่มีข้อมูลสนามในระบบ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
