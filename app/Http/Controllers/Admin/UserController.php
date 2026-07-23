<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($role = $request->get('role')) {
            $roleId = DB::table('roles')->where('name', $role)->value('id');
            $query->where('role_id', $roleId);
        }

        $users = $query->withCount('orders')->latest()->paginate(20)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role_id'   => $validated['role_id'],
            'phone'     => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        ActivityLog::log('user.created', "Created user: {$user->email}", $user);

        return redirect()->route('admin.users.index')->with('success', 'Tài khoản đã được tạo!');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role_id'  => 'required|exists:roles,id',
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'role_id' => $validated['role_id'],
            'phone'   => $validated['phone'] ?? null,
        ];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        ActivityLog::log('user.updated', "Updated user: {$user->email}", $user);

        return redirect()->route('admin.users.index')->with('success', 'Tài khoản đã cập nhật!');
    }

    public function toggleActive(User $user)
    {
        // Không cho phép tự tắt chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể vô hiệu hóa tài khoản của chính mình!');
        }
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        ActivityLog::log('user.toggled', "User {$user->email} was {$status}", $user);
        return back()->with('success', "Tài khoản đã được {$status}!");
    }
}
