@extends('layouts.app')

@section('title', 'หาก๊วนตีแบด - Player Matchmaking')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">ก๊วนหาเพื่อนตีแบด (Community Matchmaking)</h2>
            <p class="text-muted small mb-0">ค้นหาก๊วนที่เปิดรับสมาชิก กดขอเข้าร่วม หรือเปิดก๊วนของคุณเอง</p>
        </div>
        <div>
            <a href="{{ route('bookings.my') }}" class="btn btn-outline-primary rounded-pill px-3">
                <i class="bi bi-plus-circle me-1"></i> เปิดก๊วนจากรายการจองของฉัน
            </a>
        </div>
    </div>

    <!-- Skill Filter Tabs -->
    <div class="card p-3 mb-4 shadow-sm">
        <form action="{{ route('matches.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <span class="small fw-semibold text-muted me-2">เลือกระดับฝีมือ:</span>
            <a href="{{ route('matches.index') }}" class="btn btn-sm {{ !$skill ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill">ทั้งหมด</a>
            <a href="{{ route('matches.index', ['skill' => 'beginner']) }}" class="btn btn-sm {{ $skill === 'beginner' ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill">มือใหม่ (Beginner)</a>
            <a href="{{ route('matches.index', ['skill' => 'intermediate']) }}" class="btn btn-sm {{ $skill === 'intermediate' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill">ระดับกลาง (Intermediate)</a>
            <a href="{{ route('matches.index', ['skill' => 'advanced']) }}" class="btn btn-sm {{ $skill === 'advanced' ? 'btn-danger' : 'btn-outline-secondary' }} rounded-pill">ระดับแข่งขัน (Advanced)</a>
        </form>
    </div>

    <!-- Match Cards Grid -->
    <div class="row g-4">
        @forelse($matches as $match)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm p-4 card-hover d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-{{ $match->skill_level === 'beginner' ? 'success' : ($match->skill_level === 'intermediate' ? 'primary' : ($match->skill_level === 'advanced' ? 'danger' : 'secondary')) }} rounded-pill">
                            ระดับ: {{ ucfirst($match->skill_level) }}
                        </span>
                        <span class="badge bg-{{ $match->status === 'open' ? 'success' : 'secondary' }} rounded-pill">
                            {{ $match->status === 'open' ? 'ขาดอีก ' . $match->remaining_slots . ' คน' : 'เต็มแล้ว' }}
                        </span>
                    </div>

                    <h5 class="fw-bold text-dark mb-1 mt-1">{{ $match->booking->court->name }}</h5>
                    
                    <div class="small text-muted mb-2">
                        <i class="bi bi-calendar-event me-1 text-success"></i> {{ date('d M Y', strtotime($match->booking->booking_date)) }}
                    </div>
                    <div class="small text-muted mb-3">
                        <i class="bi bi-clock me-1 text-primary"></i> {{ substr($match->booking->start_time, 0, 5) }} - {{ substr($match->booking->end_time, 0, 5) }} น.
                    </div>

                    <div class="p-3 bg-light rounded-3 small flex-grow-1 mb-3">
                        <div class="fw-semibold text-dark mb-1">
                            <i class="bi bi-person-fill text-secondary me-1"></i> เจ้าของก๊วน: {{ $match->booking->user->name }}
                        </div>
                        <p class="text-muted mb-0">"{{ $match->details ?? 'ยินดีต้อนรับเพื่อนร่วมเล่นทุกคนครับ' }}"</p>
                    </div>

                    <!-- Accepted Members Count -->
                    <div class="d-flex align-items-center justify-content-between small text-muted mb-3">
                        <span>สมาชิกที่เข้าร่วมแล้ว:</span>
                        <span class="fw-bold text-dark">{{ $match->slots_filled }} / {{ $match->slots_needed }} คน</span>
                    </div>

                    <!-- Join Button / Modal Trigger -->
                    @auth
                        @if($match->booking->user_id === Auth::id())
                            <a href="{{ route('bookings.show', $match->booking) }}" class="btn btn-outline-primary btn-sm rounded-pill w-100">
                                <i class="bi bi-gear me-1"></i> จัดการก๊วนของคุณ
                            </a>
                        @elseif($match->status === 'open')
                            <button type="button" class="btn btn-primary btn-sm rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#joinModal{{ $match->id }}">
                                <i class="bi bi-person-plus me-1"></i> ขอเข้าร่วมก๊วนนี้
                            </button>

                            <!-- Join Modal -->
                            <div class="modal fade" id="joinModal{{ $match->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold">ขอเข้าร่วมก๊วนตีแบด</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('matches.join', $match) }}" method="POST">
                                            @csrf
                                            <div class="modal-body py-3">
                                                <div class="p-3 bg-light rounded-3 small mb-3">
                                                    <div><strong>สนาม:</strong> {{ $match->booking->court->name }}</div>
                                                    <div><strong>เวลา:</strong> {{ date('d M Y', strtotime($match->booking->booking_date)) }} ({{ substr($match->booking->start_time, 0, 5) }} - {{ substr($match->booking->end_time, 0, 5) }} น.)</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="message" class="form-label small fw-semibold">ข้อความแนะนำตัวสั้นๆ (ฝีมือ, เบอร์ติดต่อ)</label>
                                                    <textarea name="message" id="message" class="form-control" rows="3" placeholder="สวัสดีครับ เล่นระดับกลางๆ ขอร่วมเล่นด้วยคนครับ"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4">ส่งคำขอเข้าร่วม</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <button class="btn btn-secondary btn-sm rounded-pill w-100" disabled>สมาชิกเต็มแล้ว</button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm rounded-pill w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบเพื่อขอร่วม
                        </a>
                    @endauth
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-people fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">ยังไม่มีก๊วนที่เปิดรับสมาชิกในขณะนี้</h5>
                <p class="text-muted small">คุณสามารถจองสนามแล้วเปิดหาก๊วนเพื่อนร่วมเล่นได้ทันที</p>
                <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm rounded-pill">จองสนามและเปิดก๊วน</a>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $matches->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
