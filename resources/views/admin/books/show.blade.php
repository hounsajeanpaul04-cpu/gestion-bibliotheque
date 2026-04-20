@extends('layouts.app')

@section('title', 'Détails du livre')

@section('content')
<div class="container">
    <header style="margin-bottom: 2rem;">
        <hgroup>
            <h1>Fiche détaillée 📖</h1>
            <p>Consultation de l'ouvrage : <strong>{{ $book->title }}</strong></p>
        </hgroup>
        <a href="{{ route('admin.dashboard') }}" role="button" class="outline secondary" style="font-size: 0.8rem;">← Retour au Dashboard</a>
    </header>

    <article style="border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div class="grid">
            <div>
                <h2 style="color: var(--pico-primary);">{{ $book->title }}</h2>
                <p><strong>Auteur :</strong> {{ $book->author }}</p>
                <p><strong>ISBN :</strong> <code>{{ $book->isbn }}</code></p>
                <p><strong>Stock actuel :</strong> 
                    <span style="padding: 0.2rem 0.6rem; border-radius: 20px; {{ $book->stock > 0 ? 'background: #d1fae5; color: #065f46;' : 'background: #fee2e2; color: #991b1b;' }}">
                        {{ $book->stock }} exemplaires
                    </span>
                </p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px; justify-content: center;">
                <a href="{{ route('books.edit', $book->id) }}" role="button" class="primary">✏️ Modifier</a>
                
                <form action="{{ route('books.destroy', $book->id) }}" method="POST" style="margin: 0; width: 100%;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="outline" style="border-color: #ef4444; color: #ef4444; width: 100%;" onclick="return confirm('Supprimer ce livre ?')">
                        🗑️ Supprimer
                    </button>
                </form>
            </div>
        </div>
    </article>
</div>
@endsection