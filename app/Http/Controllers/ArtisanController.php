<?php

namespace App\Http\Controllers;

use App\Models\ArtisanProfile;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArtisanController extends Controller
{
    public function index(Request $request): View
    {
        $query = ArtisanProfile::query()
            ->with(['user', 'category'])
            ->withAvg('reviews', 'rating')
            ->where('verification_status', 'verified')
            ->whereHas('user', fn ($user) => $user->where('email', 'not like', 'artisan%@geoartisans.local'));

        $communeCoordinates = [
            'Bulengera' => [-0.1312, 29.2912],
            'Kimemi' => [-0.1268, 29.2861],
            'Mususa' => [-0.1395, 29.2735],
            'Vulamba' => [-0.1168, 29.3024],
        ];

        $query->when($request->filled('q'), function ($builder) use ($request) {
            $term = $request->string('q')->toString();
            $builder->where(function ($search) use ($term) {
                $search->where('profession', 'like', "%{$term}%")
                    ->orWhere('commune', 'like', "%{$term}%")
                    ->orWhere('quartier', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$term}%")->orWhere('first_name', 'like', "%{$term}%"))
                    ->orWhereHas('services', fn ($service) => $service->where('name', 'like', "%{$term}%"));
            });
        });
        $query->when($request->filled('category'), fn ($builder) => $builder->where('category_id', $request->integer('category')));
        $query->when($request->filled('commune'), fn ($builder) => $builder->where('commune', $request->string('commune')));
        $query->when($request->filled('availability'), fn ($builder) => $builder->where('availability', $request->string('availability')));

        $mapArtisans = (clone $query)->get()->map(function (ArtisanProfile $artisan) use ($communeCoordinates) {
            [$fallbackLatitude, $fallbackLongitude] = $communeCoordinates[$artisan->commune] ?? [-0.13, 29.28];

            return [
                'name' => trim($artisan->user->first_name . ' ' . $artisan->user->name),
                'profession' => $artisan->profession,
                'commune' => $artisan->commune,
                'quartier' => $artisan->quartier,
                'cell' => $artisan->user->cell,
                'address' => $artisan->address,
                'url' => route('artisans.show', $artisan),
                'latitude' => (float) ($artisan->latitude ?: $fallbackLatitude),
                'longitude' => (float) ($artisan->longitude ?: $fallbackLongitude),
            ];
        })->values();

        $artisans = $query->latest()->paginate(12)->withQueryString();

        return view('artisans.index', [
            'artisans' => $artisans,
            'categories' => Category::query()->where('status', true)->orderBy('name')->get(),
            'communes' => ['Bulengera', 'Kimemi', 'Mususa', 'Vulamba'],
            'mapArtisans' => $mapArtisans,
            'communeCoordinates' => $communeCoordinates,
        ]);
    }

    public function show(ArtisanProfile $artisan): View
    {
        abort_unless($artisan->verification_status === 'verified', 404);

        return view('artisans.show', [
            'artisan' => $artisan->load(['user', 'category', 'services', 'portfolioItems', 'reviews.user']),
        ]);
    }

    public function map(): View
    {
        $communeCoordinates = [
            'Bulengera' => [-0.1312, 29.2912],
            'Kimemi' => [-0.1268, 29.2861],
            'Mususa' => [-0.1395, 29.2735],
            'Vulamba' => [-0.1168, 29.3024],
        ];

        $artisans = ArtisanProfile::query()
            ->with(['user', 'category'])
            ->where('verification_status', 'verified')
            ->whereHas('user', fn ($user) => $user->where('email', 'not like', 'artisan%@geoartisans.local'))
            ->get()
            ->map(function (ArtisanProfile $artisan) use ($communeCoordinates) {
                [$fallbackLatitude, $fallbackLongitude] = $communeCoordinates[$artisan->commune] ?? [-0.13, 29.28];

                return [
                    'name' => trim($artisan->user->first_name . ' ' . $artisan->user->name),
                    'profession' => $artisan->profession,
                    'category' => $artisan->category?->name ?? 'Professionnel',
                    'commune' => $artisan->commune ?: 'Butembo',
                    'quartier' => $artisan->quartier,
                    'photo' => $artisan->coverImageUrl(),
                    'url' => route('artisans.show', $artisan),
                    'latitude' => (float) ($artisan->latitude ?: $fallbackLatitude),
                    'longitude' => (float) ($artisan->longitude ?: $fallbackLongitude),
                ];
            })
            ->values();

        return view('map', compact('artisans'));
    }
}
