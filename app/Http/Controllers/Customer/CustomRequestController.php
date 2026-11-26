<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomRequestController extends Controller
{
    // Gambar referensi disimpan di disk privat (tidak bisa dibuka lewat alamat publik).
    private const FILE_DISK = 'local';

    public function index(Request $request): View
    {
        $customRequests = $request->user()
            ->customRequests()
            ->latest()
            ->paginate(10);

        return view('customer.custom-requests.index', compact('customRequests'));
    }

    public function create(): View
    {
        return view('customer.custom-requests.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'neon_text' => ['nullable', 'string', 'max:100', 'required_without:description'],
            'description' => ['nullable', 'string', 'max:2000', 'required_without:neon_text'],
            'color' => ['required', Rule::in(CustomRequest::COLORS)],
            'width_cm' => ['required', 'integer', 'min:10', 'max:500'],
            'height_cm' => ['required', 'integer', 'min:10', 'max:500'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'deadline_date' => ['nullable', 'date', 'after:today'],
            'reference_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'neon_text.required_without' => 'Isi tulisan neon, atau jelaskan bentuknya di deskripsi.',
            'description.required_without' => 'Isi tulisan neon, atau jelaskan bentuknya di deskripsi.',
            'neon_text.max' => 'Tulisan neon maksimal 100 karakter.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
            'color.required' => 'Pilih warna.',
            'color.in' => 'Warna yang dipilih tidak tersedia.',
            'width_cm.required' => 'Isi lebar dalam cm.',
            'width_cm.integer' => 'Lebar harus berupa angka bulat.',
            'width_cm.min' => 'Lebar minimal 10 cm.',
            'width_cm.max' => 'Lebar maksimal 500 cm.',
            'height_cm.required' => 'Isi tinggi dalam cm.',
            'height_cm.integer' => 'Tinggi harus berupa angka bulat.',
            'height_cm.min' => 'Tinggi minimal 10 cm.',
            'height_cm.max' => 'Tinggi maksimal 500 cm.',
            'quantity.required' => 'Isi jumlah pesanan.',
            'quantity.integer' => 'Jumlah harus berupa angka bulat.',
            'quantity.min' => 'Jumlah minimal 1.',
            'quantity.max' => 'Jumlah maksimal 1000.',
            'deadline_date.date' => 'Tanggal tidak valid.',
            'deadline_date.after' => 'Deadline harus setelah hari ini.',
            'reference_image.image' => 'File harus berupa gambar.',
            'reference_image.mimes' => 'Gambar harus berformat jpg, png, atau webp.',
            'reference_image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $path = null;

        if ($request->hasFile('reference_image')) {
            $path = $request->file('reference_image')->store('custom-requests', self::FILE_DISK);
        }

        $customRequest = CustomRequest::create([
            'user_id' => $request->user()->id,
            'request_number' => $this->generateRequestNumber(),
            'neon_text' => $data['neon_text'] ?? null,
            'description' => $data['description'] ?? null,
            'reference_image_path' => $path,
            'width_cm' => $data['width_cm'],
            'height_cm' => $data['height_cm'],
            'color' => $data['color'],
            'quantity' => $data['quantity'],
            'deadline_date' => $data['deadline_date'] ?? null,
            'status' => CustomRequest::STATUS_SUBMITTED,
        ]);

        return redirect()
            ->route('customer.custom-requests.show', $customRequest)
            ->with('success', 'Request custom berhasil dikirim.');
    }

    public function show(Request $request, CustomRequest $customRequest): View
    {
        // Pelanggan hanya boleh melihat request miliknya sendiri.
        abort_unless($customRequest->user_id === $request->user()->id, 404);

        return view('customer.custom-requests.show', compact('customRequest'));
    }

    private function generateRequestNumber(): string
    {
        do {
            $number = 'CR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (CustomRequest::where('request_number', $number)->exists());

        return $number;
    }
}
