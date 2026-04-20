@extends('layouts.app')

@section('content')
<style>
    /* Structure principale et centrage */
    .p-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 90vh;
        background-color: #f8fafc; /* Gris très léger de fond */
        padding: 2rem;
    }

    /* Carte du formulaire professionnelle */
    .p-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        width: 100%;
        max-width: 600px;
        padding: 2rem;
    }

    /* Header de la carte */
    .p-card-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .p-card-title {
        color: #1a202c; /* Gris très foncé presque noir */
        font-weight: 700;
        font-size: 1.25rem;
        margin: 0;
    }

    /* Espacement entre les champs */
    .p-form-group { margin-bottom: 1.25rem; }

    /* Label professionnel */
    .p-label {
        display: block;
        font-weight: 600;
        font-size: 0.8rem;
        color: #4a5568; /* Gris moyen pour le texte */
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Input text et email professionnel */
    .p-input {
        width: 100%;
        padding: 0.65rem 0.75rem;
        border: 1px solid #e2e8f0; /* Gris clair pour la bordure */
        border-radius: 8px;
        font-size: 0.9rem;
        color: #1a202c;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .p-input:focus {
        outline: none;
        border-color: #3182ce; /* Bleu "focus" professionnel */
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
    }

    .p-input::placeholder { color: #a0aec0; } /* Gris clair pour les placeholders */

    /* Grille pour les mots de passe (plus compact) */
    .p-grid-passwords {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    /* Le Select professionnel */
    .p-select {
        appearance: none; /* Supprime le style par défaut de l'OS */
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2364748b'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E") no-repeat right 0.75rem center/16px 16px;
        background-color: white;
    }

    /* Le Bouton d'action pro */
    .p-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        padding: 0.7rem 1.25rem;
        background-color: #3182ce; /* Bleu corporatif */
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: background-color 0.15s ease;
        margin-top: 1rem;
    }

    .p-btn:hover { background-color: #2b6cb0; } /* Bleu plus sombre au hover */

    .p-btn:active { background-color: #2c5282; }

    /* Petit texte d'aide */
    .p-help-text {
        font-size: 0.75rem;
        color: #718096;
        margin-top: -0.8rem;
        margin-bottom: 1.2rem;
        display: block;
    }
</style>

<div class="p-container">
    <article class="p-card">
        <div class="p-card-header">
            <h2 class="p-card-title">Enregistrement d'un nouveau membre 👤</h2>
        </div>
        
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- Nom --}}
            <div class="p-form-group">
                <label for="name" class="p-label">Identité complète</label>
                <input type="text" id="name" name="name" class="p-input" placeholder="Ex: M. Jean DUPONT" required>
            </div>

            {{-- Email --}}
            <div class="p-form-group">
                <label for="email" class="p-label">Adresse électronique professionnelle</label>
                <input type="email" id="email" name="email" class="p-input" placeholder="jean.dupont@entreprise.com" required>
            </div>

            {{-- Mots de passe compacts --}}
            <div class="p-grid-passwords p-form-group">
                <div>
                    <label for="password" class="p-label">Mot de passe provisoire</label>
                    <input type="password" id="password" name="password" class="p-input" required>
                </div>
                <div>
                    <label for="password_confirmation" class="p-label">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="p-input" required>
                </div>
            </div>
            <small class="p-help-text">L'utilisateur pourra modifier ce mot de passe lors de sa première connexion.</small>

            {{-- Rôle --}}
            <div class="p-form-group">
                <label for="role" class="p-label">Rôle d'administration</label>
                <select id="role" name="role" class="p-input p-select">
                    <option value="user" selected>Utilisateur Standard (Lecteur)</option>
                    <option value="librarian">Bibliothécaire</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>

            <button type="submit" class="p-btn">
                Finaliser l'enregistrement du compte
            </button>
        </form>
    </article>
</div>
@endsection