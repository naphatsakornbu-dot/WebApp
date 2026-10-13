@extends('layouts.admin')

@section('page-title', 'จัดการข้อมูลสมาชิก')

@section('content')
<div class="card shadow-sm p-4">
    <!-- Search & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">รายชื่อสมาชิกในระบบ</h5>
            <div class="text-muted small">ตรวจสอบข้อมูลสมาชิกและสลับสิทธิ์การใช้งาน (Admin / User)</div>
        </div>
        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร...">
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>สิทธิ์ในระบบ (Role)</th>
                    <th>จำนวนการจอง</th>
                    <th>วันที่สมัคร</th>
                    <th class="text-center">สลับสิทธิ์</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold text-dark">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->phone ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $u->role === 'admin' ? 'danger' : 'success' }} px-2 py-1">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td>{{ $u->bookings_count }} ครั้ง</td>
                        <td class="text-muted">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($u->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle-role', $u) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $u->role === 'admin' ? 'secondary' : 'danger' }} rounded-pill px-3"
                                            onclick="return confirm('คุณต้องการเปลี่ยนสิทธิ์ของ {{ $u->name }} เป็น {{ $u->role === 'admin' ? 'user' : 'admin' }} ใช่หรือไม่?')">
                                        <i class="bi bi-arrow-repeat me-1"></i> เปลี่ยนเป็น {{ $u->role === 'admin' ? 'User' : 'Admin' }}
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-light text-muted border">บัญชีปัจจุบันของคุณ</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">ไม่พบข้อมูลสมาชิกตามที่ค้นหา</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
