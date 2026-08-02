<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->latest()->paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:brands',
            'description' => 'nullable|string|max:500',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'is_active'   => 'boolean',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = '/storage/' . $request->file('logo')->store('brands', 'public');
        }

        $brand = Brand::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'logo'        => $logoPath,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        ActivityLog::log('brand.created', "Created brand: {$brand->name}", $brand);

        return redirect()->route('admin.brands.index')->with('success', 'Brand đã được tạo thành công!');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string|max:500',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'is_active'   => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = '/storage/' . $request->file('logo')->store('brands', 'public');
        }

        $brand->update(array_merge($validated, [
            'slug'      => Str::slug($validated['name']),
            'is_active' => $request->boolean('is_active'),
        ]));

        ActivityLog::log('brand.updated', "Updated brand: {$brand->name}", $brand);

        return redirect()->route('admin.brands.index')->with('success', 'Brand đã cập nhật!');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return back()->with('success', 'Brand đã xóa.');
    }
}
