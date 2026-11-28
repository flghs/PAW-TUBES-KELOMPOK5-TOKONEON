<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Material extends Model
{
    // Stok tidak boleh diubah langsung dari form. Perubahan stok dilakukan
    // lewat recordMovement() yang sekaligus mencatat riwayatnya.
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

    // Stok sama dengan atau di bawah batas minimum.
    public function isLowStock(): bool
    {
        return (float) $this->current_stock <= (float) $this->minimum_stock;
    }

    // Jumlah bahan aktif yang stoknya menipis (untuk penanda di menu samping).
    public static function lowStockCount(): int
    {
        return static::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->count();
    }

    // Menampilkan jumlah tanpa nol di belakang koma: 12.50 jadi "12,5", 25.00 jadi "25".
    public static function formatQuantity(string|int|float|null $value): string
    {
        $text = number_format((float) $value, 2, ',', '.');

        return rtrim(rtrim($text, '0'), ',');
    }

    // Satu-satunya cara mengubah stok bahan. Dilakukan di dalam transaksi dengan baris
    // bahan terkunci, jadi dua pencatatan bersamaan tidak saling menimpa.
    //
    // in         : stok bertambah sebesar $quantity.
    // out        : stok berkurang sebesar $quantity (tidak boleh melebihi stok).
    // adjustment : $quantity adalah stok hasil hitung fisik; yang dicatat adalah selisihnya.
    //
    // Kalau tidak valid, dilempar ValidationException supaya form kembali dengan pesan.
    public function recordMovement(string $type, string|int|float $quantity, ?string $note, ?int $userId): MaterialStockMovement
    {
        return DB::transaction(function () use ($type, $quantity, $note, $userId) {
            $material = self::whereKey($this->id)->lockForUpdate()->firstOrFail();

            // Dihitung dalam satuan seperseratus supaya tidak ada selisih pembulatan desimal.
            $current = (int) round((float) $material->current_stock * 100);
            $amount = (int) round((float) $quantity * 100);

            if ($type === MaterialStockMovement::TYPE_ADJUSTMENT) {
                $after = $amount;
                $change = $after - $current;

                if ($change === 0) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Stok hasil hitung sama dengan stok di sistem, tidak ada yang perlu dikoreksi.',
                    ]);
                }
            } elseif ($type === MaterialStockMovement::TYPE_IN || $type === MaterialStockMovement::TYPE_OUT) {
                if ($amount < 1) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Jumlah harus lebih dari 0.',
                    ]);
                }

                if ($type === MaterialStockMovement::TYPE_OUT && $amount > $current) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Stok tidak cukup. Stok sekarang '
                            . self::formatQuantity($current / 100) . ' ' . $material->unit . '.',
                    ]);
                }

                $after = $type === MaterialStockMovement::TYPE_IN ? $current + $amount : $current - $amount;
                $change = $amount;
            } else {
                throw new \InvalidArgumentException('Jenis pergerakan stok tidak dikenal.');
            }

            $material->forceFill(['current_stock' => number_format($after / 100, 2, '.', '')])->save();

            return $material->stockMovements()->create([
                'created_by' => $userId,
                'type' => $type,
                'quantity' => number_format($change / 100, 2, '.', ''),
                'stock_after' => number_format($after / 100, 2, '.', ''),
                'note' => $note,
            ]);
        });
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(MaterialStockMovement::class);
    }
}
