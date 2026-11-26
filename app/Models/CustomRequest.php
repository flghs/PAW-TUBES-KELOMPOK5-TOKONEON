<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomRequest extends Model
{
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_IN_DISCUSSION = 'in_discussion';
    public const STATUS_QUOTED = 'quoted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        self::STATUS_SUBMITTED => 'Dikirim',
        self::STATUS_IN_DISCUSSION => 'Dalam diskusi',
        self::STATUS_QUOTED => 'Penawaran dikirim',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    public const COLORS = [
        'Putih hangat',
        'Putih dingin',
        'Merah',
        'Pink',
        'Biru',
        'Hijau',
        'Kuning',
        'Oranye',
        'Ungu',
        'Multi-warna (RGB)',
        'Lainnya (jelaskan di deskripsi)',
    ];

    // Nomor, status, dan pemilik request hanya diisi oleh kode server kita,
    // jangan pernah mengisinya langsung dari $request->all().
    protected $fillable = [
        'user_id',
        'request_number',
        'neon_text',
        'description',
        'reference_image_path',
        'width_cm',
        'height_cm',
        'color',
        'quantity',
        'deadline_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'deadline_date' => 'date',
        ];
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
