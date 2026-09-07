@extends('layouts.app')

@section('title', 'Carte des artisans | GeoArtisans')

@section('content')
<section class="city-map-page">
    <div id="city-map" class="city-map"><div class="map-loading">Chargement de la carte de Butembo…</div></div>

    <aside class="city-map-panel" aria-label="Recherche d’artisans">
        <div class="city-map-search">
            <span aria-hidden="true">⌕</span>
            <input id="map-search" type="search" placeholder="Rechercher un artisan ou un métier" autocomplete="off">
            <button id="map-search-clear" type="button" aria-label="Effacer la recherche" hidden>×</button>
        </div>
        <div class="city-map-panel-head">
            <span class="eyebrow">BUTEMBO / CARTE DIRECTE</span>
            <h1>Artisans autour de vous</h1>
            <p id="map-result-count">{{ $artisans->count() }} artisan(s) vérifié(s) à découvrir.</p>
        </div>
        <div id="map-category-filters" class="map-category-filters" aria-label="Filtrer par catégorie"></div>
        <button id="map-location" class="map-location-button" type="button"><span>◎</span> Utiliser ma position</button>
        <p id="map-location-feedback" class="map-location-feedback" aria-live="polite"></p>
        <div id="map-artisan-list" class="map-artisan-list" aria-live="polite"></div>
    </aside>

    <div class="map-floating-brand"><span class="map-live-dot"></span> Carte des artisans</div>
    <div class="map-scale-note">Butembo, Nord-Kivu</div>
</section>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const mapArtisans = @json($artisans);
    const mapElement = document.getElementById('city-map');
    const listElement = document.getElementById('map-artisan-list');
    const filterElement = document.getElementById('map-category-filters');
    const resultCount = document.getElementById('map-result-count');
    const searchInput = document.getElementById('map-search');
    const clearButton = document.getElementById('map-search-clear');
    const feedback = document.getElementById('map-location-feedback');
    let selectedCategory = 'Toutes';
    let map;
    const markerEntries = [];

    const normalize = (value) => (value || '').toLocaleLowerCase('fr');
    const matches = (artisan) => {
        const text = `${artisan.name} ${artisan.profession} ${artisan.category} ${artisan.commune} ${artisan.quartier || ''}`;
        return (selectedCategory === 'Toutes' || artisan.category === selectedCategory) && normalize(text).includes(normalize(searchInput.value));
    };

    const popupContent = (artisan) => {
        const content = document.createElement('div');
        const name = document.createElement('strong');
        name.textContent = artisan.name;
        const description = document.createElement('div');
        description.textContent = artisan.profession;
        const place = document.createElement('small');
        place.textContent = `${artisan.quartier ? artisan.quartier + ', ' : ''}${artisan.commune}`;
        const link = document.createElement('a');
        link.href = artisan.url;
        link.textContent = 'Voir le profil →';
        content.append(name, description, place, document.createElement('br'), link);
        return content;
    };

    const focusArtisan = (artisan) => {
        const entry = markerEntries.find((item) => item.artisan === artisan);
        if (!entry) return;
        map.setView(entry.marker.getLatLng(), 16, { animate: true });
        entry.marker.openPopup();
    };

    const renderList = (items) => {
        listElement.replaceChildren();
        resultCount.textContent = `${items.length} artisan(s) vérifié(s) ${items.length > 1 ? 'trouvés' : 'trouvé'}.`;
        if (!items.length) {
            const empty = document.createElement('p');
            empty.className = 'map-list-empty';
            empty.textContent = 'Aucun artisan ne correspond à cette recherche.';
            listElement.append(empty);
            return;
        }
        items.forEach((artisan) => {
            const card = document.createElement('button');
            card.className = 'map-artisan-card';
            card.type = 'button';
            const visual = document.createElement('span');
            visual.className = 'map-artisan-visual';
            if (artisan.photo) {
                visual.style.backgroundImage = `url("${artisan.photo}")`;
            } else {
                visual.textContent = artisan.name.charAt(0).toUpperCase();
            }
            const copy = document.createElement('span');
            copy.className = 'map-artisan-copy';
            const name = document.createElement('strong'); name.textContent = artisan.name;
            const job = document.createElement('span'); job.textContent = artisan.profession;
            const area = document.createElement('small'); area.textContent = `${artisan.quartier ? artisan.quartier + ', ' : ''}${artisan.commune}`;
            copy.append(name, job, area);
            const arrow = document.createElement('span'); arrow.className = 'map-card-arrow'; arrow.textContent = '→';
            card.append(visual, copy, arrow);
            card.addEventListener('click', () => focusArtisan(artisan));
            listElement.append(card);
        });
    };

    const updateMap = () => {
        const items = mapArtisans.filter(matches);
        markerEntries.forEach(({ artisan, marker }) => {
            if (items.includes(artisan)) marker.addTo(map);
            else marker.remove();
        });
        clearButton.hidden = !searchInput.value;
        renderList(items);
    };

    const renderCategories = () => {
        const categories = ['Toutes', ...new Set(mapArtisans.map((artisan) => artisan.category).filter(Boolean))];
        categories.forEach((category) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = category;
            button.classList.toggle('is-active', category === selectedCategory);
            button.addEventListener('click', () => {
                selectedCategory = category;
                [...filterElement.children].forEach((item) => item.classList.toggle('is-active', item.textContent === category));
                updateMap();
            });
            filterElement.append(button);
        });
    };

    if (typeof L === 'undefined') {
        mapElement.innerHTML = '<div class="map-fallback city-map-fallback">La carte est momentanément indisponible.<br><small>Veuillez vérifier votre connexion Internet.</small></div>';
    } else {
        mapElement.replaceChildren();
        map = L.map(mapElement, { zoomControl: false, scrollWheelZoom: true }).setView([-0.13, 29.28], 14);
        L.control.zoom({ position: 'bottomright' }).addTo(map);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors', maxZoom: 19,
        }).addTo(map);
        mapArtisans.forEach((artisan) => {
            const marker = L.marker([artisan.latitude, artisan.longitude]).addTo(map).bindPopup(popupContent(artisan));
            markerEntries.push({ artisan, marker });
        });
        if (markerEntries.length > 1) map.fitBounds(L.featureGroup(markerEntries.map((item) => item.marker)).getBounds().pad(0.16));
        else if (markerEntries.length === 1) map.setView(markerEntries[0].marker.getLatLng(), 16);
        renderCategories();
        updateMap();
    }

    searchInput.addEventListener('input', updateMap);
    clearButton.addEventListener('click', () => { searchInput.value = ''; searchInput.focus(); updateMap(); });
    document.getElementById('map-location').addEventListener('click', (event) => {
        if (!navigator.geolocation || !map) { feedback.textContent = 'La géolocalisation est indisponible.'; return; }
        event.currentTarget.disabled = true;
        navigator.geolocation.getCurrentPosition((position) => {
            const point = [position.coords.latitude, position.coords.longitude];
            L.circleMarker(point, { radius: 9, color: '#fff', weight: 3, fillColor: '#1a88a5', fillOpacity: 1 }).addTo(map).bindPopup('Votre position').openPopup();
            map.setView(point, 15, { animate: true });
            feedback.textContent = 'Votre position est affichée sur la carte.';
            event.currentTarget.disabled = false;
        }, () => { feedback.textContent = 'Nous n’avons pas pu obtenir votre position.'; event.currentTarget.disabled = false; });
    });
</script>
@endpush
