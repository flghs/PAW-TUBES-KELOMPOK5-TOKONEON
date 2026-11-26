<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const TYPE_READY = 'ready';
    public const TYPE_CUSTOM = 'custom';

    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID = 'paid';
    public const STATUS_IN_PRODUCTION = 'in_production';
    public const STATUS_READY_TO_SHIP = 'ready_to_ship';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_EXPIRED = 'expired';

    // Pesanan hanya dibuat oleh kode server kita (harga dihitung di backend),
    // jangan pernah mengisinya langsung dari $request->all().
    protected $fillable = [
        'order_number',
        'user_id',
        'handled_by',
        'type',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'subtotal',
        'shipping_cost',
        'total',
        'recipient_name',
        'phone',
        'shipping_address',
        'city',
        'province',
        'postal_code',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
