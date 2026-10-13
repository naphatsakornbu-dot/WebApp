@extends('layouts.admin')

@section('page-title', 'จัดการข้อมูลสนามแบดมินตัน')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1">
                @if($status === 'trashed')
                    <i class="bi bi-trash text-danger me-1"></i> ถังขยะสนามแบดมินตัน ({{ $courts->count() }})
                @else
                    รายการสนามทั้งหมด ({{ $courts->count() }})
                @endif
            </h5>
            <div class="text-muted small">เพิ่ม แก้ไข ปรับปรุงราคา และสถานะความพร้อมของสนาม</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.courts.index', ['status' => 'active']) }}" class="btn btn-sm {{ $status !== 'trashed' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                <i class="bi bi-grid me-1"></i> ใช้งานอยู่ ({{ $activeCount }})
            </a>
            <a href="{{ route('admin.courts.index', ['status' => 'trashed']) }}" class="btn btn-sm {{ $status === 'trashed' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3">
                <i class="bi bi-trash me-1"></i> ถังขยะ ({{ $trashedCount }})
            </a>
            @if($status !== 'trashed')
                <a href="{{ route('admin.courts.create') }}" class="btn btn-sm btn-success rounded-pill px-3">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มสนามใหม่
                </a>
            @endif
        </div>
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
                    @if($status === 'trashed')
                        <th>วันที่ลบ</th>
                    @endif
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courts as $court)
                    <tr class="{{ $status === 'trashed' ? 'table-light text-muted' : '' }}">
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
                        @if($status === 'trashed')
                            <td class="text-muted small">
                                {{ $court->deleted_at ? $court->deleted_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        @endif
                        <td class="text-center">
                            @if($status === 'trashed')
                                <div class="d-flex justify-content-center gap-1">
                                    <form action="{{ route('admin.courts.restore', $court->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="กู้คืนข้อมูล">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> กู้คืน
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.courts.force-delete', $court->id) }}" method="POST" onsubmit="return confirm('คำเตือน: คุณต้องการลบสนามนี้ถาวรใช่หรือไม่? การลบนี้ไม่สามารถย้อนกลับได้!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบถาวร">
                                            <i class="bi bi-x-circle me-1"></i> ลบถาวร
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.courts.edit', $court) }}" class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.courts.destroy', $court) }}" method="POST" onsubmit="return confirm('คุณต้องการย้ายสนามนี้ไปถังขยะใช่หรือไม่? (สามารถกู้คืนได้ภายหลัง)')">
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
                            {{ $status === 'trashed' ? 'ไม่มีรายการในถังขยะ' : 'ยังไม่มีข้อมูลสนามในระบบ' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
