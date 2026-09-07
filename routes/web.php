<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\ArtisanWorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::view('/', 'welcome')->name('home');
    Route::get('/artisans', [ArtisanController::class, 'index'])->name('artisans.index');
    Route::get('/artisans/{artisan:slug}', [ArtisanController::class, 'show'])->name('artisans.show');
    Route::get('/services', [CategoryController::class, 'index'])->name('services.index');
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/carte', [ArtisanController::class, 'map'])->name('map');
    Route::view('/comment-ca-marche', 'how-it-works')->name('how-it-works');
    Route::view('/a-propos', 'about')->name('about');
    Route::get('/contact', [ContactController::class, 'create'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::post('/artisans/{artisan}/favorite', [InteractionController::class, 'favorite'])->name('artisans.favorite');
    Route::post('/artisans/{artisan}/reviews', [InteractionController::class, 'review'])->name('artisans.reviews.store');
    Route::post('/artisans/{artisan}/messages', [InteractionController::class, 'sendMessage'])->name('artisans.messages.store');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'redirectByRole'])->name('dashboard.entry');
    Route::get('/dashboard/client', [DashboardController::class, 'client'])->middleware('role:client')->name('dashboard.client');
    Route::get('/artisan/dashboard', [DashboardController::class, 'artisan'])->middleware('role:artisan')->name('artisan.dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->middleware('role:admin')->name('admin.dashboard');
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/artisans', [AdminController::class, 'artisans'])->name('artisans');
        Route::patch('/artisans/{artisan}/status', [AdminController::class, 'updateArtisanStatus'])->name('artisans.status');
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
        Route::patch('/categories/{category}/toggle', [AdminController::class, 'toggleCategory'])->name('categories.toggle');
    });
    Route::middleware('role:artisan')->prefix('artisan')->name('artisan.')->group(function () {
        Route::get('/profile', [ArtisanWorkspaceController::class, 'profile'])->name('profile');
        Route::put('/profile', [ArtisanWorkspaceController::class, 'updateProfile'])->name('profile.update');
        Route::get('/services', [ArtisanWorkspaceController::class, 'services'])->name('services');
        Route::post('/services', [ArtisanWorkspaceController::class, 'storeService'])->name('services.store');
        Route::delete('/services/{service}', [ArtisanWorkspaceController::class, 'destroyService'])->name('services.destroy');
        Route::get('/portfolio', [ArtisanWorkspaceController::class, 'portfolio'])->name('portfolio');
        Route::post('/portfolio', [ArtisanWorkspaceController::class, 'storePortfolio'])->name('portfolio.store');
        Route::delete('/portfolio/{portfolioItem}', [ArtisanWorkspaceController::class, 'destroyPortfolio'])->name('portfolio.destroy');
        Route::get('/messages', [InteractionController::class, 'artisanMessages'])->name('messages');
        Route::patch('/messages/{message}/read', [InteractionController::class, 'markMessageRead'])->name('messages.read');
    });
    Route::middleware('role:client')->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/favoris', [InteractionController::class, 'favorites'])->name('favorites');
        Route::get('/messages', [InteractionController::class, 'clientMessages'])->name('messages');
    });
});
