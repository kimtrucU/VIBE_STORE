<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'session_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn($item) => $item->product->price * $item->quantity);
    }

    public function getShippingFeeAttribute(): float
    {
        return $this->subtotal >= 1000000 ? 0 : 30000;
    }

    public function getGrandTotalAttribute(): float
    {
        return $this->subtotal + $this->shipping_fee;
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
