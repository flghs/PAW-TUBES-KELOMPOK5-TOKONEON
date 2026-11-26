<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryItemController extends Controller
{
    // Tempat penyimpanan foto. Saat deployment nanti, ganti ke penyimpanan online.
    private const IMAGE_DISK = 'public';

    public function index(): View
    {
        $items = GalleryItem::latest()->paginate(12);

        return view('admin.gallery.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['image_path'] = $request->file('image')->store('gallery', self::IMAGE_DISK);

        GalleryItem::create($data);

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Foto galeri berhasil ditambahkan.');
    }

    public function edit(GalleryItem $gallery): View
    {
        return view('admin.gallery.edit', compact('gallery'));
    }

    public function update(Request $request, GalleryItem $gallery): RedirectResponse
    {
        $data = $this->validated($request, false);
        $oldImage = $gallery->image_path;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('gallery', self::IMAGE_DISK);
        }

        $gallery->update($data);

        // Foto lama dihapus setelah foto baru berhasil tersimpan.
        if ($request->hasFile('image')) {
            Storage::disk(self::IMAGE_DISK)->delete($oldImage);
        }

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Foto galeri berhasil diperbarui.');
    }

    public function destroy(GalleryItem $gallery): RedirectResponse
    {
        Storage::disk(self::IMAGE_DISK)->delete($gallery->image_path);

        $gallery->delete();

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Foto galeri berhasil dihapus.');
    }

    private function validated(Request $request, bool $imageRequired): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'title.required' => 'Isi judul foto.',
            'title.max' => 'Judul maksimal 120 karakter.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'image.required' => 'Pilih foto yang akan diunggah.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat jpg, png, atau webp.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        unset($data['image']);

        return $data;
    }
}
