<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Cart;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        // Share cart count globally with all views
        View::composer('*', function ($view) {
            $cartCount = 0;
            if (request() && request()->hasSession()) {
                try {
                    if (auth()->check()) {
                        $cart = Cart::where('user_id', auth()->id())->first();
                    } else {
                        $sessionId = session()->getId();
                        $cart = Cart::where('session_id', $sessionId)->first();
                    }
                    $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
                } catch (\Exception $e) {
                    $cartCount = 0;
                }
            }
            $view->with('cartCount', $cartCount);
        });
    }
}
