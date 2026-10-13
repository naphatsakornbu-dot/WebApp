<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = User::withCount('bookings')->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function toggleRole(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'ไม่สามารถเปลี่ยนสิทธิ์ของตนเองได้');
        }

        $user->role = $user->role === 'admin' ? 'user' : 'admin';
        $user->save();

        return back()->with('success', "เปลี่ยนสิทธิ์ของ {$user->name} เป็น {$user->role} เรียบร้อยแล้ว");
    }
}
