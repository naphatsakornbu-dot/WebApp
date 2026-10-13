@extends('layouts.admin')

@section('page-title', 'ตารางปิดปรับปรุงสนาม (Maintenance Scheduler)')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-0">รายการปิดปรับปรุงสนาม</h5>
            <div class="text-muted small">กำหนดช่วงเวลาที่ไม่เปิดให้ผู้ใช้จอง เช่น ซ่อมพื้น เปลี่ยนหลอดไฟ ทำความสะอาด</div>
        </div>
        <a href="{{ route('admin.maintenances.create') }}" class="btn btn-warning rounded-pill px-4 fw-bold">
            <i class="bi bi-tools me-1"></i> เพิ่มช่วงเวลาปิดปรับปรุง
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>สนาม</th>
                    <th>หัวข้อการปรับปรุง</th>
                    <th>วันที่เริ่มต้น</th>
                    <th>วันที่สิ้นสุด</th>
                    <th>ช่วงเวลาที่ปิด</th>
                    <th>รายละเอียด</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($maintenances as $m)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold text-dark">{{ $m->court->name }}</td>
                        <td><span class="badge bg-warning text-dark">{{ $m->title }}</span></td>
                        <td>{{ date('d/m/Y', strtotime($m->start_date)) }}</td>
                        <td>{{ date('d/m/Y', strtotime($m->end_date)) }}</td>
                        <td class="fw-semibold text-danger">{{ substr($m->start_time, 0, 5) }} - {{ substr($m->end_time, 0, 5) }} น.</td>
                        <td class="text-muted">{{ $m->description ?? '-' }}</td>
                        <td class="text-center">
                            <form action="{{ route('admin.maintenances.destroy', $m) }}" method="POST" onsubmit="return confirm('คุณต้องการลบรายการปิดปรับปรุงนี้ใช่หรือไม่? สนามจะกลับมาเปิดให้จองได้ปกติ')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    <i class="bi bi-trash me-1"></i> ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">ยังไม่มีรายการปิดปรับปรุงสนาม</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
