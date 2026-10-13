@extends('layouts.app')

@section('title', $court->name . ' - รายละเอียดและตารางเวลา')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">หน้าแรก</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courts.index') }}">รายการสนาม</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $court->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Left: Court Details -->
        <div class="col-lg-5">
            <div class="card shadow-sm p-4 sticky-top" style="top: 100px;">
                <div class="rounded-3 overflow-hidden mb-3 shadow-sm border">
                    <img src="{{ $court->image_url }}" alt="{{ $court->name }}" class="img-fluid w-100 object-fit-cover" style="max-height: 260px;">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 gap-2" style="min-width: 0;">
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold text-truncate" style="min-width: 0; max-width: calc(100% - 130px);" title="{{ $court->floor_type }}">
                        <i class="bi bi-layers me-1"></i>{{ $court->floor_type }}
                    </span>
                    <span class="star-rating fs-5 text-nowrap flex-shrink-0 d-inline-flex align-items-center gap-1" style="white-space: nowrap !important;">
                        <i class="bi bi-star-fill text-warning"></i>
                        <span class="fw-bold text-dark">{{ number_format($court->average_rating, 1) }}</span>
                        <span class="text-muted fs-6">({{ $court->reviews_count }} รีวิว)</span>
                    </span>
                </div>

                <h3 class="fw-bold text-dark mb-2">{{ $court->name }}</h3>
                <p class="text-muted small mb-4">{{ $court->description }}</p>

                <div class="p-3 bg-light rounded-3 mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">ราคาค่าบริการ:</span>
                        <span class="fw-bold text-success fs-5">฿{{ number_format($court->price_per_hour, 2) }} / ชม.</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">เวลาเปิด-ปิด:</span>
                        <span class="fw-semibold">{{ substr($court->open_time, 0, 5) }} - {{ substr($court->close_time, 0, 5) }} น.</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">สถานะปัจจุบัน:</span>
                        <span class="badge bg-{{ $court->status === 'available' ? 'success' : 'danger' }}">
                            {{ $court->status === 'available' ? 'พร้อมให้บริการ' : 'ปิดปรับปรุง' }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('bookings.create', ['court_id' => $court->id, 'date' => $selectedDate]) }}" class="btn btn-primary btn-lg w-100 {{ $court->status !== 'available' ? 'disabled' : '' }}">
                    <i class="bi bi-calendar-plus me-1"></i> จองสนามนี้ทันที
                </a>
            </div>
        </div>

        <!-- Right: Realtime Availability Timeline & Reviews -->
        <div class="col-lg-7">
            <!-- Availability Matrix Card -->
            <div class="card shadow-sm p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h5 class="fw-bold mb-0"><i class="bi bi-calendar-range text-success me-2"></i>ตารางเวลาว่างของสนาม</h5>
                        <div class="text-muted small">ตรวจสอบช่วงเวลาว่างก่อนทำการจอง</div>
                    </div>
                    <!-- Date Selector Form -->
                    <form action="{{ route('courts.show', $court) }}" method="GET" class="d-flex align-items-center gap-2">
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ $selectedDate }}" min="{{ date('Y-m-d') }}" onchange="this.form.submit()">
                    </form>
                </div>

                <!-- Legend -->
                <div class="d-flex flex-wrap gap-3 mb-3 p-2 bg-light rounded-3 small">
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-success p-1 rounded-circle" style="width: 12px; height: 12px;"></span> ว่างพร้อมจอง
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-danger p-1 rounded-circle" style="width: 12px; height: 12px;"></span> จองแล้ว
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-warning p-1 rounded-circle" style="width: 12px; height: 12px;"></span> ปิดปรับปรุง
                    </div>
                </div>

                <!-- Slots List -->
                <div class="row g-2">
                    @foreach($timeSlots as $slot)
                        <div class="col-6 col-md-4">
                            @if($slot['status'] === 'available')
                                <a href="{{ route('bookings.create', ['court_id' => $court->id, 'date' => $selectedDate, 'start_time' => $slot['start'], 'end_time' => $slot['end']]) }}" 
                                   class="card p-2 text-center text-decoration-none slot-badge slot-available w-100">
                                    <div class="fw-bold">{{ $slot['label'] }}</div>
                                    <span class="small text-success"><i class="bi bi-check-circle me-1"></i>ว่าง (กดจอง)</span>
                                </a>
                            @elseif($slot['status'] === 'maintenance')
                                <div class="card p-2 text-center slot-badge slot-maintenance w-100 opacity-75">
                                    <div class="fw-bold">{{ $slot['label'] }}</div>
                                    <span class="small text-warning-emphasis"><i class="bi bi-tools me-1"></i>ซ่อมบำรุง</span>
                                </div>
                            @else
                                <div class="card p-2 text-center slot-badge slot-booked w-100 opacity-75">
                                    <div class="fw-bold">{{ $slot['label'] }}</div>
                                    <span class="small text-danger"><i class="bi bi-lock-fill me-1"></i>จองแล้ว</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Customer Reviews Card -->
            <div class="card shadow-sm p-4 overflow-hidden rounded-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <h5 class="fw-bold mb-0"><i class="bi bi-chat-heart text-primary me-2"></i>รีวิวจากผู้เล่นจริง ({{ $court->reviews->count() }})</h5>
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i class="bi bi-star-fill me-1"></i>{{ number_format($court->average_rating, 1) }} / 5</span>
                </div>

                @forelse($court->reviews as $rev)
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">
                                <i class="bi bi-person-circle me-1 text-secondary"></i> {{ $rev->user->name }}
                            </div>
                            <div class="star-rating small text-nowrap">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-{{ $i <= $rev->rating ? 'fill' : '' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-muted small mb-2 text-break" style="word-break: break-word; overflow-wrap: anywhere; line-height: 1.6;">
                            "{{ $rev->comment }}"
                        </p>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-clock me-1"></i>{{ $rev->created_at->format('d/m/Y H:i') }} น.
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 small">
                        ยังไม่มีรีวิวสำหรับสนามนี้ เป็นคนแรกที่รีวิวหลังใช้งานเสร็จได้เลย!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
