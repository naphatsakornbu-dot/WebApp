@extends('layouts.app')

@section('title', 'หน้าแรก - จองสนามแบดมินตันมาตรฐานสากล')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold mb-3">
                    <i class="bi bi-star-fill text-warning me-1"></i> สนามแบดมินตันอันดับ 1 ในเมือง
                </span>
                <h1 class="display-4 fw-bold text-white mb-3">
                    จองสนามแบดมินตันง่ายๆ <br><span class="text-warning">เช็คเวลาว่างได้ทันที</span>
                </h1>
                <p class="lead text-light opacity-75 mb-4">
                    สนามพื้นยาง BWF มาตรฐานแข่งขัน แสงไฟ LED ถนอมสายตา พร้อมระบบเช่าอุปกรณ์กีฬาและจับคู่หาเพื่อนร่วมก๊วน
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('courts.index') }}" class="btn btn-warning btn-lg px-4 py-3 fw-bold rounded-pill shadow">
                        <i class="bi bi-calendar2-plus me-2"></i> ดูสนามและจองเวลา
                    </a>
                    <a href="{{ route('matches.index') }}" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold rounded-pill">
                        <i class="bi bi-people me-2"></i> ดูก๊วนหาเพื่อนเล่น
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="bg-white bg-opacity-10 p-4 rounded-4 backdrop-blur border border-white border-opacity-20 shadow-lg">
                    <h5 class="text-white fw-bold mb-3"><i class="bi bi-shield-check text-success me-2"></i>ทำไมต้องเลือกเรา?</h5>
                    <div class="d-flex align-items-center gap-3 text-start text-white mb-3 p-2 rounded-3 bg-dark bg-opacity-25">
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        <div>
                            <div class="fw-bold">ไม่มีการจองซ้อน (Conflict-Free)</div>
                            <div class="small text-light opacity-75">ระบบตรวจสอบเวลาว่างอัตโนมัติ 100%</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 text-start text-white mb-3 p-2 rounded-3 bg-dark bg-opacity-25">
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        <div>
                            <div class="fw-bold">เช่าอุปกรณ์ครบวงจร</div>
                            <div class="small text-light opacity-75">ไม้แบด Yonex, ลูกขนไก่, รองเท้าครบไซส์</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 text-start text-white p-2 rounded-3 bg-dark bg-opacity-25">
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        <div>
                            <div class="fw-bold">เปิดหาเพื่อนตีแบดได้ตลอด 24 ชม.</div>
                            <div class="small text-light opacity-75">คัดกรองระดับฝีมือได้ตามต้องการ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Counter -->
<div class="container mb-5">
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="card p-4 h-100 bg-white border-0 shadow-sm">
                <i class="bi bi-grid-3x3 text-success fs-1 mb-2"></i>
                <h2 class="fw-bold text-dark mb-0">{{ $stats['total_courts'] }} สนาม</h2>
                <p class="text-muted small mb-0">สนามมาตรฐานพร้อมระบบปรับอากาศ</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 h-100 bg-white border-0 shadow-sm">
                <i class="bi bi-check2-circle text-primary fs-1 mb-2"></i>
                <h2 class="fw-bold text-dark mb-0">{{ $stats['total_bookings'] }}+ ครั้ง</h2>
                <p class="text-muted small mb-0">การจองและเข้าใช้บริการสำเร็จ</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 h-100 bg-white border-0 shadow-sm">
                <i class="bi bi-star-fill text-warning fs-1 mb-2"></i>
                <h2 class="fw-bold text-dark mb-0">{{ number_format($stats['average_rating'], 1) }} / 5</h2>
                <p class="text-muted small mb-0">คะแนนความพึงพอใจจากผู้เล่นจริง</p>
            </div>
        </div>
    </div>
</div>

