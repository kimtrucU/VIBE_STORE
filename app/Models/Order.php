<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'status', 'payment_method',
        'payment_status', 'transfer_content', 'paid_at', 'sepay_data',
        'subtotal', 'shipping_fee', 'discount', 'coupon_code', 'total',
        'shipping_name', 'shipping_email', 'shipping_phone',
        'shipping_address', 'shipping_city', 'notes',
        'confirmed_at', 'processed_at', 'shipped_at',
        'delivered_at', 'completed_at', 'cancelled_at', 'returned_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'       => 'decimal:2',
            'shipping_fee'   => 'decimal:2',
            'discount'       => 'decimal:2',
            'total'          => 'decimal:2',
            'confirmed_at'   => 'datetime',
            'processed_at'   => 'datetime',
            'shipped_at'     => 'datetime',
            'delivered_at'   => 'datetime',
            'completed_at'   => 'datetime',
            'cancelled_at'   => 'datetime',
            'returned_at'    => 'datetime',
            'paid_at'        => 'datetime',
            'sepay_data'     => 'array',
        ];
    }

    public static $validStatuses = [
        'pending', 'confirmed', 'processing', 'shipped',
        'delivered', 'completed', 'cancelled', 'returned'
    ];

    public static $statusLabels = [
        'pending'    => 'Pending',
        'confirmed'  => 'Confirmed',
        'processing' => 'Processing',
        'shipped'    => 'Shipped',
        'delivered'  => 'Delivered',
        'completed'  => 'Completed',
        'cancelled'  => 'Cancelled',
        'returned'   => 'Returned',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'VIBE-' . str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('order_number', $number)->exists());
        return $number;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'warning',
            'confirmed'  => 'info',
            'processing' => 'primary',
            'shipped'    => 'info',
            'delivered'  => 'success',
            'completed'  => 'success',
            'cancelled'  => 'danger',
            'returned'   => 'secondary',
            default      => 'secondary',
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }
}
