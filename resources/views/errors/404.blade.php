@extends('layouts.app')
@section('title', 'Page introuvable | GeoArtisans')
@section('content')
<section class="dashboard-shell error-page"><span class="eyebrow">ERREUR / 404</span><h1 class="page-title">Oups ! Cette destination<br><em>est introuvable.</em></h1><p class="lead-copy">Retournez à la carte pour trouver un artisan près de chez vous.</p><a class="button button-dark" href="{{ route('home') }}">Retour à l’accueil ↗</a></section>
@endsection
