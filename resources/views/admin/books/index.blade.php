@extends('layouts.app')

@section('title', 'Catalogue des Livres')

@section('content')
<div class="container">
    <header style="margin-bottom: 2rem; text-align: center;">
        <hgroup>
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">📚 Notre Bibliothèque</h1>
            <p style="color: #6c757d; font-size: 1.1rem;">Explorez notre collection de {{ $books->count() }} ouvrages disponibles.</p>
        </hgroup>
    </header>

    <div class="search-container" style="margin-bottom: 3rem;">
        <form action="{{ route('books.search') }}" method="GET" role="search" style="margin: 0;">
            <div class="grid search-grid">
                <input type="search" name="q" placeholder="Rechercher un titre, un auteur ou un ISBN..." aria-label="Recherche" style="margin-bottom: 0;">
                <button type="submit" class="primary">Rechercher</button>
            </div>
        </form>
    </div>

    <div class="books-grid">
        @forelse($books as $book)
            <article class="book-card">
                <header class="book-header">
                    <div class="genre-badge">{{ $book->genre->name ?? 'Genre inconnu' }}</div>
                    <h4 class="book-title" title="{{ $book->title }}">{{ Str::limit($book->title, 50) }}</h4>
                    <p class="book-author">par <span>{{ $book->author }}</span></p>
                </header>

                <div class="book-body">
                    <details class="book-details">
                        <summary>📖 Voir le résumé et les infos</summary>
                        <div class="details-content">
                            <p><strong>ISBN :</strong> <span class="text-mono">{{ $book->isbn }}</span></p>
                            <p class="description"><strong>Résumé :</strong><br>
                                {{ $book->description ?? 'Aucun résumé disponible pour ce livre.' }}
                            </p>
                            <p class="date-added"><strong>Ajouté le :</strong> {{ $book->created_at->format('d/m/Y') }}</p>
                        </div>
                    </details>

                    <div class="stock-status">
                        <strong>Disponibilité :</strong>
                        @if($book->stock > 0)
                            <span class="badge badge-success">✅ {{ $book->stock }} en stock</span>
                        @else
                            <span class="badge badge-error">❌ Épuisé</span>
                        @endif
                    </div>
                </div>

                <footer class="book-footer">
                    <div class="grid action-grid">
                        @if($book->stock > 0)
                            <form action="{{ route('loans.store', $book->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="contrast btn-action">Emprunter</button>
                            </form>
                        @else
                            <button disabled class="secondary btn-action">Indisponible</button>
                        @endif
                        
                        <button class="outline secondary btn-fav" onclick="alert('Ajouté aux favoris !')" title="Mettre en favoris">❤️</button>
                    </div>
                </footer>
            </article>
        @empty
            <div class="empty-state" style="text-align: center; grid-column: 1 / -1; padding: 3rem; background: #f8f9fa; border-radius: 8px;">
                <p style="font-size: 1.2rem; color: #6c757d;">Aucun livre trouvé dans le catalogue.</p>
                <a href="{{ route('books.index') }}" class="button outline">Effacer la recherche</a>
            </div>
        @endforelse
    </div>
</div>

<style>
    /* --- Styles Globaux et Reset Pico --- */
    :root {
        --pico-card-background-color: #ffffff;
        --pico-card-box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
        --pico-card-border-radius: 12px;
    }

    /* --- Grille Principale --- */
    .books-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }

    /* --- Carte Livre (Structure Pro) --- */
    .book-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        margin: 0; /* Reset Pico */
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #eee;
    }

    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    /* --- Header de la carte --- */
    .book-header {
        padding: 1.5rem;
        background-color: #fcfcfd;
        border-bottom: 1px solid #eee;
        flex-grow: 0;
    }

    .genre-badge {
        display: inline-block;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .book-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.25rem;
        line-height: 1.3;
    }

    .book-author {
        font-size: 0.9rem;
        color: #6c757d;
        font-style: italic;
        margin: 0;
    }
    .book-author span {
        color: #495057;
        font-weight: 500;
    }

    /* --- Corps de la carte --- */
    .book-body {
        padding: 1.5rem;
        flex-grow: 1; /* Prend tout l'espace disponible */
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* --- Détails Déroulants Stylisés --- */
    .book-details {
        margin: 0;
        border: 1px solid #eee;
        border-radius: 8px;
        background-color: #fff;
    }

    .book-details summary {
        padding: 0.75rem 1rem;
        color: var(--pico-primary);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background-color 0.2s;
        list-style: none; /* Cache la flèche par défaut */
    }
    
    .book-details summary::after {
        content: '+'; /* Icône personnalisée */
        float: right;
        font-weight: bold;
    }
    
    .book-details[open] summary::after {
        content: '-';
    }

    .book-details summary:hover {
        background-color: #f1f5f9;
    }

    /* Supprime la flèche par défaut sur Chrome/Safari */
    .book-details summary::-webkit-details-marker {
        display: none;
    }

    .details-content {
        padding: 1rem;
        border-top: 1px solid #eee;
        background-color: #f8f9fa;
        font-size: 0.85rem;
        color: #495057;
    }
    .details-content p { margin-bottom: 0.5rem; }
    .details-content p:last-child { margin-bottom: 0; }
    
    .text-mono { font-family: monospace; background: #eee; padding: 2px 4px; border-radius: 4px;}
    .description { line-height: 1.5; color: #6c757d;}

    /* --- Status Stock --- */
    .stock-status {
        margin-top: auto; /* Pousse vers le bas de book-body */
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
    }
    .badge-success { background-color: #d1fae5; color: #065f46; }
    .badge-error { background-color: #fee2e2; color: #991b1b; }

    /* --- Footer de la carte --- */
    .book-footer {
        padding: 1.25rem 1.5rem;
        background-color: #fcfcfd;
        border-top: 1px solid #eee;
        flex-grow: 0;
    }

    .action-grid {
        grid-template-columns: 1fr auto;
        gap: 0.75rem;
        align-items: center;
    }

    .btn-action {
        width: 100%;
        margin: 0;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 8px;
    }

    .btn-fav {
        margin: 0;
        padding: 0.75rem;
        border-radius: 8px;
        border-color: #ddd;
    }
    
    /* Styles pour la barre de recherche */
    .search-grid {
        grid-template-columns: 1fr auto;
        gap: 1rem;
    }
</style>
@endsection