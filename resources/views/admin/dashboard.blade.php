@extends('layouts.app')

@section('title', 'Tableau de Bord Admin')

@section('content')
<style>
    /* Réduction de l'espacement global */
    .container { padding-top: 0.5rem !important; }

    /* Cartes de statistiques compactes */
    .stat-card {
        padding: 0.75rem !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        background: white;
        border-radius: 10px;
    }
    .stat-content { display: flex; align-items: center; gap: 0.75rem; }
    .stat-icon {
        width: 36px; height: 36px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }
    .stat-card h3 { margin: 0 !important; font-size: 1.2rem !important; font-weight: 700; line-height: 1; }
    .stat-card small { color: #6b7280; text-transform: uppercase; font-weight: 600; font-size: 0.65rem; }

    /* Tableau ultra-serré */
    table role="grid" th, 
    table role="grid" td {
        padding: 0.4rem 0.75rem !important;
        font-size: 0.85rem !important;
    }
    thead th { font-size: 0.75rem !important; text-transform: uppercase; }

    /* Boutons compacts */
    .outline {
        padding: 0.2rem 0.5rem !important;
        font-size: 0.7rem !important;
        margin: 0 !important;
        text-transform: none !important;
    }

    /* Alertes compactes */
    .import-errors {
        background-color: #fff1f2;
        border-left: 4px solid #e11d48;
        color: #9f1239;
        padding: 0.75rem;
        margin-bottom: 1rem;
        border-radius: 6px;
        font-size: 0.8rem;
    }

    header { margin-bottom: 1rem !important; }
    .grid { margin-bottom: 1.25rem !important; gap: 0.75rem !important; }
    tbody tr:hover { background-color: #f9fafb !important; }
</style>

<div class="container">
    {{-- Header compact --}}
    
    <header style="display: flex; justify-content: space-between; align-items: center;">
    <hgroup style="margin: 0;">
        <h1 style="font-size: 1.3rem; margin: 0;">Tableau de Bord 📊</h1>
        <p style="margin: 0; font-size: 0.75rem; color: #64748b;">Gestion de la bibliothèque.</p>
    </hgroup>
    <div style="display: flex; gap: 0.5rem;">
        {{-- Nouveau bouton d'inscription --}}
        <a href="{{ route('admin.users.create') }}" role="button" class="contrast" style="font-size: 0.7rem; padding: 0.4rem 0.8rem; margin: 0;">👤 Inscrire Membre</a>
        
        <a href="{{ route('books.import.view') }}" role="button" class="secondary" style="font-size: 0.7rem; padding: 0.4rem 0.8rem; margin: 0;">📥 Import CSV</a>
    </div>
</header>

    {{-- Notification Succès --}}
    @if (session('success'))
        <article style="background-color: #d1fae5; border-left: 4px solid #10b981; color: #065f46; padding: 0.75rem; margin-bottom: 1rem; border-radius: 6px; font-size: 0.85rem;">
            <strong>✅ Succès :</strong> {{ session('success') }}
        </article>
    @endif

    {{-- Notification Erreurs d'import --}}
    @if (session('skipped') && count(session('skipped')) > 0)
        <article class="import-errors">
            <details>
                <summary style="cursor: pointer; font-weight: 700;">⚠️ Lignes ignorées ({{ count(session('skipped')) }})</summary>
                <ul style="margin-top: 0.5rem; margin-bottom: 0;">
                    @foreach (session('skipped') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </details>
        </article>
    @endif

    {{-- Statistiques --}}
    <div class="grid">
        <article class="stat-card">
            <div class="stat-content">
                <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">📚</div>
                <div><small>Livres</small><h3>{{ $stats['total_books'] ?? 0 }}</h3></div>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-content">
                <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">👥</div>
                <div><small>Membres</small><h3>{{ $stats['total_users'] ?? 0 }}</h3></div>
            </div>
        </article>
        <article class="stat-card">
            <div class="stat-content">
                <div class="stat-icon" style="background: #fffbe3; color: #f59e0b;">🔄</div>
                <div><small>Emprunts</small><h3>{{ $stats['active_borrowings'] ?? 0 }}</h3></div>
            </div>
        </article>
    </div>
    
    {{-- Demandes d'emprunts en attente --}}
    <section style="margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem;">Demandes en attente ⏳</h3>
        <article style="padding: 0; overflow-x: auto; border-radius: 8px; border: 1px solid #e5e7eb; border-left: 4px solid #f59e0b;">
            <table role="grid" style="margin-bottom: 0;">
                <thead style="background: #fffbeb;">
                    <tr>
                        <th style="padding-left: 1rem;">Membre</th>
                        <th>Livre demandé</th>
                        <th style="text-align: right; padding-right: 1rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingLoans as $loan)
                    <tr>
                        <td style="padding-left: 1rem;"><strong>{{ $loan->user->name }}</strong></td>
                        <td style="color: #64748b;">{{ $loan->book->title }}</td>
                        <td style="text-align: right; padding-right: 1rem;">
                            <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                {{-- Bouton Valider --}}
                                <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="outline" style="border-color: #10b981; color: #10b981; font-weight: bold;">✅ Valider</button>
                                </form>
                                {{-- Bouton Refuser --}}
                                <form action="{{ route('admin.loans.reject', $loan->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="outline" style="border-color: #ef4444; color: #ef4444;" onclick="return confirm('Refuser cet emprunt ?')">❌</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 1rem; color: #94a3b8; font-style: italic;">Aucune demande en attente.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </article>
    </section>


    {{-- Liste des livres --}}
    <section>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <h3 style="margin: 0; font-size: 1rem;">Derniers livres</h3>
            <a href="{{ route('books.index') }}" style="font-size: 0.75rem;">Voir tout →</a>
        </div>
        
        <article style="padding: 0; overflow-x: auto; border-radius: 8px; border: 1px solid #e5e7eb;">
            <table role="grid" style="margin-bottom: 0;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding-left: 1rem;">Titre</th>
                        <th>Auteur</th>
                        <th style="text-align: center;">Stock</th>
                        <th style="text-align: right; padding-right: 1rem;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBooks as $book)
                    <tr> 
                        <td style="padding-left: 1rem;"><strong>{{ $book->title }}</strong></td>
                        <td style="color: #64748b;">{{ $book->author }}</td>
                        <td style="text-align: center;">
                            <span style="padding: 0.1rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 700; {{ $book->stock > 0 ? 'background: #d1fae5; color: #065f46;' : 'background: #fee2e2; color: #991b1b;' }}">
                                {{ $book->stock }}
                            </span>
                        </td>
                        <td style="text-align: right; padding-right: 1rem;">
                            <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                <a href="{{ route('books.edit', $book->id) }}" role="button" class="outline" style="border-color: #3b82f6; color: #3b82f6;">✏️</a>
                                <form action="{{ route('books.destroy', $book->id) }}" method="POST" style="margin: 0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="outline" style="border-color: #ef4444; color: #ef4444;" onclick="return confirm('Supprimer ?')">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align: center; padding: 2rem; color: #94a3b8;">Aucun livre.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </article>
    </section>
</div>
@endsection