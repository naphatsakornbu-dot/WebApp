@extends('layouts.app')

@section('title', 'รายละเอียดการจอง #' . $booking->id)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">หน้าแรก</a></li>
            <li class="breadcrumb-item"><a href="{{ route('bookings.my') }}">การจองของฉัน</a></li>
            <li class="breadcrumb-item active" aria-current="page">#BK-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Left: Booking Summary & Payment -->
        <div class="col-lg-7">
            <!-- Booking Information Card -->
            <div class="card shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <span class="text-muted small">รหัสการจอง:</span>
                        <h4 class="fw-bold text-dark mb-0">#BK-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</h4>
                    </div>
                    <span class="badge badge-{{ $booking->status }} px-3 py-2 rounded-pill fs-6">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="small text-muted">สนาม:</div>
                        <div class="fw-bold text-dark fs-5">{{ $booking->court->name }}</div>
                        <div class="small text-muted">{{ $booking->court->floor_type }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="small text-muted">วันที่และเวลาเล่น:</div>
                        <div class="fw-bold text-dark"><i class="bi bi-calendar-event me-1 text-success"></i> {{ date('d M Y', strtotime($booking->booking_date)) }}</div>
                        <div class="small text-muted"><i class="bi bi-clock me-1 text-primary"></i> {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} น. ({{ $booking->total_hours }} ชม.)</div>
                    </div>
                </div>

                <!-- Price Breakdown Table -->
                <h6 class="fw-bold mb-2">สรุปค่าใช้จ่าย</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>รายการ</th>
                                <th class="text-center">จำนวน/ชม.</th>
                                <th class="text-end">ราคาต่อหน่วย</th>
                                <th class="text-end">รวม (บาท)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ค่าเช่าสนาม ({{ $booking->court->name }})</td>
                                <td class="text-center">{{ $booking->total_hours }} ชม.</td>
                                <td class="text-end">฿{{ number_format($booking->court->price_per_hour, 2) }}</td>
                                <td class="text-end fw-semibold">฿{{ number_format($booking->court_price, 2) }}</td>
                            </tr>
                            @foreach($booking->equipmentRentals as $rental)
                                <tr>
                                    <td>ค่าเช่า {{ $rental->equipment?->name ?? 'อุปกรณ์' }}</td>
                                    <td class="text-center">{{ $rental->quantity }} ชิ้น</td>
                                    <td class="text-end">฿{{ number_format($rental->unit_price, 2) }}</td>
                                    <td class="text-end fw-semibold">฿{{ number_format($rental->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="table-active">
                                <td colspan="3" class="fw-bold text-end">ยอดรวมสุทธิ:</td>
                                <td class="text-end fw-bold fs-6 text-success">฿{{ number_format($booking->grand_total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($booking->notes)
                    <div class="p-3 bg-light rounded-3 small">
                        <strong>หมายเหตุ:</strong> {{ $booking->notes }}
                    </div>
                @endif
            </div>

            <!-- Payment Details Card -->
            <div class="card shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-credit-card text-success me-2"></i>ข้อมูลการชำระเงิน</h5>
                    <span class="badge bg-{{ $booking->payment?->status === 'paid' ? 'success' : ($booking->payment?->status === 'pending' ? 'warning' : 'danger') }} px-3 py-2 rounded-pill">
                        สถานะ: {{ ucfirst($booking->payment?->status ?? 'unpaid') }}
                    </span>
                </div>

                @if($booking->payment)
                    @if($booking->payment->slip_image)
                        <div class="mb-3">
                            <div class="small text-muted mb-2">หลักฐานสลิปโอนเงินที่แนบไว้:</div>
                            <div class="text-center p-2 bg-light rounded-3">
                                <img src="{{ asset('storage/' . $booking->payment->slip_image) }}" alt="Slip" class="img-fluid rounded-3 shadow-sm" style="max-height: 250px;">
                            </div>
                            <div class="small text-muted text-center mt-1">อัปโหลดเมื่อ: {{ $booking->payment->payment_date?->format('d/m/Y H:i') }} น.</div>
                        </div>
                    @endif

                    @if($booking->payment->admin_note)
                        <div class="alert alert-info small py-2 px-3 rounded-3 mb-3">
                            <strong>ข้อความจากแอดมิน:</strong> {{ $booking->payment->admin_note }}
                        </div>
                    @endif

                    @if(in_array($booking->payment->status, ['unpaid', 'rejected']))
                        <div class="text-center mt-3">
                            <a href="{{ route('payments.show', $booking) }}" class="btn btn-warning w-100 py-2 fw-bold">
                                <i class="bi bi-upload me-1"></i> {{ $booking->payment->status === 'rejected' ? 'อัปโหลดสลิปใหม่' : 'ไปที่หน้าแนบสลิปโอนเงิน' }}
                            </a>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Cancel Action -->
            @if(in_array($booking->status, ['pending', 'confirmed']))
                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจองนี้? ระบบจะคืนสต็อกอุปกรณ์ทันที')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">
                        <i class="bi bi-x-circle me-1"></i> ขอยกเลิกการจองสนาม
                    </button>
                </form>
            @endif
        </div>

        <!-- Right: Matchmaking & Review Box -->
        <div class="col-lg-5">
            <!-- Matchmaking Community Section -->
            @if(in_array($booking->status, ['confirmed', 'completed']))
                <div class="card shadow-sm p-4 mb-4 border-primary border-top border-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-people text-primary me-2"></i>ระบบหาก๊วนเพื่อนร่วมเล่น (Matchmaking)</h5>
                    
                    @if($booking->playerMatch)
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-{{ $booking->playerMatch->status === 'open' ? 'success' : 'secondary' }} rounded-pill">
                                    {{ $booking->playerMatch->status === 'open' ? 'เปิดรับสมาชิก' : 'สมาชิกเต็มแล้ว' }}
                                </span>
                                <span class="badge bg-info-subtle text-info">ระดับ: {{ ucfirst($booking->playerMatch->skill_level) }}</span>
                            </div>
                            <div class="fw-semibold text-dark mb-1">ต้องการเพิ่ม: {{ $booking->playerMatch->slots_needed }} คน (ได้แล้ว {{ $booking->playerMatch->slots_filled }} คน)</div>
                            <p class="small text-muted mb-0">{{ $booking->playerMatch->details }}</p>
                        </div>

                        <!-- Match Requests / Members List -->
                        <h6 class="fw-bold small mb-2">ผู้ที่ขอเข้าร่วมรอบนี้:</h6>
                        @forelse($booking->playerMatch->members as $member)
                            <div class="p-2 border rounded-3 mb-2 d-flex justify-content-between align-items-center bg-white">
                                <div>
                                    <div class="fw-bold small">{{ $member->user->name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $member->message ?? 'ไม่มีข้อความ' }}</div>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    @if($member->status === 'pending' && $booking->user_id === Auth::id())
                                        <form action="{{ route('matches.respond', $member) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="action" value="accept">
                                            <button type="submit" class="btn btn-sm btn-success py-0 px-2" title="รับเข้าก๊วน"><i class="bi bi-check"></i> รับ</button>
                                        </form>
                                        <form action="{{ route('matches.respond', $member) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="ปฏิเสธ"><i class="bi bi-x"></i></button>
                                        </form>
                                    @else
                                        <span class="badge bg-{{ $member->status === 'accepted' ? 'success' : ($member->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $member->status }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small">ยังไม่มีผู้ขอเข้าร่วม</div>
                        @endforelse
                    @else
                        <p class="text-muted small">ต้องการหาเพื่อนร่วมตีแบด หรือแชร์ค่าสนามรอบนี้หรือไม่?</p>
                        <a href="{{ route('matches.create', $booking) }}" class="btn btn-outline-primary w-100 rounded-pill">
                            <i class="bi bi-person-plus me-1"></i> เปิดรับสมัครเพื่อนร่วมก๊วน
                        </a>
                    @endif
                </div>
            @endif

            <!-- Post-Booking Verified Review Section -->
            @if($booking->status === 'completed')
                <div class="card shadow-sm p-4 mb-4 border-warning border-top border-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-star-fill text-warning me-2"></i>รีวิวสนามหลังใช้งาน</h5>
                    
                    @if($booking->review)
                        <div class="p-3 bg-light rounded-3">
                            <div class="star-rating text-nowrap mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-{{ $i <= $booking->review->rating ? 'fill' : '' }}"></i>
                                @endfor
                                <span class="fw-bold text-dark ms-1">({{ $booking->review->rating }} / 5)</span>
                            </div>
                            <p class="text-muted small mb-1">"{{ $booking->review->comment }}"</p>
                            <div class="text-muted" style="font-size: 0.75rem;">รีวิวเมื่อ: {{ $booking->review->created_at->format('d/m/Y H:i') }} น.</div>
                        </div>
                    @else
                        <p class="text-muted small">ให้คะแนนความพึงพอใจและแบ่งปันประสบการณ์เพื่อเป็นประโยชน์ต่อผู้เล่นคนอื่น</p>
                        <form action="{{ route('reviews.store', $booking) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="rating" class="form-label small fw-semibold">ให้คะแนนดาว (1 - 5 ดาว) <span class="text-danger">*</span></label>
                                <select name="rating" id="rating" class="form-select" required>
                                    <option value="5">⭐⭐⭐⭐⭐ 5 ดาว (ยอดเยี่ยมมาก)</option>
                                    <option value="4">⭐⭐⭐⭐ 4 ดาว (ดีมาก)</option>
                                    <option value="3">⭐⭐⭐ 3 ดาว (ปานกลาง)</option>
                                    <option value="2">⭐⭐ 2 ดาว (พอใช้)</option>
                                    <option value="1">⭐ 1 ดาว (ควรปรับปรุง)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label small fw-semibold">ความคิดเห็นของคุณ</label>
                                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="ความสะอาดของสนาม, แสงไฟ, การบริการ..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning w-100 py-2 fw-bold">
                                <i class="bi bi-send me-1"></i> ส่งรีวิว
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
