<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:50'],
            'low' => ['nullable', 'boolean'],
        ]);

        $materials = Material::query()
            ->when($filters['q'] ?? null, function ($query, $term) {
                $query->where(function ($inner) use ($term) {
                    $like = '%' . $term . '%';

                    $inner->where('name', 'ilike', $like)
                        ->orWhere('code', 'ilike', $like);
                });
            })
            // Hanya bahan aktif yang dihitung menipis.
            ->when($filters['low'] ?? null, function ($query) {
                $query->where('is_active', true)
                    ->whereColumn('current_stock', '<=', 'minimum_stock');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $lowCount = Material::lowStockCount();

        return view('staff.materials.index', compact('materials', 'filters', 'lowCount'));
    }

    public function show(Material $material): View
    {
        $movements = $material->stockMovements()
            ->with('creator')
            ->latest()
            ->orderByDesc('id')
            ->paginate(15);

        return view('staff.materials.show', compact('material', 'movements'));
    }
}
