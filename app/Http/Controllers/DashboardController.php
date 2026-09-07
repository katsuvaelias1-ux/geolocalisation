<?php

namespace App\Http\Controllers;

use App\Models\ArtisanProfile;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function redirectByRole(Request $request): RedirectResponse
    {
        return match ($request->user()->role) {
            'artisan' => redirect()->route('artisan.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('dashboard.client'),
        };
    }

    public function client(Request $request): View
    {
        return view('dashboard.client', ['user' => $request->user()]);
    }

    public function artisan(Request $request): View
    {
        $profile = $request->user()->artisanProfile()->withCount(['services', 'reviews'])->first();

        return view('dashboard.artisan', compact('profile'));
    }

    public function admin(): View
    {
        return view('dashboard.admin', [
            'usersCount' => $this->countSafely('users'),
            'artisansCount' => ArtisanProfile::query()->count(),
            'categoriesCount' => Category::query()->count(),
            'servicesCount' => Service::query()->count(),
        ]);
    }

    private function countSafely(string $table): int
    {
        return match ($table) {
            'users' => \App\Models\User::query()->count(),
            default => 0,
        };
    }
}
