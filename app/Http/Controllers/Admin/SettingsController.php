<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = DB::table('settings')->get()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'               => 'required|string|max:100',
            'site_email'              => 'required|email',
            'site_phone'              => 'nullable|string|max:20',
            'shipping_fee'            => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
        ]);

        $settings = [
            'site_name'               => $request->site_name,
            'site_email'              => $request->site_email,
            'site_phone'              => $request->site_phone ?? '',
            'shipping_fee'            => $request->shipping_fee,
            'free_shipping_threshold' => $request->free_shipping_threshold,
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        ActivityLog::log('settings.updated', 'Admin updated site settings');

        return back()->with('success', 'Cài đặt đã lưu thành công!');
    }
}
