@extends('layouts.app')
@section('title', 'Mes messages | GeoArtisans')
@section('content')
<section class="dashboard-shell"><span class="eyebrow">ESPACE ARTISAN / MESSAGES</span><h1 class="page-title">Vos échanges<br><em>de proximité.</em></h1><div class="artisan-list">@forelse($messages as $message)<article class="artisan-card"><div class="artisan-avatar">{{ strtoupper(substr($message->sender->first_name ?: $message->sender->name, 0, 1)) }}</div><div><h2>{{ $message->subject }}</h2><p>{{ $message->sender->first_name }} {{ $message->sender->name }} · {{ $message->is_read ? 'Lu' : 'Nouveau' }}</p><p>{{ $message->message }}</p></div>@if(!$message->is_read)<form method="POST" action="{{ route('artisan.messages.read', $message) }}">@csrf @method('PATCH')<button class="button button-dark" type="submit">Marquer lu</button></form>@endif</article>@empty<div class="empty-page"><h2>Aucun message reçu.</h2></div>@endforelse</div>{{ $messages->links() }}</section>
@endsection
