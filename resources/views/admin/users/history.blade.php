@extends('layouts.app')

@section('title', 'Historique des Emprunts')

@section('content')
<article style="max-width: 1000px; margin: 2rem auto; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-radius: 16px;">
    <header style="background-color: #f8fafc; padding: 1.5rem; border-radius: 16px 16px 0 0; border-bottom: 1px solid #e2e8f0;">
        <h2 style="margin: 0; font-size: 1.5rem; color: #1e293b;">📋 {{ Auth::user()->role === 'admin' ? 'Gestion Globale des Emprunts' : 'Mon Historique d\'Emprunts' }}</h2>
        <p style="margin: 0; color: #64748b;">
            {{ Auth::user()->role === 'admin' ? 'Liste complète des activités de la bibliothèque.' : 'Retrouvez ici l\'état de vos demandes et vos lectures passées.' }}
        </p>
    </header>

    <div class="overflow-auto" style="padding: 1rem;">
        <table class="striped">
            <thead>
                <tr>
                    @if(Auth::user()->role === 'admin')
                        <th scope="col">Emprunteur</th>
                    @endif
                    <th scope="col">Livre</th>
                    <th scope="col">Date</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Pénalité</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $loan)
                    <tr>
                        {{-- Colonne Emprunteur (Admin seulement) --}}
                        @if(Auth::user()->role === 'admin')
                            <td style="vertical-align: middle;">
                                <strong>{{ $loan->user->name }}</strong><br>
                                <small>{{ $loan->user->email }}</small>
                            </td>
                        @endif

                        <td style="vertical-align: middle;">
                            <strong style="color: #1e293b;">{{ $loan->book->title }}</strong><br>
                            <small style="color: #64748b;">{{ $loan->book->author }}</small>
                        </td>
                        
                        <td style="vertical-align: middle;">
                            <small>Demande : {{ $loan->created_at->format('d/m/Y') }}</small>
                            @if($loan->returned_at)
                                <br><small style="color: #166534;">Rendu : {{ $loan->returned_at->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        
                        <td style="vertical-align: middle;">
                            @if($loan->status == 'pending')
                                <span style="background: #fffbeb; color: #9a3412; padding: 6px 14px; border-radius: 99px; font-size: 0.75rem; font-weight: bold; border: 1px solid #fed7aa;">⏳ En attente</span>
                            @elseif($loan->status == 'approved' || $loan->status == 'active')
                                {{-- Vérification si en retard même si approuvé --}}
                                @if($loan->due_date < now() && !$loan->returned_at)
                                    <span style="background: #fef2f2; color: #991b1b; padding: 6px 14px; border-radius: 99px; font-size: 0.75rem; font-weight: bold; border: 1px solid #fecaca;">⚠️ Retard</span>
                                @else
                                    <span style="background: #f0fdf4; color: #166534; padding: 6px 14px; border-radius: 99px; font-size: 0.75rem; font-weight: bold; border: 1px solid #bbf7d0;">✅ Validé</span>
                                @endif
                            @elseif($loan->status == 'rejected')
                                <span style="background: #fef2f2; color: #991b1b; padding: 6px 14px; border-radius: 99px; font-size: 0.75rem; font-weight: bold; border: 1px solid #fecaca;">❌ Refusé</span>
                            @else
                                <span style="background: #f1f5f9; color: #475569; padding: 6px 14px; border-radius: 99px; font-size: 0.75rem; font-weight: bold; border: 1px solid #e2e8f0;">📁 Rendu</span>
                            @endif
                        </td>

                        {{-- Affichage de la pénalité --}}
                          <td style="vertical-align: middle; font-weight: bold;">
                           @php 
                           $currentPenalty = ($loan->status == 'returned') ? $loan->penalty : $loan->calculatePenalty(); 
                           @endphp

                             @if($currentPenalty > 0)
                             <span style="color: #991b1b;">{{ number_format($currentPenalty, 0, ',', ' ') }} FCFA</span>
                             @else
                             <span style="color: #166534;">0 FCFA</span>
                             @endif
                            </td>
                        <td style="vertical-align: middle;">
                            @if($loan->status === 'approved' || $loan->status === 'active')
                             <a href="{{ route('books.show', $loan->book_id) }}" role="button" class="outline" style="font-size: 0.65rem; padding: 0.3rem 0.6rem; margin: 0;">
                                    Consulter
                                </a>
                            @else
                                <small style="color: #94a3b8;">Aucune action</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ Auth::user()->role === 'admin' ? '6' : '5' }}" style="text-align: center; padding: 3rem; color: #94a3b8;">
                            Aucun historique trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</article>
@endsection