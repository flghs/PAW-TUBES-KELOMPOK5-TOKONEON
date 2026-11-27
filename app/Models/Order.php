<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    public const TYPE_READY = 'ready';
    public const TYPE_CUSTOM = 'custom';

    public const TYPE_LABELS = [
        self::TYPE_READY => 'Produk jadi',
        self::TYPE_CUSTOM => 'Custom',
    ];

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

    // Alur status: "dari status ini boleh pindah ke status mana saja".
    // Status "Dibayar" tidak diubah lewat daftar ini, tapi lewat persetujuan pembayaran.
    public const NEXT_STATUSES = [
        self::STATUS_PAID => [self::STATUS_IN_PRODUCTION, self::STATUS_READY_TO_SHIP],
        self::STATUS_IN_PRODUCTION => [self::STATUS_READY_TO_SHIP],
        self::STATUS_READY_TO_SHIP => [self::STATUS_SHIPPED],
        self::STATUS_SHIPPED => [self::STATUS_COMPLETED],
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

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function paymentLabel(): string
    {
        return self::PAYMENT_LABELS[$this->payment_status] ?? $this->payment_status;
    }

    // Status tujuan yang boleh dipilih staff atau admin dari status sekarang.
    public function nextStatuses(): array
    {
        return self::NEXT_STATUSES[$this->status] ?? [];
    }

    // Pesanan masih menunggu pembayaran: pelanggan boleh mengunggah bukti bayar atau membatalkan.
    public function isAwaitingPayment(): bool
    {
        return $this->status === self::STATUS_PENDING_PAYMENT
            && in_array($this->payment_status, [self::PAYMENT_UNPAID, self::PAYMENT_REJECTED], true);
    }

    // Admin boleh membatalkan selama pesanan belum dikirim.
    public function canBeCancelledByAdmin(): bool
    {
        return ! in_array($this->status, [
            self::STATUS_SHIPPED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ], true);
    }

    // Membatalkan pesanan dan mengembalikan stok produk jadi.
    // Dipakai bersama oleh pelanggan (batalkan sendiri) dan admin.
    public function cancelAndRestoreStock(?int $handledBy = null): void
    {
        DB::transaction(function () use ($handledBy) {
            foreach ($this->items as $item) {
                // Item custom tidak punya product_id, jadi tidak ada stok yang dikembalikan.
                if ($item->product_id) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }

            $data = ['status' => self::STATUS_CANCELLED];

            if ($handledBy) {
                $data['handled_by'] = $handledBy;
            }

            $this->update($data);
        });
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
