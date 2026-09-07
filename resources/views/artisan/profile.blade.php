@extends('layouts.app')

@section('title', 'Mon profil artisan | GeoArtisans')

@section('content')
<section class="workspace-hero reveal">
    <div>
        <span class="eyebrow">ESPACE ARTISAN / PROFIL</span>
        <h1>Présentez votre<br><em>savoir-faire.</em></h1>
        <p>Un profil précis aide les habitants de Butembo à comprendre votre métier, votre zone d’intervention et la manière de vous contacter.</p>
    </div>
    <div class="workspace-hero-index"><strong>.01</strong><span>Votre identité<br>professionnelle</span></div>
</section>

@if (session('success'))
    <div class="workspace-alert alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="workspace-alert alert alert-error">Vérifiez les champs signalés avant d’enregistrer votre profil.</div>
@endif

<form method="POST" action="{{ route('artisan.profile.update') }}" enctype="multipart/form-data" class="workspace-form" id="artisan-profile-form">
    @csrf
    @method('PUT')

    <section class="workspace-section reveal">
        <div class="workspace-section-heading"><span>.01</span><div><span class="eyebrow">IDENTITÉ DU MÉTIER</span><h2>Ce que vous faites.</h2><p>Présentez votre spécialité avec des mots simples et précis.</p></div></div>
        <div class="workspace-fields">
            <label>Métier
                <input name="profession" value="{{ old('profession', $profile?->profession === 'À compléter' ? '' : $profile?->profession) }}" placeholder="Ex. Électricien bâtiment" required>
                @error('profession')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label>Catégorie
                <select name="category_id">
                    <option value="">Choisir une catégorie</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $profile?->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label class="field-wide">Description de votre activité
                <textarea name="description" rows="6" placeholder="Présentez vos services, votre expérience et les types de travaux que vous acceptez.">{{ old('description', $profile?->description) }}</textarea>
                @error('description')<small class="field-error">{{ $message }}</small>@enderror
            </label>
        </div>
    </section>

    <section class="workspace-section workspace-section-dark reveal">
        <div class="workspace-section-heading"><span>.02</span><div><span class="eyebrow">EXPÉRIENCE / CONTACT</span><h2>Donnez confiance.</h2><p>Quelques informations permettent au client de choisir le bon professionnel.</p></div></div>
        <div class="workspace-fields">
            <label>Années d’expérience
                <input type="number" name="experience_years" min="0" max="80" value="{{ old('experience_years', $profile?->experience_years) }}" placeholder="0">
                @error('experience_years')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label>Numéro WhatsApp
                <input name="whatsapp" value="{{ old('whatsapp', $profile?->whatsapp) }}" placeholder="243...">
                @error('whatsapp')<small class="field-error">{{ $message }}</small>@enderror
            </label>
        </div>
    </section>

    <section class="workspace-section reveal">
        <div class="workspace-section-heading"><span>.05</span><div><span class="eyebrow">IMAGE DU MÉTIER</span><h2>Montrez votre activité.</h2><p>Ajoutez une photo réelle de votre atelier, de votre équipement ou d’un travail en cours.</p></div></div>
        <div class="workspace-fields">
            <label class="field-wide photo-picker">Photo de votre métier <span class="muted">JPG, PNG ou WebP · 6 Mo max.</span>
                <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
                @error('cover_image')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            @if ($profile?->cover_image)
                <div class="current-work-image field-wide"><img src="{{ $profile->coverImageUrl() }}" alt="Activité de {{ auth()->user()->first_name }} {{ auth()->user()->name }}"><span>Image actuelle</span></div>
            @endif
        </div>
    </section>

    <section class="workspace-section reveal">
        <div class="workspace-section-heading"><span>.03</span><div><span class="eyebrow">ZONE D’INTERVENTION</span><h2>Où vous trouver.</h2><p>Votre adresse sert à situer votre activité sur la carte GeoArtisans.</p></div></div>
        <div class="workspace-fields">
            <label>Commune
                <select name="commune" required>
                    @foreach (['Bulengera', 'Kimemi', 'Mususa', 'Vulamba'] as $commune)
                        <option value="{{ $commune }}" @selected(old('commune', $profile?->commune) === $commune)>{{ $commune }}</option>
                    @endforeach
                </select>
                @error('commune')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label>Quartier
                <input name="quartier" value="{{ old('quartier', $profile?->quartier) }}" placeholder="Votre quartier">
                @error('quartier')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label class="field-wide">Adresse ou repère
                <input name="address" value="{{ old('address', $profile?->address) }}" placeholder="Rue, avenue, repère connu">
                @error('address')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <div class="coordinates-card field-wide"><div class="coordinates-card-heading"><div><strong>Coordonnées GPS</strong><span>Autorisez votre position pour remplir automatiquement la latitude et la longitude.</span></div><button class="button button-light location-button" type="button" data-fill-location>Utiliser ma position</button></div><div class="workspace-fields coordinates-fields"><label>Latitude<input id="latitude" name="latitude" value="{{ old('latitude', $profile?->latitude) }}" placeholder="-0.13"></label><label>Longitude<input id="longitude" name="longitude" value="{{ old('longitude', $profile?->longitude) }}" placeholder="29.28"></label></div><small class="location-feedback" data-location-feedback></small></div>
        </div>
    </section>

    <section class="workspace-section reveal">
        <div class="workspace-section-heading"><span>.04</span><div><span class="eyebrow">DISPONIBILITÉ</span><h2>Quand vous contacter.</h2><p>Votre disponibilité est visible sur votre profil public.</p></div></div>
        <div class="availability-choice"><label><input type="radio" name="availability" value="available" @checked(old('availability', $profile?->availability) === 'available')><span><strong>Disponible</strong><small>Je peux recevoir de nouvelles demandes.</small></span></label><label><input type="radio" name="availability" value="unavailable" @checked(old('availability', $profile?->availability) === 'unavailable')><span><strong>Indisponible</strong><small>Je ne prends pas de nouvelle demande actuellement.</small></span></label></div>
    </section>

    <div class="workspace-submit"><span>Votre profil sera soumis à validation avant sa publication.</span><button class="button button-dark" type="submit">Enregistrer mon profil <span>↗</span></button></div>
</form>
@endsection
@push('scripts')
<script>
    document.querySelector('[data-fill-location]')?.addEventListener('click', (event) => {
        const feedback = document.querySelector('[data-location-feedback]');
        if (!navigator.geolocation) {
            feedback.textContent = 'La géolocalisation n’est pas disponible sur cet appareil.';
            return;
        }
        event.target.textContent = 'Localisation…';
        navigator.geolocation.getCurrentPosition((position) => {
            document.querySelector('#latitude').value = position.coords.latitude.toFixed(7);
            document.querySelector('#longitude').value = position.coords.longitude.toFixed(7);
            feedback.textContent = 'Coordonnées récupérées. Enregistrez le profil pour les conserver.';
            event.target.textContent = 'Position récupérée';
        }, () => {
            feedback.textContent = 'Position non autorisée. Vous pouvez saisir les coordonnées manuellement.';
            event.target.textContent = 'Réessayer';
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
    });
</script>
@endpush
