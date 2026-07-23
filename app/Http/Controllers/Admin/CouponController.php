<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:coupons',
            'name'        => 'required|string|max:200',
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0',
            'min_order'   => 'nullable|numeric|min:0',
            'max_discount'=> 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date|after_or_equal:starts_at',
            'is_active'   => 'boolean',
        ]);

        $coupon = Coupon::create(array_merge($validated, [
            'code'      => strtoupper($validated['code']),
            'min_order' => $validated['min_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]));

        ActivityLog::log('coupon.created', "Created coupon: {$coupon->code}", $coupon);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon đã tạo thành công!');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0',
            'min_order'   => 'nullable|numeric|min:0',
            'max_discount'=> 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at'   => 'nullable|date',
            'expires_at'  => 'nullable|date',
            'is_active'   => 'boolean',
        ]);

        $coupon->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        ActivityLog::log('coupon.updated', "Updated coupon: {$coupon->code}", $coupon);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon đã cập nhật!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon đã xóa!');
    }
}
