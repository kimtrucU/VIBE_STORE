<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Merge guest cart into user cart
            $this->mergeGuestCart();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('info', 'You have been logged out.');
    }

    private function mergeGuestCart(): void
    {
        // If there's a guest cart, merge it into the user's cart
        $sessionId = session()->getId();
        $guestCart = \App\Models\Cart::where('session_id', $sessionId)->first();

        if ($guestCart && $guestCart->items->count() > 0) {
            $userCart = \App\Models\Cart::firstOrCreate(['user_id' => Auth::id()]);
            foreach ($guestCart->items as $item) {
                $existing = $userCart->items()->where('product_id', $item->product_id)->where('size', $item->size)->first();
                if ($existing) {
                    $existing->increment('quantity', $item->quantity);
                } else {
                    $userCart->items()->create([
                        'product_id' => $item->product_id,
                        'size'       => $item->size,
                        'quantity'   => $item->quantity,
                    ]);
                }
            }
            $guestCart->delete();
        }
    }
}
