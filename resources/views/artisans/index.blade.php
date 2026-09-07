@extends('layouts.app')

@section('title', 'Artisans à proximité | GeoArtisans')

@section('content')
<section class="search-hero">
    <span class="eyebrow">RECHERCHE / PROXIMITÉ</span>
    <h1>Trouvez un artisan<br><em>dans votre commune.</em></h1>
    <p>Sélectionnez uniquement votre métier et votre zone. GeoArtisans affiche les professionnels vérifiés à proximité.</p>
</section>

<section class="artisan-search-shell">
    <form class="proximity-filters" method="GET" action="{{ route('artisans.index') }}">
        <label>
            <span>01 / Catégorie</span>
            <select name="category">
                <option value="">Tous les métiers</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>02 / Commune ou village</span>
            <select name="commune">
                <option value="">Toutes les zones de Butembo</option>
                @foreach ($communes as $commune)
                    <option value="{{ $commune }}" @selected(request('commune') === $commune)>{{ $commune }}</option>
                @endforeach
            </select>
        </label>
        <button class="button button-dark" type="submit">Afficher les artisans <span>↗</span></button>
    </form>
    <button class="location-link proximity-location-button" type="button" data-user-location>◎ Utiliser ma position pour calculer les distances</button>

    <div class="search-results-layout">
        <div class="search-results-column">
            <div class="results-heading">
                <div><span class="eyebrow">RÉSULTATS</span><h2>{{ $artisans->total() }} artisan(s) trouvé(s)</h2></div>
                @if (request('category') && request('commune'))<span class="result-context">{{ $communes[array_search(request('commune'), $communes, true)] ?? request('commune') }}</span>@endif
            </div>
            <div class="artisan-list">
                @forelse ($artisans as $artisan)
                    <article class="artisan-card proximity-card" data-latitude="{{ $artisan->latitude }}" data-longitude="{{ $artisan->longitude }}">
                        @if ($artisan->cover_image)
                            <div class="artisan-work-image"><img src="{{ $artisan->coverImageUrl() }}" alt="Activité de {{ $artisan->user->first_name }} {{ $artisan->user->name }}"></div>
                        @else
                            <div class="artisan-work-image artisan-work-image-empty">Photo du métier non ajoutée</div>
                        @endif
                        <div class="artisan-avatar">
                            @if ($artisan->user->profile_photo)
                                <img src="{{ $artisan->user->profilePhotoUrl() }}" alt="Photo de {{ $artisan->user->first_name }}">
                            @else
                                {{ strtoupper(substr($artisan->user->first_name ?: $artisan->user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="artisan-card-content">
                            <span class="eyebrow">{{ $artisan->category?->name ?: 'Professionnel' }}</span>
                            <h2>{{ $artisan->user->first_name }} {{ $artisan->user->name }}</h2>
                            <p>{{ $artisan->profession }}</p>
                            <div class="artisan-location">⌖ <span>{{ $artisan->quartier ?: 'Quartier non renseigné' }}, {{ $artisan->commune ?: 'Butembo' }}</span></div>
                            <span class="distance-label" hidden></span>
                        </div>
                        <div class="artisan-card-actions"><a class="arrow-link" href="{{ route('artisans.show', $artisan) }}">Voir le profil ↗</a><a class="map-link" href="#artisan-map">Voir sur la carte</a></div>
                    </article>
                @empty
                    <div class="empty-page"><h2>Aucun artisan dans cette zone.</h2><p>Choisissez une autre catégorie ou une autre commune.</p></div>
                @endforelse
            </div>
            {{ $artisans->links() }}
        </div>
        <aside class="search-map-panel">
            <div class="map-panel-heading"><span class="eyebrow">CARTE / BUTEMBO</span><strong><span class="map-live-dot"></span> Artisans vérifiés · <span data-map-level>Butembo</span></strong></div>
            <div id="artisan-map" class="artisan-map"><div class="map-loading">Chargement de la carte…</div></div>
            <p class="map-help">Les marqueurs indiquent la zone déclarée par chaque artisan. La distance s’affiche uniquement après votre autorisation de géolocalisation.</p>
        </aside>
    </div>
</section>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const artisans = @json($mapArtisans);
    const communeCoordinates = @json($communeCoordinates);
    const selectedCommune = @json(request('commune'));
    const defaultCenter = selectedCommune && communeCoordinates[selectedCommune] ? communeCoordinates[selectedCommune] : [-0.13, 29.28];
    const mapElement = document.getElementById('artisan-map');
    if (typeof L === 'undefined') {
        mapElement.innerHTML = '<div class="map-fallback">La carte est momentanément indisponible.<br><small>Les résultats restent consultables à gauche.</small></div>';
    } else {
    mapElement.innerHTML = '';
    const map = L.map(mapElement, { scrollWheelZoom: true, zoomControl: true }).setView(defaultCenter, selectedCommune ? 14 : 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
    const markers = [];
    const locationLevel = (artisan, zoom) => {
        if (zoom <= 12) return ['Butembo', 'Ville de Butembo'];
        if (zoom <= 14) return [artisan.commune || 'Commune non renseignée', `Commune : ${artisan.commune || 'non renseignée'}`];
        if (zoom <= 16) return [artisan.quartier || 'Quartier non renseigné', `Quartier : ${artisan.quartier || 'non renseigné'}`];
        if (zoom <= 18) return [artisan.cell || 'Cellule non renseignée', `Cellule : ${artisan.cell || 'non renseignée'}`];
        return [artisan.address || 'Position GPS déclarée', 'Position GPS déclarée par l’artisan'];
    };
    artisans.forEach((artisan) => {
        const marker = L.marker([artisan.latitude, artisan.longitude]).addTo(map);
        markers.push({ marker, artisan });
    });
    const updateMapLevel = () => {
        const zoom = map.getZoom();
        const firstArtisan = markers[0]?.artisan;
        const [, label] = firstArtisan ? locationLevel(firstArtisan, zoom) : ['Butembo', 'Butembo'];
        document.querySelector('[data-map-level]').textContent = label;
        markers.forEach(({ marker, artisan }) => {
            const [place] = locationLevel(artisan, zoom);
            marker.bindPopup(`<strong>${artisan.name}</strong><br>${artisan.profession}<br><small>${place}</small><br><a href="${artisan.url}">Voir le profil</a>`);
        });
    };
    if (markers.length > 1) map.fitBounds(L.featureGroup(markers.map(({ marker }) => marker)).getBounds().pad(0.2));
    updateMapLevel();
    map.on('zoomend', updateMapLevel);
    setTimeout(() => map.invalidateSize(), 250);

    function distanceInKm(lat1, lon1, lat2, lon2) {
        const earthRadius = 6371;
        const radians = (value) => value * Math.PI / 180;
        const deltaLat = radians(lat2 - lat1);
        const deltaLon = radians(lon2 - lon1);
        const a = Math.sin(deltaLat / 2) ** 2 + Math.cos(radians(lat1)) * Math.cos(radians(lat2)) * Math.sin(deltaLon / 2) ** 2;
        return earthRadius * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    document.querySelector('[data-user-location]')?.addEventListener('click', (event) => {
        if (!navigator.geolocation) { event.target.textContent = 'Géolocalisation indisponible'; return; }
        navigator.geolocation.getCurrentPosition((position) => {
            const { latitude, longitude } = position.coords;
            L.marker([latitude, longitude]).addTo(map).bindPopup('Votre position').openPopup();
            document.querySelectorAll('.proximity-card').forEach((card) => {
                const distance = distanceInKm(latitude, longitude, Number(card.dataset.latitude), Number(card.dataset.longitude));
                const label = card.querySelector('.distance-label');
                label.textContent = distance < 1 ? `${Math.round(distance * 1000)} m de vous` : `${distance.toFixed(1).replace('.', ',')} km de vous`;
                label.hidden = false;
            });
            event.target.textContent = 'Distances calculées';
        }, () => { event.target.textContent = 'Position non autorisée'; });
    });
    }
</script>
@endpush
