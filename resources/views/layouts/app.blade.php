<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque - @yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
    <style>
        nav { margin-bottom: 2rem; border-bottom: 1px solid #e1e1e1; padding: 10px 20px; }
        .admin-link { color: #d93526; font-weight: bold; }
        footer { text-align: center; margin-top: 3rem; padding: 2rem; background: #f9f9f9; }
        nav ul li form { margin: 0; padding: 0; }
        
        /* Styles pour les messages flash plus visibles */
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <nav class="container-fluid">
        <ul>
            <li><strong>📚 Ma Biblio</strong></li>
        </ul>
        <ul>
            <li><a href="{{ url('/') }}">Accueil</a></li>
            <li><a href="{{ route('books.index') }}">Catalogue</a></li>
            
            @auth
                @if(Auth::user()->is_admin)
                    <li><a href="{{ route('admin.dashboard') }}" class="admin-link">📊 Dashboard</a></li>
                    <li><a href="{{ route('books.import.view') }}">📥 Import CSV</a></li>
                @endif
                <li><a href="{{ route('profile.edit') }}">👤 Mon Profil</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="outline secondary" style="padding: 5px 10px; font-size: 0.8rem;">Quitter</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}" class="contrast">Connexion</a></li>
            @endauth
        </ul>
    </nav>

    <main class="container">
        @if(session('success'))
            <article class="alert alert-success">
                ✅ {{ session('success') }}
            </article>
        @endif

        @if(session('error'))
            <article class="alert alert-error">
                ❌ {{ session('error') }}
            </article>
        @endif

        @yield('content')
    </main>

    <footer>
        <p>&copy; 2026 - Système de Gestion de Bibliothèque v1.0</p>
    </footer>

</body>
</html>