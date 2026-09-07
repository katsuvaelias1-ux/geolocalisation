<?php

namespace App\Http\Controllers;

use App\Models\ArtisanProfile;
use App\Models\Category;
use App\Models\PortfolioItem;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ArtisanWorkspaceController extends Controller
{
    public function profile(Request $request): View
    {
        return view('artisan.profile', [
            'profile' => $request->user()->artisanProfile,
            'categories' => Category::query()->where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'profession' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:3000'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'commune' => ['required', 'in:Bulengera,Kimemi,Mususa,Vulamba'],
            'quartier' => ['nullable', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'availability' => ['required', 'in:available,unavailable'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ]);

        $profile = $request->user()->artisanProfile;
        abort_unless($profile, 404);
        if ($request->hasFile('cover_image')) {
            if ($profile->cover_image) {
                Storage::disk('public')->delete($profile->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('artisan-work', 'public');
        }
        $profile->update($data);

        return back()->with('success', 'Votre profil a été mis à jour.');
    }

    public function services(Request $request): View
    {
        return view('artisan.services', ['profile' => $request->user()->artisanProfile()->with('services.category')->first()]);
    }

    public function storeService(Request $request): RedirectResponse
    {
        $profile = $request->user()->artisanProfile;
        abort_unless($profile, 404);
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'gte:price_min'],
        ]);
        $profile->services()->create($data);

        return back()->with('success', 'Service ajouté.');
    }

    public function destroyService(Request $request, Service $service): RedirectResponse
    {
        abort_unless($service->artisan_id === $request->user()->artisanProfile?->id, 403);
        $service->delete();

        return back()->with('success', 'Service supprimé.');
    }

    public function portfolio(Request $request): View
    {
        return view('artisan.portfolio', ['profile' => $request->user()->artisanProfile()->with('portfolioItems')->first()]);
    }

    public function storePortfolio(Request $request): RedirectResponse
    {
        $profile = $request->user()->artisanProfile;
        abort_unless($profile, 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['required', 'image', 'max:4096'],
        ]);
        $data['image'] = $request->file('image')->store('portfolio', 'public');
        $profile->portfolioItems()->create($data);

        return back()->with('success', 'Réalisation ajoutée.');
    }

    public function destroyPortfolio(Request $request, PortfolioItem $portfolioItem): RedirectResponse
    {
        abort_unless($portfolioItem->artisan_id === $request->user()->artisanProfile?->id, 403);
        $portfolioItem->delete();

        return back()->with('success', 'Réalisation supprimée.');
    }
}
