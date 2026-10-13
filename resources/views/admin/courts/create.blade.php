@extends('layouts.admin')

@section('page-title', 'เพิ่มสนามแบดมินตันใหม่')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="fw-bold text-dark mb-0">กรอกข้อมูลสนามใหม่</h5>
        <a href="{{ route('admin.courts.index') }}" class="btn btn-outline-secondary btn-sm">ย้อนกลับ</a>
    </div>

    <form action="{{ route('admin.courts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label fw-semibold">ชื่อสนาม <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="เช่น สนาม 5 (VIP Court)" required>
            </div>
            <div class="col-md-6">
                <label for="floor_type" class="form-label fw-semibold">ประเภทพื้นผิวสนาม <span class="text-danger">*</span></label>
                <input type="text" name="floor_type" id="floor_type" class="form-control" value="{{ old('floor_type', 'ยาง PB มาตรฐาน BWF') }}" required>
            </div>
            <div class="col-md-4">
                <label for="price_per_hour" class="form-label fw-semibold">ราคาค่าเช่าต่อชั่วโมง (บาท) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="price_per_hour" id="price_per_hour" class="form-control" value="{{ old('price_per_hour', '150.00') }}" required>
            </div>
            <div class="col-md-4">
                <label for="open_time" class="form-label fw-semibold">เวลาเปิดให้บริการ <span class="text-danger">*</span></label>
                <input type="time" name="open_time" id="open_time" class="form-control" value="{{ old('open_time', '08:00') }}" required>
            </div>
            <div class="col-md-4">
                <label for="close_time" class="form-label fw-semibold">เวลาปิดให้บริการ <span class="text-danger">*</span></label>
                <input type="time" name="close_time" id="close_time" class="form-control" value="{{ old('close_time', '22:00') }}" required>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label fw-semibold">สถานะสนาม <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>พร้อมให้บริการ (Available)</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>ปิดปรับปรุง (Maintenance)</option>
                    <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>ปิดถาวร (Closed)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="image" class="form-label fw-semibold">รูปภาพสนาม</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
                <label for="description" class="form-label fw-semibold">รายละเอียดและจุดเด่นของสนาม</label>
                <textarea name="description" id="description" class="form-control" rows="3" placeholder="ระบุประเภทหลอดไฟ, ความหนาพื้นยาง, แอร์ หรือสิ่งอำนวยความสะดวก">{{ old('description') }}</textarea>
            </div>
            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="bi bi-save me-1"></i> บันทึกข้อมูลสนาม
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
