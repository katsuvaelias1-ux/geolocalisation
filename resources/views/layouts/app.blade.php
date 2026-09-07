<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GeoArtisans Butembo')</title>
    <meta name="description" content="Le savoir-faire local, à portée de carte.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-loading {{ request()->routeIs('home') ? 'home-page' : '' }}">
    <div class="site-intro" id="site-intro" aria-hidden="true">
        <div class="intro-grid" aria-hidden="true"></div>
        <div class="intro-orbit intro-orbit-one" aria-hidden="true"></div>
        <div class="intro-orbit intro-orbit-two" aria-hidden="true"></div>
        <div class="intro-content">
            <div class="intro-brand"><span class="intro-mark">G</span><span>GEO<span>ARTISANS</span></span></div>
            <p>Butembo / Nord-Kivu</p>
            <strong>Le savoir-faire local,<br>à portée de carte.</strong>
        </div>
        <div class="intro-progress"><span></span></div>
        <small class="intro-scroll-label">Chargement de votre quartier</small>
    </div>
    <div class="topline"><span>GEOARTISANS / BUTEMBO</span><span>Le savoir-faire local, à portée de carte.</span></div>
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark">G</span><span>GEO<span>ARTISANS</span></span></a>
        <nav class="main-nav" aria-label="Navigation principale">
            <a href="{{ route('home') }}">Accueil</a>
            <a href="{{ route('artisans.index') }}">Artisans</a>
            <a href="{{ route('services.index') }}">Services</a>
            <a href="{{ route('map') }}">Carte</a>
            <a href="{{ route('how-it-works') }}">Comment ça marche</a>
        </nav>
        <div class="header-actions">
            <a class="header-command header-command-contact" data-magnetic href="{{ route('contact') }}">Contact</a>
            @auth
                <a class="header-command header-command-quiet" data-magnetic href="{{ route('dashboard.entry') }}">Mon espace</a>
                <form method="POST" action="{{ route('logout') }}"><button class="button button-ghost" type="submit">Déconnexion</button>@csrf</form>
            @else
                <a class="header-command header-command-quiet" data-magnetic href="{{ route('login') }}">Connexion</a>
                <a class="header-command header-command-primary" data-magnetic href="{{ route('register') }}"><span>Devenir artisan</span><i>↗</i></a>
            @endauth
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="Ouvrir le menu">☰</button>
        </div>
    </header>
    <nav class="mobile-menu" id="mobile-menu" aria-label="Navigation mobile">
        <a href="{{ route('home') }}">Accueil</a>
        <a href="{{ route('artisans.index') }}">Artisans</a>
        <a href="{{ route('services.index') }}">Services</a>
        <a href="{{ route('map') }}">Carte</a>
        <a href="{{ route('how-it-works') }}">Comment ça marche</a>
        <a href="{{ route('contact') }}">Contact</a>
    </nav>
    <main>@yield('content')</main>
    <footer class="site-footer"><div><span class="brand brand-light"><span class="brand-mark">G</span><span>GEO<span>ARTISANS</span></span></span><p>Le savoir-faire local, à portée de carte.</p></div><div><strong>Butembo, Nord-Kivu</strong><span>République démocratique du Congo</span></div><small>© 2026 GeoArtisans Butembo</small></footer>
    @stack('scripts')
</body>
</html>
