@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลส่วนตัว')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm p-4">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.5rem; font-weight: 700;">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
                        <div class="text-muted small">
                            อีเมล: {{ $user->email }} | สิทธิ์: <span class="badge bg-{{ $user->isAdmin() ? 'danger' : 'success' }}">{{ $user->role }}</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">ชื่อ-นามสกุล</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">อีเมล (ไม่สามารถเปลี่ยนได้)</label>
                        <input type="email" class="form-control bg-light" id="email" value="{{ $user->email }}" disabled>
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label fw-semibold">เบอร์โทรศัพท์</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock me-1"></i> เปลี่ยนรหัสผ่านใหม่ (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</h6>
                        <div class="mb-3">
                            <label for="password" class="form-label small fw-semibold">รหัสผ่านใหม่</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••">
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-0">
                            <label for="password_confirmation" class="form-label small fw-semibold">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
