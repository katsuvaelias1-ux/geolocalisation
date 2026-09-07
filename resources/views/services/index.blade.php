@extends('layouts.app')

@section('title', 'Services | GeoArtisans')

@section('content')
<section class="dashboard-shell">
    <span class="eyebrow">SERVICES / CATALOGUE</span>
    <h1 class="page-title">Le bon geste,<br><em>au bon endroit.</em></h1>
    <div class="category-grid">
        @forelse ($categories as $category)
            <article>
                <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $category->name }}</h3>
                <p>{{ $category->description ?: 'Trouvez un professionnel de proximité pour ce service.' }}</p>
                <a href="{{ route('categories.show', $category) }}">Voir les artisans ↗</a>
            </article>
        @empty
            <div class="empty-page">
                <h2>Aucune catégorie disponible.</h2>
                <p>Les services seront publiés dès leur validation par l’administration.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
