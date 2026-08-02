<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Đồng bộ thông tin user Firebase vào database MySQL.
     * Gọi khi khách hàng đăng nhập Google lần đầu tiên.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'uid'          => 'required|string',
            'email'        => 'required|email',
            'display_name' => 'nullable|string',
            'photo_url'    => 'nullable|string',
        ]);

        $user = User::updateOrCreate(
            ['firebase_uid' => $request->uid],
            [
                'name'       => $request->display_name ?? explode('@', $request->email)[0],
                'email'      => $request->email,
                'password'   => bcrypt(Str::random(24)), // Random pw vì xài Firebase Auth
            ]
        );

        return response()->json([
            'success' => true,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}
