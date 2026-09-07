@extends('layouts.app')

@section('title', $artisan->user->first_name . ' ' . $artisan->user->name . ' | GeoArtisans')

@section('content')
<section class="profile-hero">
    <div class="profile-avatar">
        @if ($artisan->user->profile_photo)
            <img src="{{ $artisan->user->profilePhotoUrl() }}" alt="Photo de {{ $artisan->user->first_name }}">
        @else
            {{ strtoupper(substr($artisan->user->first_name ?: $artisan->user->name, 0, 1)) }}
        @endif
    </div>
    <div>
        <span class="eyebrow">{{ $artisan->category?->name ?: 'Artisan' }} · {{ $artisan->commune }}</span>
        <h1>{{ $artisan->user->first_name }} {{ $artisan->user->name }}</h1>
        <p>{{ $artisan->profession }} · {{ $artisan->quartier ?: 'Butembo' }}</p>
        <div class="profile-actions">
            @auth
                @if (auth()->user()->role === 'client')
                    <a class="button button-light" href="#contact-artisan">Envoyer un message</a>
                @endif
            @else
                <a class="button button-light" href="{{ route('login') }}">Se connecter pour contacter</a>
            @endauth
            @auth
                @if (auth()->user()->role === 'client')
                    <form method="POST" action="{{ route('artisans.favorite', $artisan) }}">
                        @csrf
                        <button class="button button-outline-light" type="submit">♡ Favori</button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</section>

@if ($artisan->cover_image)
    <section class="artisan-cover-photo"><img src="{{ $artisan->coverImageUrl() }}" alt="Activité de {{ $artisan->user->first_name }} {{ $artisan->user->name }}"><div><span class="eyebrow">SAVOIR-FAIRE / {{ $artisan->profession }}</span><strong>Une activité présentée par l’artisan.</strong></div></section>
@endif

@if ($artisan->latitude && $artisan->longitude)
    <section class="artisan-location-map-section">
        <div class="artisan-location-map-heading">
            <div><span class="eyebrow">LOCALISATION / BUTEMBO</span><h2>Voici où trouver {{ $artisan->user->first_name }}.</h2><p>{{ $artisan->quartier ?: 'Quartier non renseigné' }}, {{ $artisan->commune }}</p><p class="map-zoom-status" data-map-zoom-status>Vue générale : Butembo</p></div>
            <a class="button button-dark" href="https://www.google.com/maps/search/?api=1&query={{ $artisan->latitude }},{{ $artisan->longitude }}" target="_blank" rel="noopener">Ouvrir Google Maps ↗</a>
        </div>
        <div id="artisan-location-map" class="artisan-location-map" aria-label="Carte de localisation de {{ $artisan->user->first_name }}" data-name="{{ $artisan->user->first_name }} {{ $artisan->user->name }}" data-profession="{{ $artisan->profession }}" data-commune="{{ $artisan->commune }}" data-quartier="{{ $artisan->quartier }}" data-cell="{{ $artisan->user->cell }}" data-address="{{ $artisan->address }}" data-latitude="{{ $artisan->latitude }}" data-longitude="{{ $artisan->longitude }}"></div>
    </section>
@endif

<section class="profile-body">
    <div>
        <span class="eyebrow">PRÉSENTATION</span>
        <h2>Le métier, avec une adresse.</h2>
        <p>{{ $artisan->description ?: 'La présentation de cet artisan n’a pas encore été renseignée.' }}</p>
        <div class="profile-location">
            <strong>⌖ Localisation</strong>
            <span>{{ $artisan->address ?: 'Adresse non renseignée' }}, {{ $artisan->quartier ?: 'quartier non renseigné' }}, {{ $artisan->commune ?: 'Butembo' }}</span>
            @if ($artisan->latitude && $artisan->longitude)
                <a class="map-link" href="https://www.google.com/maps/search/?api=1&query={{ $artisan->latitude }},{{ $artisan->longitude }}" target="_blank" rel="noopener">Ouvrir dans Google Maps ↗</a>
            @endif
        </div>
    </div>
    <aside class="profile-facts">
        <strong>{{ $artisan->experience_years ?: 0 }} ans</strong>
        <span>d’expérience</span>
        <strong>{{ $artisan->availability === 'available' ? 'Disponible' : 'Indisponible' }}</strong>
        <span>disponibilité</span>
        <strong>{{ $artisan->services->count() }}</strong>
        <span>services</span>
    </aside>
