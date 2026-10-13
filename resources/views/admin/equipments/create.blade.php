@extends('layouts.admin')

@section('page-title', 'เพิ่มอุปกรณ์กีฬาใหม่')

@section('content')
<div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="fw-bold text-dark mb-0">กรอกข้อมูลอุปกรณ์ใหม่</h5>
        <a href="{{ route('admin.equipments.index') }}" class="btn btn-outline-secondary btn-sm">ย้อนกลับ</a>
    </div>

    <form action="{{ route('admin.equipments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label fw-semibold">ชื่ออุปกรณ์ <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="เช่น ไม้แบดมินตัน Yonex Astrox" required>
            </div>
            <div class="col-md-6">
                <label for="category" class="form-label fw-semibold">หมวดหมู่อุปกรณ์ <span class="text-danger">*</span></label>
                <input type="text" name="category" id="category" class="form-control" value="{{ old('category', 'ไม้แบดมินตัน') }}" placeholder="ไม้แบด, ลูกแบด, รองเท้า" required>
            </div>
            <div class="col-md-4">
                <label for="rental_price" class="form-label fw-semibold">ราคาค่าเช่า (บาท) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="rental_price" id="rental_price" class="form-control" value="{{ old('rental_price', '50.00') }}" required>
            </div>
            <div class="col-md-4">
                <label for="total_stock" class="form-label fw-semibold">จำนวนสต็อกทั้งหมด <span class="text-danger">*</span></label>
                <input type="number" name="total_stock" id="total_stock" class="form-control" value="{{ old('total_stock', 10) }}" required>
            </div>
            <div class="col-md-4">
                <label for="available_stock" class="form-label fw-semibold">จำนวนที่พร้อมให้เช่า <span class="text-danger">*</span></label>
                <input type="number" name="available_stock" id="available_stock" class="form-control" value="{{ old('available_stock', 10) }}" required>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label fw-semibold">สถานะ <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>เปิดให้บริการ (Active)</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ปิดให้บริการชั่วคราว (Inactive)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="image" class="form-label fw-semibold">รูปภาพอุปกรณ์</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>
            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="bi bi-save me-1"></i> บันทึกข้อมูลอุปกรณ์
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
