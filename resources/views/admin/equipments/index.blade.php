@extends('layouts.admin')

@section('page-title', 'จัดการอุปกรณ์กีฬาและสต็อก')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">
                @if($status === 'trashed')
                    <i class="bi bi-trash text-danger me-1"></i> ถังขยะอุปกรณ์ ({{ $equipments->count() }})
                @else
                    รายการอุปกรณ์สำหรับเช่า ({{ $equipments->count() }})
                @endif
            </h5>
            <div class="text-muted small">จัดการสต็อก ราคาค่าเช่า และสถานะการให้บริการอุปกรณ์</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.equipments.index', ['status' => 'active']) }}" class="btn btn-sm {{ $status !== 'trashed' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                <i class="bi bi-box-seam me-1"></i> ใช้งานอยู่ ({{ $activeCount }})
            </a>
            <a href="{{ route('admin.equipments.index', ['status' => 'trashed']) }}" class="btn btn-sm {{ $status === 'trashed' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3">
                <i class="bi bi-trash me-1"></i> ถังขยะ ({{ $trashedCount }})
            </a>
            @if($status !== 'trashed')
                <a href="{{ route('admin.equipments.create') }}" class="btn btn-sm btn-success rounded-pill px-3">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มอุปกรณ์ใหม่
                </a>
            @endif
        </div>
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
                    @if($status === 'trashed')
                        <th>วันที่ลบ</th>
                    @endif
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipments as $eq)
                    <tr class="{{ $status === 'trashed' ? 'table-light text-muted' : '' }}">
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
                        @if($status === 'trashed')
                            <td class="text-muted small">
                                {{ $eq->deleted_at ? $eq->deleted_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        @endif
                        <td class="text-center">
                            @if($status === 'trashed')
                                <div class="d-flex justify-content-center gap-1">
                                    <form action="{{ route('admin.equipments.restore', $eq->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="กู้คืนข้อมูล">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> กู้คืน
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.equipments.force-delete', $eq->id) }}" method="POST" onsubmit="return confirm('คำเตือน: คุณต้องการลบอุปกรณ์นี้ถาวรใช่หรือไม่? การลบนี้ไม่สามารถย้อนกลับได้!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบถาวร">
                                            <i class="bi bi-x-circle me-1"></i> ลบถาวร
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.equipments.edit', $eq) }}" class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.equipments.destroy', $eq) }}" method="POST" onsubmit="return confirm('คุณต้องการย้ายอุปกรณ์นี้ไปถังขยะใช่หรือไม่? (สามารถกู้คืนได้ภายหลัง)')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="ย้ายไปถังขยะ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $status === 'trashed' ? '9' : '8' }}" class="text-center py-4 text-muted">
                            {{ $status === 'trashed' ? 'ไม่มีรายการในถังขยะ' : 'ยังไม่มีรายการอุปกรณ์ในระบบ' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
