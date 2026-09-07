@extends('layouts.app')
@section('title', 'Mes favoris | GeoArtisans')
@section('content')
<section class="dashboard-shell"><span class="eyebrow">ESPACE CLIENT / FAVORIS</span><h1 class="page-title">Votre carnet<br><em>d’adresses.</em></h1><div class="artisan-list">@forelse($favorites as $favorite)<article class="artisan-card"><div class="artisan-avatar">{{ strtoupper(substr($favorite->artisanProfile->user->first_name ?: $favorite->artisanProfile->user->name, 0, 1)) }}</div><div><h2>{{ $favorite->artisanProfile->user->first_name }} {{ $favorite->artisanProfile->user->name }}</h2><p>{{ $favorite->artisanProfile->profession }} · {{ $favorite->artisanProfile->commune }}</p></div><a class="arrow-link" href="{{ route('artisans.show', $favorite->artisanProfile) }}">Voir ↗</a></article>@empty<div class="empty-page"><h2>Votre carnet est vide.</h2><p>Ajoutez un artisan depuis son profil.</p></div>@endforelse</div>{{ $favorites->links() }}</section>
@endsection
