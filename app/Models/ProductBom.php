<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBom extends Model
{
    protected $fillable = [
        'product_id',
        'material_id',
        'quantity_per_unit',
    ];

    protected function casts(): array
    {
        return [
            'quantity_per_unit' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
