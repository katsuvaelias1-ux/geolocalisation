<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], $this->messages());

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Ces identifiants sont incorrects.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.entry'));
    }

    public function showRegister(): View
    {
        return view('auth.register', [
            'categories' => \App\Models\Category::query()->where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'full_address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'commune' => ['required', 'in:Bulengera,Kimemi,Mususa,Vulamba'],
            'quartier' => ['required', 'string', 'max:120'],
            'cell' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'in:client,artisan'],
            'profile_photo' => [Rule::requiredIf($request->input('role') === 'artisan'), 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'profession' => [Rule::requiredIf($request->input('role') === 'artisan'), 'nullable', 'string', 'max:160'],
            'cover_image' => [Rule::requiredIf($request->input('role') === 'artisan'), 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ], $this->messages());

        $role = $data['role'];
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('artisan-work', 'public');
        }
        $user = User::create(Arr::only($data, [
            'first_name', 'name', 'phone', 'full_address', 'city', 'commune',
            'quartier', 'cell', 'email', 'password', 'role', 'profile_photo',
        ]));

        if ($role === 'artisan') {
            $user->artisanProfile()->create([
                'slug' => Str::slug($user->first_name . '-' . $user->name) . '-' . $user->id,
                'category_id' => $data['category_id'] ?? null,
                'profession' => $data['profession'],
                'commune' => $user->commune,
                'quartier' => $user->quartier,
                'address' => $user->full_address,
                'cover_image' => $data['cover_image'],
                'verification_status' => 'pending',
            ]);
        }
        Auth::login($user);
        $request->session()->regenerate();

        return $user->role === 'artisan'
            ? redirect()->route('artisan.profile')->with('success', 'Votre compte est créé. Complétez votre profil artisan avant sa validation.')
            : redirect()->route('dashboard.client')->with('success', 'Bienvenue sur GeoArtisans Butembo.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function messages(): array
    {
        return [
            'required' => 'Ce champ est obligatoire.',
            'email' => 'Saisissez une adresse e-mail valide.',
            'unique' => 'Cette adresse e-mail est déjà utilisée.',
            'confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'in' => 'Le type de compte sélectionné est invalide.',
        ];
    }
}
