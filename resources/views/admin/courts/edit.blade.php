@extends('layouts.admin')

@section('page-title', 'แก้ไขข้อมูลสนาม: ' . $court->name)

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="fw-bold text-dark mb-0">แก้ไขข้อมูลสนาม #{{ $court->id }}</h5>
        <a href="{{ route('admin.courts.index') }}" class="btn btn-outline-secondary btn-sm">ย้อนกลับ</a>
    </div>

    <form action="{{ route('admin.courts.update', $court) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label fw-semibold">ชื่อสนาม <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $court->name) }}" required>
            </div>
            <div class="col-md-6">
                <label for="floor_type" class="form-label fw-semibold">ประเภทพื้นผิวสนาม <span class="text-danger">*</span></label>
                <input type="text" name="floor_type" id="floor_type" class="form-control" value="{{ old('floor_type', $court->floor_type) }}" required>
            </div>
            <div class="col-md-4">
                <label for="price_per_hour" class="form-label fw-semibold">ราคาค่าเช่าต่อชั่วโมง (บาท) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="price_per_hour" id="price_per_hour" class="form-control" value="{{ old('price_per_hour', $court->price_per_hour) }}" required>
            </div>
            <div class="col-md-4">
                <label for="open_time" class="form-label fw-semibold">เวลาเปิดให้บริการ <span class="text-danger">*</span></label>
                <input type="time" name="open_time" id="open_time" class="form-control" value="{{ old('open_time', substr($court->open_time, 0, 5)) }}" required>
            </div>
            <div class="col-md-4">
                <label for="close_time" class="form-label fw-semibold">เวลาปิดให้บริการ <span class="text-danger">*</span></label>
                <input type="time" name="close_time" id="close_time" class="form-control" value="{{ old('close_time', substr($court->close_time, 0, 5)) }}" required>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label fw-semibold">สถานะสนาม <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="available" {{ old('status', $court->status) == 'available' ? 'selected' : '' }}>พร้อมให้บริการ (Available)</option>
                    <option value="maintenance" {{ old('status', $court->status) == 'maintenance' ? 'selected' : '' }}>ปิดปรับปรุง (Maintenance)</option>
                    <option value="closed" {{ old('status', $court->status) == 'closed' ? 'selected' : '' }}>ปิดถาวร (Closed)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="image" class="form-label fw-semibold">เปลี่ยนรูปภาพสนาม</label>
                @if($court->image)
                    <div class="mb-2 d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                        <img src="{{ $court->image_url }}" alt="{{ $court->name }}" class="rounded-3 object-fit-cover shadow-sm border" style="width: 80px; height: 60px;">
                        <span class="small text-muted">รูปภาพปัจจุบัน (หากไม่ต้องการเปลี่ยน ปล่อยว่างไว้ได้)</span>
                    </div>
                @endif
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
                <label for="description" class="form-label fw-semibold">รายละเอียดและจุดเด่นของสนาม</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $court->description) }}</textarea>
            </div>
            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="bi bi-save me-1"></i> บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
