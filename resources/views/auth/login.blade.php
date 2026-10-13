@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle p-3 mb-3">
                        <i class="bi bi-person-circle fs-1"></i>
                    </div>
                    <h3 class="fw-bold">เข้าสู่ระบบ</h3>
                    <p class="text-muted small">กรอกอีเมลและรหัสผ่านเพื่อเข้าใช้งานระบบจองสนาม</p>
                </div>

                <form id="loginForm" action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">อีเมลหรือชื่อผู้ใช้ (Email / Username)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="กรอกอีเมลของคุณ">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">รหัสผ่าน (Password)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label small text-muted" for="remember">จดจำฉันในระบบ</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center small">
                    ยังไม่มีบัญชีสมาชิก? <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">สมัครสมาชิกใหม่</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
