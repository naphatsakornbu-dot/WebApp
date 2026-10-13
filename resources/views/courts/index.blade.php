@extends('layouts.app')

@section('title', 'รายการสนามแบดมินตัน')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">รายการสนามแบดมินตัน</h2>
            <p class="text-muted small mb-0">เลือกสนามที่ต้องการเพื่อตรวจสอบตารางเวลาว่างและทำการจอง</p>
        </div>
        <div>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-calendar-plus me-1"></i> จองสนามด่วน
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card p-3 mb-4 shadow-sm">
        <form action="{{ route('courts.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="floor_type" class="form-label small fw-semibold">ประเภทพื้นสนาม</label>
                <select name="floor_type" id="floor_type" class="form-select">
                    <option value="">-- ทั้งหมดทุกประเภทพื้น --</option>
                    <option value="ยาง PB" {{ request('floor_type') == 'ยาง PB' ? 'selected' : '' }}>ยาง PB มาตรฐาน BWF</option>
                    <option value="ไวนิล" {{ request('floor_type') == 'ไวนิล' ? 'selected' : '' }}>ไวนิลซับแรงกระแทก Pro</option>
                    <option value="ไม้ปาร์เก้" {{ request('floor_type') == 'ไม้ปาร์เก้' ? 'selected' : '' }}>พื้นไม้ปาร์เก้ VIP</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label small fw-semibold">สถานะสนาม</label>
                <select name="status" id="status" class="form-select">
                    <option value="">-- ทุกสถานะ --</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>พร้อมให้บริการ (Available)</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>ปิดปรับปรุง (Maintenance)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> ค้นหา</button>
                <a href="{{ route('courts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>

    <!-- Courts Grid -->
    <div class="row g-4">
        @forelse($courts as $court)
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 card-hover">
                    <div class="position-relative overflow-hidden" style="border-top-left-radius: inherit; border-top-right-radius: inherit;">
                        <img src="{{ $court->image_url }}" alt="{{ $court->name }}" class="card-img-top object-fit-cover w-100" style="height: 190px;">
                        <span class="position-absolute top-0 end-0 m-3 badge bg-dark bg-opacity-75 rounded-pill shadow-sm">
                            ฿{{ number_format($court->price_per_hour, 0) }} / ชม.
                        </span>
                        @if($court->status !== 'available')
                            <span class="position-absolute top-0 start-0 m-3 badge bg-danger rounded-pill shadow-sm">
                                ปิดปรับปรุงชั่วคราว
                            </span>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2 gap-2" style="min-width: 0;">
                            <span class="badge bg-light text-secondary border text-truncate text-start flex-grow-1" style="min-width: 0; max-width: calc(100% - 60px);" title="{{ $court->floor_type }}">
                                <i class="bi bi-layers me-1 text-primary"></i>{{ $court->floor_type }}
                            </span>
                            <span class="small star-rating text-nowrap flex-shrink-0 d-inline-flex align-items-center gap-1" style="white-space: nowrap !important;">
                                <i class="bi bi-star-fill text-warning"></i>
                                <span class="fw-bold text-dark">{{ number_format($court->average_rating, 1) }}</span>
                            </span>
                        </div>
                        <h5 class="fw-bold card-title mt-1">{{ $court->name }}</h5>
                        <p class="text-muted small flex-grow-1">{{ $court->description }}</p>
                        
                        <div class="bg-light p-2 rounded-3 small mb-3 border">
                            <div class="text-secondary"><i class="bi bi-clock-fill me-1 text-success"></i> <strong class="text-dark">เวลาเปิด-ปิด:</strong> {{ substr($court->open_time, 0, 5) }} - {{ substr($court->close_time, 0, 5) }} น.</div>
                            <div class="text-secondary mt-1"><i class="bi bi-chat-left-text-fill me-1 text-primary"></i> <strong class="text-dark">รีวิว:</strong> {{ $court->reviews_count }} รายการ</div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('courts.show', $court) }}" class="btn btn-outline-primary w-50">
                                <i class="bi bi-info-circle me-1"></i> รายละเอียด
                            </a>
                            <a href="{{ route('bookings.create', ['court_id' => $court->id]) }}" class="btn btn-primary w-50 {{ $court->status !== 'available' ? 'disabled' : '' }}">
                                <i class="bi bi-calendar-check me-1"></i> จองทันที
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-search fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">ไม่พบข้อมูลสนามตามเงื่อนไขที่เลือก</h5>
                <a href="{{ route('courts.index') }}" class="btn btn-sm btn-outline-primary mt-2">ล้างตัวกรอง</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
