@extends('layouts.app')

@section('title', 'Créer un compte | GeoArtisans')

@section('content')
<section class="auth-shell registration-shell">
    <div class="auth-aside">
        <span class="eyebrow">GEOARTISANS / INSCRIPTION</span>
        <h1>Un compte pour une expérience locale.</h1>
        <p>Vos informations permettent de personnaliser les résultats et de faciliter la mise en relation dans votre zone.</p>
        <div class="registration-note"><strong>Confidentialité</strong><span>Votre adresse sert à la localisation déclarée et n’est jamais localisée à partir de votre e-mail ou de votre numéro.</span></div>
    </div>
    <div class="form-panel">
        <span class="eyebrow">Créer un compte</span>
        <h2>Votre point de départ.</h2>
        <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" class="stack-form" id="registration-form">
            @csrf
            <div class="form-grid">
                <label>Prénom
                    <input name="first_name" value="{{ old('first_name') }}" required>
                    @error('first_name')<small class="field-error">{{ $message }}</small>@enderror
                </label>
                <label>Nom
                    <input name="name" value="{{ old('name') }}" required>
                    @error('name')<small class="field-error">{{ $message }}</small>@enderror
                </label>
            </div>
            <div class="form-grid">
                <label>Téléphone <span class="muted">(facultatif)</span>
                    <input name="phone" value="{{ old('phone') }}">
                    @error('phone')<small class="field-error">{{ $message }}</small>@enderror
                </label>
                <label>E-mail
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<small class="field-error">{{ $message }}</small>@enderror
                </label>
            </div>
            <label>Adresse complète
                <input name="full_address" value="{{ old('full_address') }}" placeholder="Avenue, numéro ou repère" required>
                @error('full_address')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <div class="form-grid">
                <label>Ville de provenance
                    <input name="city" value="{{ old('city') }}" placeholder="Butembo" required>
                    @error('city')<small class="field-error">{{ $message }}</small>@enderror
                </label>
                <label>Commune
                    <select name="commune" required>
                        <option value="">Choisir une commune</option>
                        @foreach (['Bulengera', 'Kimemi', 'Mususa', 'Vulamba'] as $commune)
                            <option value="{{ $commune }}" @selected(old('commune') === $commune)>{{ $commune }}</option>
                        @endforeach
                    </select>
                    @error('commune')<small class="field-error">{{ $message }}</small>@enderror
                </label>
            </div>
            <div class="form-grid">
                <label>Quartier
                    <input name="quartier" value="{{ old('quartier') }}" required>
                    @error('quartier')<small class="field-error">{{ $message }}</small>@enderror
                </label>
                <label>Cellule
                    <input name="cell" value="{{ old('cell') }}" required>
                    @error('cell')<small class="field-error">{{ $message }}</small>@enderror
                </label>
            </div>
            <label class="photo-picker">Photo de profil <span class="muted">(obligatoire pour un artisan · JPG, PNG ou WebP · 4 Mo max.)</span>
                <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" data-artisan-required>
                @error('profile_photo')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <div id="artisan-registration-fields" hidden>
                <div class="form-grid">
                    <label>Métier
                        <input name="profession" value="{{ old('profession') }}" placeholder="Ex. Électromécanicien" data-artisan-required>
                        @error('profession')<small class="field-error">{{ $message }}</small>@enderror
                    </label>
                    <label>Catégorie
                        <select name="category_id">
                            <option value="">Choisir une catégorie</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<small class="field-error">{{ $message }}</small>@enderror
                    </label>
                </div>
                <label class="photo-picker">Photo de votre métier <span class="muted">(atelier, outil ou travail en cours · obligatoire · 6 Mo max.)</span>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" data-artisan-required>
                    @error('cover_image')<small class="field-error">{{ $message }}</small>@enderror
                </label>
            </div>
            <div class="form-grid">
                <label>Mot de passe
                    <input type="password" name="password" required>
                    @error('password')<small class="field-error">{{ $message }}</small>@enderror
                </label>
                <label>Confirmation
                    <input type="password" name="password_confirmation" required>
                </label>
            </div>
            <label>Je m’inscris comme
                <select name="role" required data-registration-role>
                    <option value="client">Client</option>
                    <option value="artisan" @selected(old('role') === 'artisan')>Artisan</option>
                </select>
            </label>
            <button class="button button-dark button-full" type="submit">Créer mon compte</button>
        </form>
        <p class="form-note">Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a></p>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const accountRole = document.querySelector('[data-registration-role]');
    const artisanFields = document.querySelector('#artisan-registration-fields');
    const syncArtisanFields = () => {
        const isArtisan = accountRole?.value === 'artisan';
        artisanFields.hidden = !isArtisan;
        document.querySelectorAll('[data-artisan-required]').forEach((field) => field.required = isArtisan);
    };
    accountRole?.addEventListener('change', syncArtisanFields);
    syncArtisanFields();

    document.querySelector('#registration-form')?.addEventListener('submit', (event) => {
        const profilePhoto = document.querySelector('[name="profile_photo"]')?.files[0];
        const coverImage = document.querySelector('[name="cover_image"]')?.files[0];
        const megabyte = 1024 * 1024;

        if (profilePhoto && profilePhoto.size > 4 * megabyte) {
            event.preventDefault();
            alert('La photo de profil ne doit pas dépasser 4 Mo.');
        } else if (coverImage && coverImage.size > 6 * megabyte) {
            event.preventDefault();
            alert('La photo de votre métier ne doit pas dépasser 6 Mo.');
        }
    });
</script>
@endpush
