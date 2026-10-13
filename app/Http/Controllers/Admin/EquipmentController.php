<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::latest()->get();
        return view('admin.equipments.index', compact('equipments'));
    }

    public function create()
    {
        return view('admin.equipments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:50'],
            'rental_price' => ['required', 'numeric', 'min:0'],
            'total_stock' => ['required', 'integer', 'min:0'],
            'available_stock' => ['required', 'integer', 'min:0', 'lte:total_stock'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ], [
            'name.required' => 'กรุณาระบุชื่ออุปกรณ์',
            'category.required' => 'กรุณาระบุหมวดหมู่',
            'rental_price.required' => 'กรุณาระบุราคาเช่า',
            'total_stock.required' => 'กรุณาระบุสต็อกทั้งหมด',
            'available_stock.lte' => 'จำนวนพร้อมเช่าต้องไม่เกินจำนวนสต็อกทั้งหมด',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('equipments', 'public');
        }

        Equipment::create($validated);

        return redirect()->route('admin.equipments.index')->with('success', 'เพิ่มอุปกรณ์ใหม่เรียบร้อยแล้ว');
    }

    public function edit(Equipment $equipment)
    {
        return view('admin.equipments.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:50'],
            'rental_price' => ['required', 'numeric', 'min:0'],
            'total_stock' => ['required', 'integer', 'min:0'],
            'available_stock' => ['required', 'integer', 'min:0', 'lte:total_stock'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        if ($request->hasFile('image')) {
            if ($equipment->image && Storage::disk('public')->exists($equipment->image)) {
                Storage::disk('public')->delete($equipment->image);
            }
            $validated['image'] = $request->file('image')->store('equipments', 'public');
        }

        $equipment->update($validated);

        return redirect()->route('admin.equipments.index')->with('success', 'อัปเดตข้อมูลอุปกรณ์เรียบร้อยแล้ว');
    }

    public function destroy(Equipment $equipment)
    {
        if ($equipment->image && Storage::disk('public')->exists($equipment->image)) {
            Storage::disk('public')->delete($equipment->image);
        }
        $equipment->delete();

        return redirect()->route('admin.equipments.index')->with('success', 'ลบอุปกรณ์เรียบร้อยแล้ว');
    }
}
