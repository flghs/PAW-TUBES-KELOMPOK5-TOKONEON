<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const TYPE_READY = 'ready';
    public const TYPE_CUSTOM = 'custom';

    // Tahap pengerjaan pesanan.
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID = 'paid';
    public const STATUS_IN_PRODUCTION = 'in_production';
    public const STATUS_READY_TO_SHIP = 'ready_to_ship';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        self::STATUS_PENDING_PAYMENT => 'Menunggu pembayaran',
        self::STATUS_PAID => 'Dibayar',
        self::STATUS_IN_PRODUCTION => 'Diproduksi',
        self::STATUS_READY_TO_SHIP => 'Siap dikirim',
        self::STATUS_SHIPPED => 'Dikirim',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    // Keadaan pembayaran.
    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PENDING_REVIEW = 'pending_review';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REJECTED = 'rejected';

    public const PAYMENT_LABELS = [
        self::PAYMENT_UNPAID => 'Belum dibayar',
        self::PAYMENT_PENDING_REVIEW => 'Bukti bayar diperiksa',
        self::PAYMENT_PAID => 'Lunas',
        self::PAYMENT_REJECTED => 'Bukti ditolak',
    ];

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
        'payment_proof_path',
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

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function paymentLabel(): string
    {
        return self::PAYMENT_LABELS[$this->payment_status] ?? $this->payment_status;
    }

    // Pesanan masih menunggu pembayaran: pelanggan boleh mengunggah bukti bayar atau membatalkan.
    public function isAwaitingPayment(): bool
    {
        return $this->status === self::STATUS_PENDING_PAYMENT
            && in_array($this->payment_status, [self::PAYMENT_UNPAID, self::PAYMENT_REJECTED], true);
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
