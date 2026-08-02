<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user']);

        if ($approved = $request->get('approved')) {
            $query->where('is_approved', $approved === '1');
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        ActivityLog::log('review.approved', "Approved review #{$review->id}", $review);
        return back()->with('success', 'Đánh giá đã được duyệt!');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Đánh giá đã xóa!');
    }
}
