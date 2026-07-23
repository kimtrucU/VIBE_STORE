<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_order',
        'max_discount', 'usage_limit', 'used_count',
        'starts_at', 'expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value'       => 'decimal:2',
            'min_order'   => 'decimal:2',
            'max_discount'=> 'decimal:2',
            'starts_at'   => 'date',
            'expires_at'  => 'date',
            'is_active'   => 'boolean',
        ];
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->min_order) return 0;
        if ($this->type === 'percent') {
            $discount = $subtotal * ($this->value / 100);
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
            return $discount;
        }
        return min($this->value, $subtotal);
    }
}
