@extends('layouts.admin')

@section('page-title', 'บันทึกช่วงเวลาปิดปรับปรุงสนาม')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="fw-bold text-dark mb-0">กำหนดช่วงเวลาปิดปรับปรุง</h5>
        <a href="{{ route('admin.maintenances.index') }}" class="btn btn-outline-secondary btn-sm">ย้อนกลับ</a>
    </div>

    <form action="{{ route('admin.maintenances.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="court_id" class="form-label fw-semibold">เลือกสนามที่ต้องการปิดปรับปรุง <span class="text-danger">*</span></label>
                <select name="court_id" id="court_id" class="form-select" required>
                    <option value="">-- เลือกสนาม --</option>
                    @foreach($courts as $c)
                        <option value="{{ $c->id }}" {{ old('court_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->floor_type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="title" class="form-label fw-semibold">หัวข้องานปรับปรุง / ซ่อมบำรุง <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="เช่น ซ่อมระบบไฟ LED, ขัดล้างพื้นยาง, ปรับปรุงระบบแอร์" required>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label fw-semibold">วันที่เริ่มต้น <span class="text-danger">*</span></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label for="start_time" class="form-label fw-semibold">เวลาเริ่มต้น <span class="text-danger">*</span></label>
                <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', '08:00') }}" required>
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label fw-semibold">วันที่สิ้นสุด <span class="text-danger">*</span></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label for="end_time" class="form-label fw-semibold">เวลาสิ้นสุด <span class="text-danger">*</span></label>
                <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', '12:00') }}" required>
            </div>
            <div class="col-12">
                <label for="description" class="form-label fw-semibold">รายละเอียดช่าง / ผู้รับผิดชอบ</label>
                <textarea name="description" id="description" class="form-control" rows="3" placeholder="ระบุรายละเอียดงานหรือผู้ติดต่อช่าง">{{ old('description') }}</textarea>
            </div>
            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-warning px-4 py-2 fw-bold">
                    <i class="bi bi-tools me-1"></i> บันทึกตารางปิดปรับปรุง
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
