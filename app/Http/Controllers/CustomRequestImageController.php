<?php

namespace App\Http\Controllers;

use App\Models\CustomRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomRequestImageController extends Controller
{
    public function __invoke(Request $request, CustomRequest $customRequest): StreamedResponse
    {
        $user = $request->user();

        // Hanya pemilik request, staff, atau admin yang boleh membuka gambar.
        $allowed = $user->isAdmin()
            || $user->isStaff()
            || $customRequest->user_id === $user->id;

        abort_unless($allowed, 404);

        $path = $customRequest->reference_image_path;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }
}
