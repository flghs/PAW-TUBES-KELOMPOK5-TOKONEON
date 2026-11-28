<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialStockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// Hanya data bahan (nama, kode, satuan, batas minimum). Stok diubah lewat pencatatan stok.
// Bahan tidak dihapus, hanya dinonaktifkan, supaya riwayat stoknya tetap utuh.
class MaterialController extends Controller
{
    public function create(): View
    {
        return view('admin.materials.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeCode($request);

        $data = $request->validate(
            array_merge($this->rules(), [
                'initial_stock' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:1000000'],
            ]),
            $this->messages()
        );

        $material = DB::transaction(function () use ($request, $data) {
            $material = Material::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'unit' => $data['unit'],
                'minimum_stock' => $data['minimum_stock'],
                'is_active' => $request->boolean('is_active'),
            ]);

            // Stok awal dicatat sebagai stok masuk supaya muncul di riwayat.
            if ((float) ($data['initial_stock'] ?? 0) > 0) {
                $material->recordMovement(
                    MaterialStockMovement::TYPE_IN,
                    $data['initial_stock'],
                    'Stok awal',
                    $request->user()->id
                );
            }

            return $material;
        });

        return redirect()
            ->route('staff.materials.show', $material)
            ->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function edit(Material $material): View
    {
        return view('admin.materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material): RedirectResponse
    {
        $this->normalizeCode($request);

        $data = $request->validate($this->rules($material), $this->messages());

        $material->update([
            'name' => $data['name'],
            'code' => $data['code'],
            'unit' => $data['unit'],
            'minimum_stock' => $data['minimum_stock'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('staff.materials.show', $material)
            ->with('success', 'Data bahan berhasil diperbarui.');
    }

    // Kode bahan selalu huruf besar tanpa spasi di pinggir.
    private function normalizeCode(Request $request): void
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);
    }

    private function rules(?Material $material = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9][A-Z0-9\-_.]*$/',
                Rule::unique('materials', 'code')->ignore($material?->id),
            ],
            'unit' => ['required', 'string', 'max:20'],
            'minimum_stock' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:1000000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Isi nama bahan.',
            'code.required' => 'Isi kode bahan.',
            'code.regex' => 'Kode hanya boleh berisi huruf, angka, tanda - _ atau titik.',
            'code.unique' => 'Kode ini sudah dipakai bahan lain.',
            'unit.required' => 'Isi satuan (misalnya meter, pcs, lembar).',
            'minimum_stock.required' => 'Isi batas stok minimum (boleh 0).',
            'minimum_stock.numeric' => 'Batas minimum harus berupa angka.',
            'minimum_stock.decimal' => 'Batas minimum maksimal 2 angka di belakang koma.',
            'minimum_stock.min' => 'Batas minimum tidak boleh negatif.',
            'initial_stock.numeric' => 'Stok awal harus berupa angka.',
            'initial_stock.decimal' => 'Stok awal maksimal 2 angka di belakang koma.',
            'initial_stock.min' => 'Stok awal tidak boleh negatif.',
        ];
    }
}
