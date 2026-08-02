<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistApiController extends Controller
{
    private function getUser(Request $request): ?User
    {
        return User::where('firebase_uid', $request->firebase_uid)->first();
    }

    /**
     * Lấy danh sách productId trong wishlist (gọi sau khi đăng nhập).
     */
    public function index(Request $request)
    {
        $user = $this->getUser($request);

        if (!$user) {
            return response()->json([]);
        }

        $ids = Wishlist::where('user_id', $user->id)->pluck('product_id');

        return response()->json($ids);
    }

    /**
     * Thêm sản phẩm vào wishlist.
     */
    public function store(Request $request)
    {
        $request->validate(['productId' => 'required|integer|exists:products,id']);

        $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        Wishlist::firstOrCreate([
            'user_id'    => $user->id,
            'product_id' => $request->productId,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Xóa sản phẩm khỏi wishlist.
     */
    public function destroy(Request $request, int $productId)
    {
        $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['success' => true]);
    }
}
