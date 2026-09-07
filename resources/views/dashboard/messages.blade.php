@extends('layouts.app')
@section('title', 'Mes messages | GeoArtisans')
@section('content')
<section class="dashboard-shell"><span class="eyebrow">ESPACE CLIENT / MESSAGES</span><h1 class="page-title">Vos conversations<br><em>en un lieu.</em></h1><div class="artisan-list">@forelse($messages as $message)<article class="artisan-card"><div class="artisan-avatar">G</div><div><h2>{{ $message->subject }}</h2><p>À {{ $message->artisanProfile->user->first_name }} {{ $message->artisanProfile->user->name }} · {{ $message->created_at->format('d/m/Y') }}</p><p>{{ $message->message }}</p></div></article>@empty<div class="empty-page"><h2>Aucune conversation.</h2><p>Contactez un artisan depuis son profil.</p></div>@endforelse</div>{{ $messages->links() }}</section>
@endsection
