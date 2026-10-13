@extends('layouts.admin')

@section('page-title', 'จัดการอุปกรณ์กีฬาและสต็อก')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-0">รายการอุปกรณ์สำหรับเช่า ({{ $equipments->count() }})</h5>
            <div class="text-muted small">จัดการสต็อก ราคาค่าเช่า และสถานะการให้บริการอุปกรณ์</div>
        </div>
        <a href="{{ route('admin.equipments.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> เพิ่มอุปกรณ์ใหม่
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>ชื่ออุปกรณ์</th>
                    <th>หมวดหมู่</th>
                    <th>ราคาเช่า (บาท)</th>
                    <th>สต็อกทั้งหมด</th>
                    <th>พร้อมให้เช่า</th>
                    <th>สถานะ</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipments as $eq)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold text-dark">{{ $eq->name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $eq->category }}</span></td>
                        <td class="fw-bold text-success">฿{{ number_format($eq->rental_price, 2) }}</td>
                        <td>{{ $eq->total_stock }} ชิ้น</td>
                        <td>
                            <span class="badge bg-{{ $eq->available_stock > 0 ? 'success' : 'danger' }} px-2 py-1">
                                {{ $eq->available_stock }} ชิ้น
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $eq->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($eq->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.equipments.edit', $eq) }}" class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.equipments.destroy', $eq) }}" method="POST" onsubmit="return confirm('คุณต้องการลบอุปกรณ์นี้ใช่หรือไม่?')">
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
                        <td colspan="8" class="text-center py-4 text-muted">ยังไม่มีรายการอุปกรณ์ในระบบ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
