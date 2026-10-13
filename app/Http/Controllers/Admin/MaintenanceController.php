<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\Court;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with('court')->latest('start_date')->get();
        return view('admin.maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $courts = Court::all();
        return view('admin.maintenances.create', compact('courts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id' => ['required', 'exists:courts,id'],
            'start_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'end_time' => ['required'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ], [
            'court_id.required' => 'กรุณาเลือกสนาม',
            'start_date.required' => 'กรุณาระบุวันที่เริ่ม',
            'end_date.required' => 'กรุณาระบุวันที่สิ้นสุด',
            'title.required' => 'กรุณาระบุหัวข้องานปิดปรับปรุง',
        ]);

        Maintenance::create($validated);

        return redirect()->route('admin.maintenances.index')->with('success', 'บันทึกช่วงเวลาปิดปรับปรุงสนามเรียบร้อยแล้ว');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('admin.maintenances.index')->with('success', 'ลบรายการปิดปรับปรุงเรียบร้อยแล้ว');
    }
}
