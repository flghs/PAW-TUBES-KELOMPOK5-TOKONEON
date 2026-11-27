<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\OrderAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderProofController extends Controller
{
    public function __invoke(Request $request, Order $order): StreamedResponse
    {
        $user = $request->user();

        // Hanya staff, admin, atau pembeli yang browsernya sudah terverifikasi untuk pesanan ini.
        $allowed = ($user && ($user->isAdmin() || $user->isStaff()))
            || OrderAccess::has($request, $order);

        abort_unless($allowed, 404);

        $path = $order->payment_proof_path;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }
}
