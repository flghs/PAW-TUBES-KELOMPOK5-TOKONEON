<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\View\View;

class CustomPageController extends Controller
{
    public function index(): View
    {
        // Hanya angka yang dipakai dari nomor WhatsApp di pengaturan.
        $number = preg_replace('/\D+/', '', (string) config('shop.whatsapp_number'));

        $whatsappUrl = $number !== ''
            ? 'https://wa.me/' . $number . '?text=' . rawurlencode($this->message())
            : null;

        $items = GalleryItem::published()->latest()->paginate(12);

        return view('custom', compact('whatsappUrl', 'items'));
    }

    private function message(): string
    {
        return "Halo Neon Store, saya ingin memesan neon sign custom.\n\n"
            . "Tulisan / desain: \n"
            . "Ukuran: \n"
            . "Warna: \n"
            . "Jumlah: \n"
            . "Deadline: ";
    }
}
