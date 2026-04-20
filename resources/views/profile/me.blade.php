
@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
    <h2>Bienvenue, {{ Auth::user()->name }} !</h2>
    <div class="grid">
        <article>
            <header>Mes Informations</header>
            <p>Email : {{ Auth::user()->email }}</p>
            <p>Rôle : {{ Auth::user()->is_admin ? 'Administrateur' : 'Membre' }}</p>
        </article>
        
        <article>
            <header>Mes Emprunts</header>
            <p>Tu as actuellement <strong>{{ Auth::user()->borrowings->count() }}</strong> livres en main.</p>
        </article>
    </div>
@endsection