<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบจองสนามแบดมินตัน') - ITBADDIWA</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/badminton.css') }}">
    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <span class="fw-bold">IT<span class="text-success">BADDIWA</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i> หน้าแรก
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('courts.*') ? 'active' : '' }}"
                            href="{{ route('courts.index') }}">
                            <i class="bi bi-grid-3x3 me-1"></i> รายการสนาม
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('matches.*') ? 'active' : '' }}"
                            href="{{ route('matches.index') }}">
                            <i class="bi bi-people me-1"></i> หาเพื่อนตีแบด
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('bookings.my') ? 'active' : '' }}"
                                href="{{ route('bookings.my') }}">
                                <i class="bi bi-calendar-check me-1"></i> การจองของฉัน
                            </a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    @guest
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-secondary px-3" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i> สมัครสมาชิก
                            </a>
                        </li>
                    @else
                        @if(Auth::user()->isAdmin())
                            <li class="nav-item me-2">
                                <a class="btn btn-dark btn-sm rounded-pill px-3" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> แผงควบคุม Admin
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                                data-bs-toggle="dropdown">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 34px; height: 34px; font-weight: 600;">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="fw-semibold">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                <li>
                                    <span class="dropdown-item-text text-muted small">
                                        สถานะ: <span
                                            class="badge bg-{{ Auth::user()->isAdmin() ? 'danger' : 'success' }}">{{ Auth::user()->role }}</span>
                                    </span>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person-gear me-2"></i> แก้ไขข้อมูลส่วนตัว
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('bookings.my') }}">
                                        <i class="bi bi-calendar-week me-2"></i> ประวัติการจอง
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> ออกจากระบบ
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm rounded-3"
                role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm rounded-3"
                role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-2"></i>กรุณาตรวจสอบข้อผิดพลาด:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5 col-md-6">
                    <h5 class="text-white fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-circle-fill text-success" style="font-size: 0.75rem;"></i>
                        IT<span class="text-success">BADDIWA</span>
                    </h5>
                    <p class="footer-desc mb-3">
                        ระบบจองสนามแบดมินตันมาตรฐานระดับแข่งขัน พื้นยาง BWF ระบบไฟ LED ถนอมสายตา ไร้เงาสะท้อน
                        พร้อมระบบเช่าอุปกรณ์และค้นหาก๊วนตีแบดครบวงจร
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span
                            class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                            <i class="bi bi-shield-check me-1"></i> มาตรฐานแข่งขัน BWF
                        </span>
                        <span
                            class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill">
                            <i class="bi bi-lightning-charge me-1"></i> จองออนไลน์ 24 ชม.
                        </span>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <h6 class="text-white fw-bold mb-3">เมนูหลัก</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="{{ route('home') }}" class="footer-link">
                                <i class="bi bi-chevron-right text-success small"></i> หน้าแรก
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('courts.index') }}" class="footer-link">
                                <i class="bi bi-chevron-right text-success small"></i> ตารางสนาม & อัตราค่าบริการ
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('matches.index') }}" class="footer-link">
                                <i class="bi bi-chevron-right text-success small"></i> ก๊วนตีแบด (Community)
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <h6 class="text-white fw-bold mb-3">เวลาและสถานที่ให้บริการ</h6>
                    <div class="footer-info-item">
                        <i class="bi bi-clock-fill text-warning"></i>
                        <span>เปิดบริการทุกวัน: <strong class="text-white">08:00 - 22:00 น.</strong></span>
                    </div>
                    <div class="footer-info-item">
                        <i class="bi bi-geo-alt-fill text-danger"></i>
                        <span>SC09 อาคารวิทยวิภาส คณะวิทยาศาสตร์ มหาวิทยาลัยขอนแก่น</span>
                    </div>
                    <div class="footer-info-item">
                        <i class="bi bi-telephone-fill text-success"></i>
                        <span>ติดต่อสอบถาม: <strong class="text-white">091-111-1111</strong></span>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 footer-copyright">
                <div>
                    &copy; {{ date('Y') }} <strong class="text-white">ITBADDIWA</strong>. สงวนลิขสิทธิ์ทุกประการ (Court
                    Booking System)
                </div>
                <div class="text-white-50 small">
                    ระบบจัดการและจองสนามแบดมินตันออนไลน์
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>