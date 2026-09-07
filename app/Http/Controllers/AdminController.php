<?php

namespace App\Http\Controllers;

use App\Models\ArtisanProfile;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function artisans(): View
    {
        return view('admin.artisans', ['artisans' => ArtisanProfile::query()->with('user', 'category')->latest()->paginate(20)]);
    }

    public function updateArtisanStatus(Request $request, ArtisanProfile $artisan): RedirectResponse
    {
        $data = $request->validate(['verification_status' => ['required', 'in:pending,verified,rejected']]);
        $artisan->update($data);

        return back()->with('success', 'Statut de l’artisan mis à jour.');
    }

    public function categories(): View
    {
        return view('admin.categories', ['categories' => Category::query()->withCount('artisanProfiles', 'services')->orderBy('name')->paginate(20)]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        Category::create($request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'slug' => ['required', 'alpha_dash', 'max:120', 'unique:categories,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:80'],
        ]));

        return back()->with('success', 'Catégorie créée.');
    }

    public function toggleCategory(Category $category): RedirectResponse
    {
        $category->update(['status' => ! $category->status]);

        return back()->with('success', 'Statut de la catégorie mis à jour.');
    }
}
