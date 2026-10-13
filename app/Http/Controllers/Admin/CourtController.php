<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourtController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');
        if ($status === 'trashed') {
            $courts = Court::onlyTrashed()->withCount('bookings')->latest()->get();
        } else {
            $courts = Court::withCount('bookings')->latest()->get();
        }

        $trashedCount = Court::onlyTrashed()->count();
        $activeCount = Court::count();

        return view('admin.courts.index', compact('courts', 'status', 'trashedCount', 'activeCount'));
    }

    public function create()
    {
        return view('admin.courts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'floor_type' => ['required', 'string', 'max:50'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'open_time' => ['required', 'date_format:H:i'],
            'close_time' => ['required', 'date_format:H:i', 'after:open_time'],
            'status' => ['required', 'in:available,maintenance,closed'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'กรุณาระบุชื่อสนาม',
            'floor_type.required' => 'กรุณาระบุประเภทพื้นสนาม',
            'price_per_hour.required' => 'กรุณาระบุราคาต่อชั่วโมง',
            'open_time.required' => 'กรุณาระบุเวลาเปิด',
            'close_time.required' => 'กรุณาระบุเวลาปิด',
            'close_time.after' => 'เวลาปิดต้องมากกว่าเวลาเปิด',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        Court::create($validated);

        return redirect()->route('admin.courts.index')->with('success', 'เพิ่มข้อมูลสนามใหม่เรียบร้อยแล้ว');
    }

    public function edit(Court $court)
    {
        return view('admin.courts.edit', compact('court'));
    }

    public function update(Request $request, Court $court)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'floor_type' => ['required', 'string', 'max:50'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'open_time' => ['required'],
            'close_time' => ['required', 'after:open_time'],
            'status' => ['required', 'in:available,maintenance,closed'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'description' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('image')) {
            if ($court->image && Storage::disk('public')->exists($court->image)) {
                Storage::disk('public')->delete($court->image);
            }
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        $court->update($validated);

        return redirect()->route('admin.courts.index')->with('success', 'อัปเดตข้อมูลสนามเรียบร้อยแล้ว');
    }

    public function destroy(Court $court)
    {
        $court->delete();

        return redirect()->route('admin.courts.index')->with('success', 'ย้ายสนามไปยังถังขยะเรียบร้อยแล้ว (Soft Delete)');
    }

    public function restore($id)
    {
        $court = Court::onlyTrashed()->findOrFail($id);
        $court->restore();

        return redirect()->route('admin.courts.index', ['status' => 'trashed'])->with('success', 'กู้คืนข้อมูลสนามเรียบร้อยแล้ว');
    }

    public function forceDelete($id)
    {
        $court = Court::onlyTrashed()->findOrFail($id);
        if ($court->image && Storage::disk('public')->exists($court->image)) {
            Storage::disk('public')->delete($court->image);
        }
        $court->forceDelete();

        return redirect()->route('admin.courts.index', ['status' => 'trashed'])->with('success', 'ลบข้อมูลสนามถาวรเรียบร้อยแล้ว');
    }
}
