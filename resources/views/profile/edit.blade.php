@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลส่วนตัว')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-person-gear text-success me-2"></i>แก้ไขข้อมูลส่วนตัว
                </h4>
                <div class="text-muted small mb-4">จัดการข้อมูลโปรไฟล์ รูปประจำตัว และรหัสผ่านของคุณ</div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Avatar Upload Section -->
                    <div class="card bg-light border-0 rounded-4 p-3 mb-4">
                        <label class="form-label fw-semibold text-dark mb-2">รูปโปรไฟล์ (Profile Picture)</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                <img id="avatarPreview"
                                     src="{{ $user->avatar_url }}"
                                     alt="{{ $user->name }}"
                                     class="rounded-circle object-fit-cover shadow-sm border border-2 border-white"
                                     style="width: 76px; height: 76px;">
                            </div>
                            <div class="flex-grow-1">
                                <input type="file"
                                       class="form-control form-control-sm @error('avatar') is-invalid @enderror"
                                       id="avatarInput"
                                       name="avatar"
                                       accept="image/jpeg,image/png,image/jpg,image/webp">
                                <div class="form-text small text-muted mt-1" style="font-size: 0.78rem;">
                                    รองรับ JPG, PNG, WEBP (ระบบจะปรับและย่อขนาดให้อัตโนมัติ)
                                </div>
                                @error('avatar')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                                @if($user->has_custom_avatar)
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="removeAvatar">
                                        <label class="form-check-label text-danger small fw-semibold" for="removeAvatar">
                                            <i class="bi bi-trash me-1"></i> ลบรูปโปรไฟล์ (กลับไปใช้รูปเริ่มต้น)
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">ชื่อ-นามสกุล</label>
                        <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">อีเมล (ไม่สามารถเปลี่ยนได้)</label>
                        <input type="email" class="form-control rounded-3 bg-light" id="email" value="{{ $user->email }}" disabled>
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label fw-semibold">เบอร์โทรศัพท์</label>
                        <input type="text" class="form-control rounded-3 @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required placeholder="08xxxxxxxx">
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Change Section -->
                    <div class="p-3 bg-light rounded-4 mb-4 border">
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="bi bi-shield-lock me-1 text-primary"></i> เปลี่ยนรหัสผ่านใหม่ (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)
                        </h6>
                        <div class="mb-3">
                            <label for="password" class="form-label small fw-semibold">รหัสผ่านใหม่</label>
                            <input type="password" class="form-control rounded-3 @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••">
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-0">
                            <label for="password_confirmation" class="form-label small fw-semibold">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" class="form-control rounded-3" id="password_confirmation" name="password_confirmation" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-4">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-save me-1"></i> บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    let isCompressing = false;

    avatarInput?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Show instant preview
        const reader = new FileReader();
        reader.onload = function(event) {
            if (avatarPreview) {
                avatarPreview.src = event.target.result;
            }

            // Auto-compress large images using Canvas to prevent server upload limit errors
            if (file.type.startsWith('image/')) {
                isCompressing = true;
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const MAX_SIZE = 600;
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > MAX_SIZE) {
                            height = Math.round((height * MAX_SIZE) / width);
                            width = MAX_SIZE;
                        }
                    } else {
                        if (height > MAX_SIZE) {
                            width = Math.round((width * MAX_SIZE) / height);
                            height = MAX_SIZE;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob(function(blob) {
                        if (blob && window.DataTransfer) {
                            const newFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            const dt = new DataTransfer();
                            dt.items.add(newFile);
                            avatarInput.files = dt.files;
                        }
                        isCompressing = false;
                    }, 'image/jpeg', 0.85);
                };
                img.src = event.target.result;
            }
        };
        reader.readAsDataURL(file);
    });

    // Make sure form does not submit while compression is ongoing
    document.querySelector('form')?.addEventListener('submit', function(e) {
        if (isCompressing) {
            e.preventDefault();
            setTimeout(() => {
                this.submit();
            }, 300);
        }
    });
</script>
@endpush
@endsection
