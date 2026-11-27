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
        'handled_by',
        'type',
        'status',
        'payment_status',
        'payment_method',
        'payment_proof_path',
        'stock_deducted',
        'stock_short',
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
            'stock_deducted' => 'boolean',
            'stock_short' => 'boolean',
        ];
    }

    // Nomor telepon dibandingkan dalam bentuk angka saja dengan awalan 62,
    // jadi "0812-3456-789", "+62 812 3456 789", dan "6281234567890" dianggap sama.
    public static function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '620')) {
            return '62' . substr($digits, 3);
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        return $digits;
    }

    // Nomor pembeli untuk tautan wa.me.
    public function whatsappPhone(): string
    {
        return self::normalizePhone($this->phone);
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
        // Stok kurang: kalau pembeli memilih menunggu, pesanan hanya bisa lanjut ke produksi.
        // (Kalau pembeli memilih uang kembali, staff membatalkan pesanan.)
        if ($this->stock_short && $this->status === self::STATUS_PAID) {
            return [self::STATUS_IN_PRODUCTION];
        }

        return self::NEXT_STATUSES[$this->status] ?? [];
    }

    // Pesanan masih menunggu pembayaran: pembeli boleh mengunggah bukti bayar atau membatalkan.
    public function isAwaitingPayment(): bool
    {
        return $this->status === self::STATUS_PENDING_PAYMENT
            && in_array($this->payment_status, [self::PAYMENT_UNPAID, self::PAYMENT_REJECTED], true);
    }

    // Staff dan admin boleh membatalkan selama pesanan belum dikirim.
    public function canBeCancelled(): bool
    {
        return ! in_array($this->status, [
            self::STATUS_SHIPPED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ], true);
    }

    // Membatalkan pesanan. Stok hanya dikembalikan kalau memang sudah dikurangi.
    // $onlyIfAwaitingPayment = true dipakai pembeli (hanya boleh batal sebelum bayar).
    // Mengembalikan false kalau pesanan sudah tidak bisa dibatalkan.
    public function cancel(?int $handledBy = null, bool $onlyIfAwaitingPayment = false): bool
    {
        return DB::transaction(function () use ($handledBy, $onlyIfAwaitingPayment) {
            // Kunci pesanan dan baca ulang statusnya, kalau-kalau baru saja berubah.
            $order = self::whereKey($this->id)->lockForUpdate()->with('items')->firstOrFail();

            $allowed = $onlyIfAwaitingPayment
                ? $order->isAwaitingPayment()
                : $order->canBeCancelled();

            if (! $allowed) {
                return false;
            }

            if ($order->stock_deducted) {
                foreach ($order->items as $item) {
                    // Item custom tidak punya product_id, jadi tidak ada stok yang dikembalikan.
                    if ($item->product_id) {
                        Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }

            $data = [
                'status' => self::STATUS_CANCELLED,
                'stock_deducted' => false,
                'stock_short' => false,
            ];

            if ($handledBy) {
                $data['handled_by'] = $handledBy;
            }

            $order->update($data);

            return true;
        });
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
