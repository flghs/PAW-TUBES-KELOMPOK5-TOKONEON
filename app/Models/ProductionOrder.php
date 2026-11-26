<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    public const TYPE_CUSTOM_ORDER = 'custom_order';
    public const TYPE_STOCK = 'stock';

    public const STATUS_PLANNED = 'planned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'production_number',
        'type',
        'order_id',
        'product_id',
        'assigned_to',
        'quantity_target',
        'quantity_completed',
        'status',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Bahan yang dipakai di produksi ini: baris riwayat stok bahan bertipe "out".
    public function materialMovements(): HasMany
    {
        return $this->hasMany(MaterialStockMovement::class);
    }
}
