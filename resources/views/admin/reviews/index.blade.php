@extends('layouts.admin')

@section('page-title', 'จัดการรีวิวและความคิดเห็น')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-0">รายการรีวิวจากผู้เล่นทั้งหมด</h5>
            <div class="text-muted small">ตรวจสอบความคิดเห็น คะแนนดาว และลบรีวิวที่ไม่เหมาะสม</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>ผู้รีวิว</th>
                    <th>สนาม</th>
                    <th>คะแนนดาว</th>
                    <th>ความคิดเห็น</th>
                    <th>วันที่รีวิว</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $rev)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $rev->user->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $rev->user->email }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $rev->court->name }}</span></td>
                        <td>
                            <div class="star-rating text-nowrap">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-{{ $i <= $rev->rating ? 'fill' : '' }}"></i>
                                @endfor
                                <span class="fw-bold text-dark ms-1">({{ $rev->rating }})</span>
                            </div>
                        </td>
                        <td>"{{ $rev->comment }}"</td>
                        <td class="text-muted">{{ $rev->created_at->format('d/m/Y H:i') }} น.</td>
                        <td class="text-center">
                            <form action="{{ route('admin.reviews.destroy', $rev) }}" method="POST" onsubmit="return confirm('คุณต้องการลบรีวิวนี้ใช่หรือไม่?')">
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
                        <td colspan="7" class="text-center py-4 text-muted">ยังไม่มีข้อมูลรีวิวในระบบ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reviews->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
