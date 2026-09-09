@extends('layouts.app')

@section('title', 'GeoArtisans Butembo | Le savoir-faire local')

@section('content')
<div class="home-progress-rail" aria-hidden="true"><span class="rail-pin">⌖</span><span class="rail-line"></span><div><b>01</b><b>02</b><b>03</b><b>04</b></div><span class="rail-bell">●</span></div>
<a class="home-discovery-bar" href="{{ route('services.index') }}"><span>NOUVEAU</span><strong>Découvrez les talents de Butembo</strong><i>→</i></a>
<section class="hero editorial-hero home-hero reveal">
    <div class="hero-copy">
        <span class="eyebrow">BUTEMBO / SERVICES DE PROXIMITÉ</span>
        <h1>Le savoir-faire local, <em>à portée de carte.</em></h1>
        <p>Trouvez en quelques instants un professionnel fiable près de chez vous.</p>
        <form class="search-panel" method="GET" action="{{ route('artisans.index') }}">
            <label>
                <span>Catégorie de service</span>
                <select name="category">
                    <option value="">Tous les métiers</option>
                    @foreach(\App\Models\Category::where('status', true)->orderBy('name')->get() as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Commune ou village</span>
                <select name="commune">
                    <option value="">Toutes les zones de Butembo</option>
                    <option>Bulengera</option>
                    <option>Kimemi</option>
                    <option>Mususa</option>
                    <option>Vulamba</option>
                </select>
            </label>
            <button class="button button-dark" type="submit">Rechercher <span>→</span></button>
        </form>
        <div class="hero-shortcuts">
            <button class="location-link" type="button" data-location>◎ Utiliser ma position</button>
            <a class="hero-map-link" href="{{ route('map') }}">Voir la carte en direct <span>→</span></a>
        </div>
    </div>
    <div class="hero-photo" data-parallax="0.09">
        <div class="hero-photo-overlay"><span>GEOARTISANS / BUTEMBO</span><strong>Les mains<br>qui font avancer<br>la ville.</strong></div>
    </div>
</section>

<section class="manifesto home-manifesto reveal">
    <div class="manifesto-index">01</div>
    <div>
        <span class="eyebrow">LOCALISEZ / EXPLOREZ / CONTACTEZ</span>
        <h2>Chaque métier a une adresse. Chaque quartier a ses talents.</h2>
    </div>
    <p>GeoArtisans rapproche les habitants des professionnels disponibles autour d’eux, avec une information claire et une localisation déclarée.</p>
</section>

<section class="feature-band home-feature reveal" id="explore">
    <div class="feature-image feature-image-map" data-parallax="0.055"><div class="feature-image-label"><span>02</span><strong>Une carte<br>à taille humaine.</strong></div></div>
    <div class="feature-copy">
        <span class="eyebrow">LOCALISEZ</span>
        <h2>Les services dont vous avez besoin, plus proches de vous.</h2>
        <p>Retrouvez les artisans vérifiés de votre zone sur une carte simple à lire.</p>
        <a class="button button-light" href="{{ route('map') }}">Ouvrir la carte <span>→</span></a>
    </div>
</section>

<section class="section-band section-light home-services reveal" id="services">
    <div class="section-heading"><span class="eyebrow">03 / EXPLOREZ</span><h2>Quel service recherchez-vous ?</h2><a class="arrow-link" href="{{ route('services.index') }}">Toutes les catégories <span>→</span></a></div>
    <div class="category-grid">
        @forelse(\App\Models\Category::query()->where('status', true)->orderBy('name')->limit(4)->get() as $category)
            <article><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $category->name }}</h3><p>{{ $category->description ?: 'Trouvez un professionnel de proximité pour ce service.' }}</p><a href="{{ route('categories.show', $category) }}">Trouver un artisan →</a></article>
        @empty
            <div class="empty-page"><h2>Les catégories arrivent bientôt.</h2><p>Actualisez cette page dans quelques instants.</p></div>
        @endforelse
    </div>
</section>

<section class="craft-gallery home-gallery reveal">
    <div class="gallery-copy"><span class="eyebrow">04 / SAVOIR-FAIRE</span><h2>Des personnes avant des résultats.</h2><p>Découvrez les gestes et les parcours qui donnent vie aux services de proximité.</p><a class="arrow-link" href="{{ route('artisans.index') }}">Voir les artisans →</a></div>
    <div class="gallery-image gallery-image-tools"><span>01 / LE GESTE</span></div>
    <div class="gallery-image gallery-image-workshop"><span>02 / L’ATELIER</span></div>
</section>

<section class="steps home-steps reveal" id="how-it-works">
    <span class="eyebrow">05 / COMMENT ÇA MARCHE</span><h2>Du besoin à la rencontre.</h2>
    <div class="step-grid"><div><strong>01</strong><h3>Recherchez</h3><p>Choisissez une catégorie.</p></div><div><strong>02</strong><h3>Localisez</h3><p>Indiquez votre commune.</p></div><div><strong>03</strong><h3>Contactez</h3><p>Échangez avec l’artisan.</p></div></div>
    <a class="button button-dark section-button" href="{{ route('how-it-works') }}">Découvrir le parcours <span>→</span></a>
</section>

<section class="artisan-cta home-artisan-cta reveal"><div><span class="eyebrow">POUR LES PROFESSIONNELS</span><h2>Vous êtes artisan à Butembo ?</h2><p>Faites connaître votre métier, votre zone et votre savoir-faire.</p></div><a class="button button-light" href="{{ route('register') }}?role=artisan">Créer mon profil <span>→</span></a></section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-location]').forEach((button) => button.addEventListener('click', () => {
        if (!navigator.geolocation) { button.textContent = 'Géolocalisation indisponible'; return; }
        navigator.geolocation.getCurrentPosition(() => { button.textContent = 'Position utilisée'; }, () => { button.textContent = 'Position non autorisée'; });
    }));
</script>
@endpush
