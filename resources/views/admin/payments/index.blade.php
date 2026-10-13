@extends('layouts.admin')

@section('page-title', 'ตรวจสอบการชำระเงินและสลิป')

@section('content')
<!-- Bank Account & Reconciliation Overview for Admin -->
<div class="row g-3 mb-4">
    <!-- Arena Bank Accounts (for admin to check with their mobile banking) -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #064e3b 0%, #0f766e 50%, #047857 100%);">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-bank2 fs-5 text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-white">ข้อมูลบัญชีธนาคารของสนาม (สำหรับ Admin เช็คยอด)</h6>
                        <small class="text-white-50">ใช้เปรียบเทียบยอดเงินโอนเข้าในแอปธนาคาร / SMS แจ้งเตือน</small>
                    </div>
                </div>
                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">
                    <i class="bi bi-shield-check me-1"></i> บัญชีรับเงินทางการ
                </span>
            </div>

            <div class="row g-3 pt-2">
                <div class="col-sm-6">
                    <div class="bg-white bg-opacity-10 rounded-3 p-3 border border-white border-opacity-10">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="small text-white-50">ธนาคารกสิกรไทย (KBANK)</span>
                            <span class="badge bg-success text-white" style="font-size: 0.7rem;">พร้อมใช้งาน</span>
                        </div>
                        <div class="fs-5 fw-bold text-warning letter-spacing-1 font-monospace">123-4-56789-0</div>
                        <div class="small text-white-50 mt-1">ชื่อบัญชี: <strong>บจก. ไอที แบดมินตัน อารีน่า</strong></div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="bg-white bg-opacity-10 rounded-3 p-3 border border-white border-opacity-10">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="small text-white-50">พร้อมเพย์ (PromptPay QR)</span>
                            <span class="badge bg-info text-white" style="font-size: 0.7rem;">QR Code</span>
                        </div>
                        <div class="fs-5 fw-bold text-white letter-spacing-1 font-monospace">081-234-5678</div>
                        <div class="small text-white-50 mt-1">เบอร์ผูกระบบ: <strong>ฝ่ายการเงินสนามแบดมินตัน</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats for Admin -->
    <div class="col-lg-5">
        <div class="row g-3 h-100">
            <div class="col-6">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 border-start border-4 border-warning bg-white">
                    <div class="text-muted small">สลิปรอตรวจสอบ</div>
                    <h3 class="fw-bold text-warning-emphasis my-1">{{ $stats['pending_count'] ?? 0 }} <span class="fs-6 text-muted fw-normal">รายการ</span></h3>
                    <div class="small fw-semibold text-warning">฿{{ number_format($stats['pending_amount'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 border-start border-4 border-success bg-white">
                    <div class="text-muted small">อนุมัติแล้ววันนี้</div>
                    <h3 class="fw-bold text-success my-1">{{ $stats['paid_today_count'] ?? 0 }} <span class="fs-6 text-muted fw-normal">รายการ</span></h3>
                    <div class="small fw-semibold text-success">฿{{ number_format($stats['paid_today_amount'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">ยอดชำระเงินที่อนุมัติสะสมทั้งหมด</div>
                        <h4 class="fw-bold text-dark mb-0">฿{{ number_format($stats['total_paid_amount'] ?? 0, 2) }}</h4>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle">
                        <i class="bi bi-cash-coin fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm p-4">
    <!-- Filter Tabs -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill {{ !$status ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">ทั้งหมด</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.payments.index', ['status' => 'pending']) }}">
                    รอตรวจสอบ (Pending)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill {{ $status === 'paid' ? 'active' : '' }}" href="{{ route('admin.payments.index', ['status' => 'paid']) }}">
                    อนุมัติแล้ว (Paid)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill {{ $status === 'rejected' ? 'active' : '' }}" href="{{ route('admin.payments.index', ['status' => 'rejected']) }}">
                    ปฏิเสธ (Rejected)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill {{ $status === 'unpaid' ? 'active' : '' }}" href="{{ route('admin.payments.index', ['status' => 'unpaid']) }}">
                    ยังไม่ชำระ (Unpaid)
                </a>
            </li>
        </ul>
    </div>

    <!-- Payments Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>รหัสการจอง</th>
                    <th>ผู้จอง</th>
                    <th>สนาม</th>
                    <th>ยอดเงิน</th>
                    <th>สลิปหลักฐาน</th>
                    <th>วันเวลาที่โอน</th>
                    <th>สถานะ</th>
                    <th class="text-center">ตรวจสอบ / จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td class="fw-bold">#BK-{{ str_pad($p->booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->booking->user->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $p->booking->user->phone }}</div>
                        </td>
                        <td>{{ $p->booking->court->name }}</td>
                        <td class="fw-bold text-success fs-6">฿{{ number_format($p->amount, 2) }}</td>
                        <td>
                            @if($p->slip_image)
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#slipModal{{ $p->id }}">
                                    <i class="bi bi-image me-1"></i> ดูสลิป
                                </button>

                                <!-- Slip Modal -->
                                <div class="modal fade" id="slipModal{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">สลิปโอนเงิน #BK-{{ str_pad($p->booking->id, 5, '0', STR_PAD_LEFT) }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-3">
                                                <img src="{{ asset('storage/' . $p->slip_image) }}" alt="Slip" class="img-fluid rounded-3 shadow-sm" style="max-height: 400px;">
                                                <div class="mt-2 fw-bold text-success">ยอดตามระบบ: ฿{{ number_format($p->amount, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">ยังไม่แนบสลิป</span>
                            @endif
                        </td>
                        <td>{{ $p->payment_date ? $p->payment_date->format('d/m/Y H:i') . ' น.' : '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $p->status === 'paid' ? 'success' : ($p->status === 'pending' ? 'warning' : ($p->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($p->status === 'pending')
                                <div class="d-flex justify-content-center gap-1">
                                    <form action="{{ route('admin.payments.approve', $p) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" title="อนุมัติการชำระเงิน">
                                            <i class="bi bi-check-circle me-1"></i> อนุมัติ
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}" title="ปฏิเสธสลิป">
                                        <i class="bi bi-x-circle me-1"></i> ปฏิเสธ
                                    </button>
                                </div>

                                <!-- Reject Reason Modal -->
                                <div class="modal fade" id="rejectModal{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-danger">ระบุเหตุผลในการปฏิเสธสลิป</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.payments.reject', $p) }}" method="POST">
                                                @csrf
                                                <div class="modal-body py-3 text-start">
                                                    <label for="admin_note" class="form-label small fw-semibold">ข้อความแจ้งเตือนผู้ใช้</label>
                                                    <textarea name="admin_note" id="admin_note" class="form-control" rows="3" placeholder="เช่น ยอดเงินไม่ตรงกับยอดจอง หรือสลิปไม่ชัดเจน กรุณาแนบใหม่" required></textarea>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4">ยืนยันการปฏิเสธ</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">ไม่พบข้อมูลการชำระเงินในระบบ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payments->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
