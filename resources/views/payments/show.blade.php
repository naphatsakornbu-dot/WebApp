@extends('layouts.app')

@section('title', 'ชำระเงินและแนบสลิป - การจอง #' . $booking->id)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm p-4 p-md-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle p-3 mb-2">
                        <i class="bi bi-qr-code fs-1"></i>
                    </div>
                    <h3 class="fw-bold">ชำระเงินและแนบสลิปโอนเงิน</h3>
                    <p class="text-muted small">การจอง #BK-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }} | {{ $booking->court->name }}</p>
                </div>

                <!-- Total Amount Display -->
                <div class="bg-light p-4 rounded-4 text-center mb-4 border">
                    <div class="text-muted small mb-1">ยอดเงินที่ต้องชำระทั้งสิ้น:</div>
                    <h2 class="fw-bold text-success mb-0">฿{{ number_format($booking->grand_total, 2) }}</h2>
                    <div class="small text-muted mt-2">
                        (ค่าสนาม {{ $booking->total_hours }} ชม. ฿{{ number_format($booking->court_price, 2) }} + อุปกรณ์ ฿{{ number_format($booking->equipment_price, 2) }})
                    </div>
                </div>

                <!-- Bank Transfer Details -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 h-100 bg-white">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-bank text-primary fs-4"></i>
                                <span class="fw-bold">โอนผ่านบัญชีธนาคาร</span>
                            </div>
                            <div class="small text-muted mb-1">ธนาคารกสิกรไทย (KBANK)</div>
                            <div class="fw-bold text-dark fs-5 mb-1">123-4-56789-0</div>
                            <div class="small text-muted">ชื่อบัญชี: บจก. แบดมินตัน อารีน่า</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 h-100 bg-white text-center">
                            <div class="small fw-bold text-muted mb-1">PromptPay QR Code</div>
                            <div class="bg-light p-2 rounded-3 d-inline-block border">
                                <i class="bi bi-qr-code-scan fs-1 text-dark"></i>
                            </div>
                            <div class="small text-muted mt-1">พร้อมเพย์: 081-234-5678</div>
                        </div>
                    </div>
                </div>

                <!-- Slip Upload Form -->
                <form action="{{ route('payments.upload', $booking) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="slip_image" class="form-label fw-semibold">
                            แนบหลักฐานสลิปโอนเงิน (Image File) <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="slip_image" id="slip_image" 
                               class="form-control @error('slip_image') is-invalid @enderror" 
                               accept="image/jpeg,image/png,image/jpg,image/webp" required onchange="previewSlip(this)">
                        <div class="form-text small">รองรับไฟล์ภาพ JPG, PNG, WEBP ขนาดไม่เกิน 5 MB</div>
                        @error('slip_image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Slip Preview Box -->
                    <div id="slipPreviewContainer" class="text-center p-3 bg-light rounded-3 mb-4 d-none">
                        <div class="small text-muted mb-2">ตัวอย่างรูปภาพสลิป:</div>
                        <img id="slipPreviewImg" src="" alt="Slip Preview" class="img-fluid rounded-3 shadow-sm" style="max-height: 250px;">
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> ยืนยันการชำระเงินและส่งสลิป
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('bookings.show', $booking) }}" class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> ข้ามไปดูรายละเอียดการจองก่อน
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewSlip(input) {
    const container = document.getElementById('slipPreviewContainer');
    const img = document.getElementById('slipPreviewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            container.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
