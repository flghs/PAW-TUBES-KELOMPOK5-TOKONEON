<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderProofController extends Controller
{
    public function __invoke(Request $request, Order $order): StreamedResponse
    {
        $user = $request->user();

        // Hanya pemilik pesanan, staff, atau admin yang boleh membuka bukti bayar.
        $allowed = $user->isAdmin()
            || $user->isStaff()
            || $order->user_id === $user->id;

        abort_unless($allowed, 404);

        $path = $order->payment_proof_path;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }
}
