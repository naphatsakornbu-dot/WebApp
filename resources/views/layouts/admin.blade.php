<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Badminton Arena Control</title>

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

<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Admin Sidebar -->
            <div class="col-lg-2 col-md-3 admin-sidebar d-flex flex-column p-3">
                <div class="d-flex align-items-center gap-2 px-2 py-3 mb-3 border-bottom border-secondary">

                    <div>
                        <div class="fw-bold text-white fs-6">ADMIN PANEL</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Badminton Management</div>
                    </div>
                </div>

                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> แผงควบคุม (Dashboard)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.courts.index') }}"
                            class="nav-link {{ request()->routeIs('admin.courts.*') ? 'active' : '' }}">
                            <i class="bi bi-grid-3x3"></i> จัดการสนาม
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.bookings.index') }}"
                            class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check"></i> รายการจองทั้งหมด
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.payments.index') }}"
                            class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <i class="bi bi-receipt"></i> ตรวจสอบการชำระเงิน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.equipments.index') }}"
                            class="nav-link {{ request()->routeIs('admin.equipments.*') ? 'active' : '' }}">
                            <i class="bi bi-box-seam"></i> จัดการอุปกรณ์
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.maintenances.index') }}"
                            class="nav-link {{ request()->routeIs('admin.maintenances.*') ? 'active' : '' }}">
                            <i class="bi bi-tools"></i> ปิดปรับปรุงสนาม
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reviews.index') }}"
                            class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                            <i class="bi bi-star-half"></i> รีวิวและคะแนน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}"
                            class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> ข้อมูลสมาชิก
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.index') }}"
                            class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-line"></i> รายงานและสถิติ
                        </a>
                    </li>
                </ul>

                <hr class="border-secondary my-3">

                <div class="px-2">
                    <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm w-100 mb-2">
                        <i class="bi bi-globe me-1"></i> ไปหน้าเว็บไซต์หลัก
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
                        </button>
                    </form>
                </div>
            </div>

            <!-- Admin Main Content -->
            <div class="col-lg-10 col-md-9 admin-content">
                <!-- Topbar -->
                <div
                    class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom bg-white p-3 rounded-4 shadow-sm">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">@yield('page-title', 'แดชบอร์ดผู้ดูแลระบบ')</h4>
                        <div class="text-muted small">ยินดีต้อนรับ, {{ Auth::user()->name }} (ผู้ดูแลระบบ)</div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                            <i class="bi bi-clock me-1"></i> {{ date('d M Y') }}
                        </span>
                        <div class="dropdown">
                            <button
                                class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 rounded-pill border"
                                type="button" data-bs-toggle="dropdown">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 28px; height: 28px; font-size: 0.85rem;">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="small fw-semibold">{{ Auth::user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                            class="bi bi-person me-2"></i>โปรไฟล์</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i
                                                class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
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

                <!-- Dynamic Page Content -->
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>