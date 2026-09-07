<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('services.index', ['categories' => Category::query()->where('status', true)->withCount('services')->orderBy('name')->get()]);
    }

    public function show(Category $category): View
    {
        return view('services.category', [
            'category' => $category->load(['services.artisanProfile.user']),
            'artisans' => $category->artisanProfiles()->with('user')->where('verification_status', 'verified')->paginate(12),
        ]);
    }
}
