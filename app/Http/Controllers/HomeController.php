<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount(['products as active_products_count' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $latestProducts = Product::active()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('categories', 'latestProducts'));
    }
}
