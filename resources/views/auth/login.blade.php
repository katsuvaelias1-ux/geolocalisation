@extends('layouts.app')
@section('title', 'Se connecter | GeoArtisans')
@section('content')
<section class="auth-shell"><div class="auth-aside"><span class="eyebrow">GEOARTISANS / 01</span><h1>Retrouvez votre espace local.</h1><p>Suivez vos échanges, vos favoris et votre activité depuis un seul endroit.</p></div><div class="form-panel"><span class="eyebrow">Connexion</span><h2>Bon retour.</h2>@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<form method="POST" action="{{ route('login.store') }}" class="stack-form">@csrf
<label>E-mail<input type="email" name="email" value="{{ old('email') }}" required autofocus>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
<label>Mot de passe<input type="password" name="password" required>@error('password')<small class="field-error">{{ $message }}</small>@enderror</label>
<label class="check-line"><input type="checkbox" name="remember"> Se souvenir de moi</label><button class="button button-dark button-full" type="submit">Ouvrir ma session</button></form><p class="form-note">Pas encore de compte ? <a href="{{ route('register') }}">Créer un compte</a></p></div></section>
@endsection
