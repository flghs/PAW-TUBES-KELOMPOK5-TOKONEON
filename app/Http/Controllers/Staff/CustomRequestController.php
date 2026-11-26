<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\CustomRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(CustomRequest::STATUS_LABELS))],
        ]);

        $customRequests = CustomRequest::with('user')
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('staff.custom-requests.index', [
            'customRequests' => $customRequests,
            'filters' => $filters,
        ]);
    }

    public function show(CustomRequest $customRequest): View
    {
        $customRequest->load('user');

        return view('staff.custom-requests.show', compact('customRequest'));
    }
}
