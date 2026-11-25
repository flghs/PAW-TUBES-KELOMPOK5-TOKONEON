<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    // Stok tidak boleh diubah langsung dari form. Perubahan stok dilakukan
    // lewat kode server yang sekaligus mencatat riwayatnya.
    protected $fillable = [
        'name',
        'code',
        'unit',
        'minimum_stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function isLowStock(): bool
    {
        return (float) $this->current_stock <= (float) $this->minimum_stock;
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(MaterialStockMovement::class);
    }

    public function boms(): HasMany
    {
        return $this->hasMany(ProductBom::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(ProductionMaterialUsage::class);
    }
}
