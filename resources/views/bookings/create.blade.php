@extends('layouts.app')

@section('title', 'จองสนามแบดมินตัน')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex align-items-center gap-2 mb-4">
                <a href="{{ route('courts.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-arrow-left"></i></a>
                <h3 class="fw-bold text-dark mb-0">แบบฟอร์มการจองสนามแบดมินตัน</h3>
            </div>

            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                @csrf
                <div class="row g-4">
                    <!-- Left: Booking Form -->
                    <div class="col-lg-7">
                        <div class="card p-4 shadow-sm mb-4">
                            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-1-circle-fill text-success me-2"></i>ข้อมูลการจองสนาม</h5>

                            <!-- Court Selection -->
                            <div class="mb-3">
                                <label for="court_id" class="form-label fw-semibold">เลือกสนาม <span class="text-danger">*</span></label>
                                <select name="court_id" id="court_id" class="form-select @error('court_id') is-invalid @enderror" required onchange="calculatePrice()">
                                    @foreach($courts as $court)
                                        <option value="{{ $court->id }}" 
                                                data-price="{{ $court->price_per_hour }}" 
                                                data-open="{{ $court->open_time }}" 
                                                data-close="{{ $court->close_time }}"
                                                {{ (old('court_id', $selectedCourt?->id) == $court->id) ? 'selected' : '' }}>
                                            {{ $court->name }} ({{ $court->floor_type }}) — ฿{{ number_format($court->price_per_hour, 0) }}/ชม.
                                        </option>
                                    @endforeach
                                </select>
                                @error('court_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Booking Date -->
                            <div class="mb-3">
                                <label for="booking_date" class="form-label fw-semibold">วันที่ต้องการจอง <span class="text-danger">*</span></label>
                                <input type="date" name="booking_date" id="booking_date" class="form-control @error('booking_date') is-invalid @enderror" 
                                       value="{{ old('booking_date', $defaultDate) }}" min="{{ date('Y-m-d') }}" required>
                                @error('booking_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start & End Time -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_time" class="form-label fw-semibold">เวลาเริ่มต้น <span class="text-danger">*</span></label>
                                    <select name="start_time" id="start_time" class="form-select @error('start_time') is-invalid @enderror" required onchange="calculatePrice()">
                                        @for($h = 8; $h < 22; $h++)
                                            @php $timeStr = sprintf('%02d:00', $h); @endphp
                                            <option value="{{ $timeStr }}" {{ old('start_time', $defaultStartTime) == $timeStr ? 'selected' : '' }}>
                                                {{ $timeStr }} น.
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_time" class="form-label fw-semibold">เวลาสิ้นสุด <span class="text-danger">*</span></label>
                                    <select name="end_time" id="end_time" class="form-select @error('end_time') is-invalid @enderror" required onchange="calculatePrice()">
                                        @for($h = 9; $h <= 22; $h++)
                                            @php $timeStr = sprintf('%02d:00', $h); @endphp
                                            <option value="{{ $timeStr }}" {{ old('end_time', $defaultEndTime) == $timeStr ? 'selected' : '' }}>
                                                {{ $timeStr }} น.
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mb-0">
                                <label for="notes" class="form-label fw-semibold">หมายเหตุเพิ่มเติม (ถ้ามี)</label>
                                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="เช่น ขอตาข่ายตึงพิเศษ หรือต้องการเปิดไฟสว่างเป็นพิเศษ">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Equipment Rental Selection -->
                        <div class="card p-4 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-2-circle-fill text-success me-2"></i>เช่าอุปกรณ์กีฬาเสริม (ถ้าต้องการ)</h5>
                                <span class="badge bg-light text-muted border">ไม่บังคับ</span>
                            </div>

                            <div class="row g-3">
                                @forelse($equipments as $eq)
                                    <div class="col-12">
                                        <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center bg-light bg-opacity-50">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $eq->name }}</div>
                                                <div class="small text-muted">
                                                    หมวดหมู่: {{ $eq->category }} | คงเหลือ: <span class="badge bg-success-subtle text-success">{{ $eq->available_stock }} ชิ้น</span>
                                                </div>
                                                <div class="text-success fw-semibold small mt-1">฿{{ number_format($eq->rental_price, 2) }} / รายการ</div>
                                            </div>
                                            <div style="width: 110px;">
                                                <input type="number" name="equipments[{{ $eq->id }}]" 
                                                       class="form-control text-center equipment-input" 
                                                       value="{{ old('equipments.' . $eq->id, 0) }}" 
                                                       min="0" max="{{ $eq->available_stock }}" 
                                                       data-price="{{ $eq->rental_price }}" 
                                                       data-name="{{ $eq->name }}"
                                                       onchange="calculatePrice()">
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted small py-2">ไม่มีอุปกรณ์พร้อมให้เช่าในขณะนี้</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Right: Summary Box -->
                    <div class="col-lg-5">
                        <div class="card shadow-sm p-4 sticky-top" style="top: 100px;">
                            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-receipt text-primary me-2"></i>สรุปรายการและราคา</h5>

                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">สนามที่เลือก:</span>
                                    <span class="fw-semibold text-dark" id="summaryCourtName">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">อัตราค่าเช่าสนาม:</span>
                                    <span class="fw-semibold" id="summaryRate">฿0.00 / ชม.</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">จำนวนชั่วโมง:</span>
                                    <span class="fw-semibold" id="summaryHours">0 ชม.</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">รวมค่าสนาม:</span>
                                    <span class="fw-bold text-dark" id="summaryCourtPrice">฿0.00</span>
                                </div>
                            </div>

                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">รวมค่าเช่าอุปกรณ์:</span>
                                    <span class="fw-bold text-dark" id="summaryEquipmentPrice">฿0.00</span>
                                </div>
                                <div class="small text-muted" id="summaryEquipmentList"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fw-bold fs-5 text-dark">ยอดรวมสุทธิ:</span>
                                <span class="fw-bold fs-4 text-success" id="summaryGrandTotal">฿0.00</span>
                            </div>

                            <div class="alert alert-warning small py-2 px-3 rounded-3 mb-3">
                                <i class="bi bi-info-circle me-1"></i> เมื่อกดยืนยันการจอง ระบบจะล็อคสนามและนำคุณไปยังหน้าอัปโหลดสลิปชำระเงิน
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                                <i class="bi bi-check2-circle me-1"></i> ยืนยันการจองสนาม
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function calculatePrice() {
    const courtSelect = document.getElementById('court_id');
    const selectedOption = courtSelect.options[courtSelect.selectedIndex];
    const courtPricePerHour = parseFloat(selectedOption.getAttribute('data-price') || 0);
    const courtName = selectedOption.text.split('—')[0].trim();

    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;

    const startH = parseInt(startTime.split(':')[0]);
    const endH = parseInt(endTime.split(':')[0]);
    let hours = endH - startH;
    if (hours <= 0) hours = 0;

    const courtPrice = hours * courtPricePerHour;

    // Calculate Equipment Subtotal
    let eqPrice = 0;
    let eqSummaryHtml = '';
    const eqInputs = document.querySelectorAll('.equipment-input');
    eqInputs.forEach(input => {
        const qty = parseInt(input.value || 0);
        if (qty > 0) {
            const unitPrice = parseFloat(input.getAttribute('data-price') || 0);
            const name = input.getAttribute('data-name');
            const subtotal = qty * unitPrice;
            eqPrice += subtotal;
            eqSummaryHtml += `<div>• ${name} x ${qty} = ฿${subtotal.toFixed(2)}</div>`;
        }
    });

    const grandTotal = courtPrice + eqPrice;

    // Update DOM
    document.getElementById('summaryCourtName').textContent = courtName;
    document.getElementById('summaryRate').textContent = `฿${courtPricePerHour.toFixed(2)} / ชม.`;
    document.getElementById('summaryHours').textContent = `${hours} ชม. (${startTime} - ${endTime} น.)`;
    document.getElementById('summaryCourtPrice').textContent = `฿${courtPrice.toFixed(2)}`;
    document.getElementById('summaryEquipmentPrice').textContent = `฿${eqPrice.toFixed(2)}`;
    document.getElementById('summaryEquipmentList').innerHTML = eqSummaryHtml;
    document.getElementById('summaryGrandTotal').textContent = `฿${grandTotal.toFixed(2)}`;
}

// Initial calculation on page load
document.addEventListener('DOMContentLoaded', calculatePrice);
</script>
@endpush
