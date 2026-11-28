<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialStockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Mencatat stok masuk, stok keluar, dan koreksi stok.
class MaterialStockController extends Controller
{
    public function store(Request $request, Material $material): RedirectResponse
    {
        if (! $material->is_active) {
            return back()->with('error', 'Bahan ini nonaktif, stoknya tidak bisa diubah.');
        }

        $data = $request->validate([
            'type' => ['required', Rule::in([
                MaterialStockMovement::TYPE_IN,
                MaterialStockMovement::TYPE_OUT,
                MaterialStockMovement::TYPE_ADJUSTMENT,
            ])],
            'quantity' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:1000000'],
            // Stok keluar dan koreksi wajib punya alasan.
            'note' => ['required_if:type,out,adjustment', 'nullable', 'string', 'max:255'],
        ], [
            'type.required' => 'Pilih jenis pencatatan.',
            'type.in' => 'Jenis pencatatan tidak valid.',
            'quantity.required' => 'Isi jumlah.',
            'quantity.numeric' => 'Jumlah harus berupa angka.',
            'quantity.decimal' => 'Jumlah maksimal 2 angka di belakang koma.',
            'quantity.min' => 'Jumlah tidak boleh negatif.',
            'quantity.max' => 'Jumlah terlalu besar.',
            'note.required_if' => 'Isi catatan (alasan stok keluar atau koreksi).',
        ]);

        $movement = $material->recordMovement(
            $data['type'],
            $data['quantity'],
            $data['note'] ?? null,
            $request->user()->id
        );

        $label = match ($data['type']) {
            MaterialStockMovement::TYPE_IN => 'Stok masuk dicatat.',
            MaterialStockMovement::TYPE_OUT => 'Stok keluar dicatat.',
            default => 'Stok dikoreksi.',
        };

        $message = $label . ' Stok sekarang ' . Material::formatQuantity($movement->stock_after) . ' ' . $material->unit . '.';

        if ((float) $movement->stock_after <= (float) $material->minimum_stock) {
            $message .= ' Stok menipis.';
        }

        return back()->with('success', $message);
    }
}