</section>

<section class="section-light profile-services">
    <span class="eyebrow">SERVICES</span>
    <h2>Ce que je peux faire pour vous.</h2>
    <div class="category-grid">
        @forelse ($artisan->services as $service)
            <article>
                <span>↗</span>
                <h3>{{ $service->name }}</h3>
                <p>{{ $service->description }}</p>
            </article>
        @empty
            <p>Aucun service publié pour le moment.</p>
        @endforelse
    </div>

    @auth
        @if (auth()->user()->role === 'client')
            <div class="contact-artisan" id="contact-artisan">
                <h3>Écrire à cet artisan</h3>
                <p class="form-note">Votre message sera enregistré dans GeoArtisans et transmis sur WhatsApp avec votre numéro de téléphone.</p>
                <form method="POST" action="{{ route('artisans.messages.store', $artisan) }}" class="stack-form">
                    @csrf
                    <label>Sujet
                        <input name="subject" required>
                    </label>
                    <label>Message
                        <textarea name="message" rows="4" required></textarea>
                    </label>
                    <button class="button button-dark" type="submit">Envoyer le message ↗</button>
                </form>
            </div>
        @endif
    @endauth
</section>
@endsection

@if ($artisan->latitude && $artisan->longitude)
    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const artisanLocationMap = document.getElementById('artisan-location-map');
        const artisanLocation = {
            name: artisanLocationMap.dataset.name,
            profession: artisanLocationMap.dataset.profession,
            commune: artisanLocationMap.dataset.commune,
            quartier: artisanLocationMap.dataset.quartier || 'Quartier non renseigné',
            cell: artisanLocationMap.dataset.cell || 'Cellule non renseignée',
            address: artisanLocationMap.dataset.address || 'Adresse GPS déclarée',
            latitude: Number(artisanLocationMap.dataset.latitude),
            longitude: Number(artisanLocationMap.dataset.longitude),
        };
        if (typeof L === 'undefined') {
            artisanLocationMap.innerHTML = '<p class="map-fallback">La carte est momentanément indisponible. Utilisez le bouton Google Maps.</p>';
        } else {
            artisanLocationMap.innerHTML = '';
            const map = L.map(artisanLocationMap, { scrollWheelZoom: true }).setView([-0.1300, 29.2800], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
            const marker = L.marker([artisanLocation.latitude, artisanLocation.longitude]).addTo(map);
            const zoomStatus = document.querySelector('[data-map-zoom-status]');
            const locationLevel = (zoom) => {
                if (zoom <= 12) return ['Butembo', 'Vue générale de la ville'];
                if (zoom <= 14) return [artisanLocation.commune, `Commune : ${artisanLocation.commune}`];
                if (zoom <= 16) return [artisanLocation.quartier, `Quartier : ${artisanLocation.quartier}`];
                if (zoom <= 18) return [artisanLocation.cell, `Cellule : ${artisanLocation.cell}`];
                return [artisanLocation.address, 'Position GPS déclarée par l’artisan'];
            };
            const updateLocationLevel = () => {
                const [place, label] = locationLevel(map.getZoom());
                zoomStatus.textContent = label;
                marker.bindPopup(`<strong>${artisanLocation.name}</strong><br>${artisanLocation.profession}<br><small>${place}</small>`);
            };
            updateLocationLevel();
            marker.openPopup();
            map.on('zoomend', updateLocationLevel);
            setTimeout(() => map.invalidateSize(), 250);
        }
    </script>
    @endpush
@endif
