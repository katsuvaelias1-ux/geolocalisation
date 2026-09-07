@extends('layouts.app')
@section('title', 'Administration | GeoArtisans')
@section('content')
<section class="dashboard-shell"><div class="dashboard-intro"><span class="eyebrow">ADMINISTRATION / 01</span><h1>Vue d’ensemble.</h1><p>Modérez la plateforme et accompagnez les artisans locaux.</p></div><div class="stats-grid"><div><span>Utilisateurs</span><strong>{{ $usersCount }}</strong></div><div><span>Artisans</span><strong>{{ $artisansCount }}</strong></div><div><span>Catégories</span><strong>{{ $categoriesCount }}</strong></div><div><span>Services</span><strong>{{ $servicesCount }}</strong></div></div><div class="dashboard-card"><span class="card-index">.02</span><h2>Prochaines actions</h2><p>La gestion des artisans, catégories et avis sera centralisée dans cet espace.</p></div></section>
@endsection