<!-- Featured Courts -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="text-success fw-bold text-uppercase small">สนามของเรา</span>
            <h2 class="fw-bold text-dark mb-0">สนามแบดมินตันพร้อมให้บริการ</h2>
        </div>
        <a href="{{ route('courts.index') }}" class="btn btn-outline-success rounded-pill">
            ดูสนามทั้งหมด <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row g-4">
        @forelse($courts as $court)
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 card-hover">
                    <div class="position-relative overflow-hidden" style="border-top-left-radius: inherit; border-top-right-radius: inherit;">
                        <img src="{{ $court->image_url }}" alt="{{ $court->name }}" class="card-img-top object-fit-cover w-100" style="height: 190px;">
                        <span class="position-absolute top-0 end-0 m-3 badge bg-dark bg-opacity-75 rounded-pill shadow-sm">
                            ฿{{ number_format($court->price_per_hour, 0) }} / ชม.
                        </span>
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
                        <div class="text-secondary small mb-3">
                            <i class="bi bi-clock me-1 text-success"></i> <span class="text-dark fw-medium">เวลาเปิด-ปิด:</span> {{ substr($court->open_time, 0, 5) }} - {{ substr($court->close_time, 0, 5) }} น.
                        </div>
                        <a href="{{ route('courts.show', $court) }}" class="btn btn-primary w-100">
                            <i class="bi bi-calendar-check me-1"></i> ดูตารางว่าง & จอง
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-4">
                <p class="text-muted">ยังไม่มีข้อมูลสนามในขณะนี้</p>
            </div>
        @endforelse
    </div>
</section>

<!-- Community / Matchmaking Teaser -->
@if($openMatches->isNotEmpty())
<section class="container mb-5">
    <div class="p-4 p-md-5 rounded-4 bg-dark text-white position-relative overflow-hidden shadow">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">
                    <i class="bi bi-fire me-1"></i> Community Matchmaking
                </span>
                <h2 class="fw-bold mb-3">หาก๊วนตีแบด? เล่นคนเดียวไม่ต้องเหงา</h2>
                <p class="text-light opacity-75 mb-4">
                    มีผู้จองสนามกำลังเปิดรับสมาชิกเพิ่ม กดเข้าร่วมก๊วนเพื่อแชร์ค่าสนามและแลกเปลี่ยนฝีมือได้เลย
                </p>
                <a href="{{ route('matches.index') }}" class="btn btn-warning rounded-pill px-4 py-2 fw-bold">
                    ดูก๊วนทั้งหมด ({{ $openMatches->count() }}) <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    @foreach($openMatches as $match)
                        <div class="col-12">
                            <div class="bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-white">{{ $match->booking->court->name }}</div>
                                    <div class="small text-light opacity-75">
                                        <i class="bi bi-calendar-event me-1"></i> {{ date('d M Y', strtotime($match->booking->booking_date)) }} 
                                        ({{ substr($match->booking->start_time, 0, 5) }} - {{ substr($match->booking->end_time, 0, 5) }} น.)
                                    </div>
                                    <span class="badge bg-info-subtle text-info mt-1">ระดับ: {{ ucfirst($match->skill_level) }}</span>
                                </div>
                                <div class="text-end">
                                    <div class="badge bg-success mb-2">ขาด {{ $match->remaining_slots }} คน</div>
                                    <div><a href="{{ route('matches.index') }}" class="btn btn-sm btn-light rounded-pill">ขอเข้าร่วม</a></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Reviews Section -->
<section class="container mb-5">
    <div class="text-center mb-4">
        <span class="text-success fw-bold text-uppercase small">เสียงตอบรับจากผู้เล่น</span>
        <h2 class="fw-bold text-dark">รีวิวและความประทับใจ</h2>
    </div>

    <div class="row g-4">
        @forelse($recentReviews as $review)
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative d-flex flex-column">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div class="star-rating text-nowrap">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-{{ $i <= $review->rating ? 'fill' : '' }}"></i>
                            @endfor
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle text-truncate" style="max-width: 170px;" title="{{ $review->court->name }}">
                            <i class="bi bi-grid-3x3 me-1"></i>{{ $review->court->name }}
                        </span>
                    </div>
                    <p class="text-muted small flex-grow-1 mb-3 text-break" style="word-break: break-word; overflow-wrap: anywhere; line-height: 1.6;">
                        "{{ $review->comment }}"
                    </p>
                    <div class="d-flex align-items-center gap-2 pt-3 border-top mt-auto">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-weight: 600; font-size: 0.8rem;">
                            {{ mb_substr($review->user->name, 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <div class="fw-semibold small text-dark text-truncate">{{ $review->user->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $review->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-3">ยังไม่มีรีวิวในระบบ</div>
        @endforelse
    </div>
</section>
@endsection
