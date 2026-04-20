@extends('layouts.app')

@section('title', 'Importation Massive')

@section('content')
<style>
    :root {
        --primary-blue: #008cc9;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
    }

    /* On remonte tout au maximum */
    .import-container {
        padding-top: 0 !important;
        margin-top: -15px; /* Ajuste selon ton menu pour coller au haut */
    }

    .import-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
        overflow: hidden;
        margin-top: 0;
    }

    /* Header très compact */
    .import-header {
        padding: 0.7rem 1.2rem; 
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center; 
    }

    .title-group h2 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .title-group small {
        color: #64748b;
        font-size: 0.75rem;
    }

    .template-link {
        display: flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        color: #475569;
        background: #f1f5f9;
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-size: 0.75rem;
    }

    /* Zone de dépôt ultra-serrée */
    .drop-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 1rem; /* Minimum de padding */
        margin: 0.8rem 1.2rem;
        text-align: center;
        background-color: var(--bg-light);
        cursor: pointer;
    }

    .drop-zone svg {
        width: 32px;
        height: 32px;
        margin-bottom: 0.3rem;
    }

    .btn-import {
        background-color: var(--primary-blue) !important;
        color: white !important;
        padding: 0.7rem !important;
        width: 100%;
        border: none;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9rem;
    }
</style>

<div class="container import-container">
    {{-- Fil d'Ariane réduit au minimum --}}
    <nav aria-label="breadcrumb" style="margin-bottom: 0.2rem; font-size: 0.8rem;">
        <ul style="padding: 0; margin: 0;">
            <li><a href="{{ route('admin.dashboard') }}">Admin</a> / <a href="{{ route('books.index') }}">Livres</a> / <strong>Import</strong></li>
        </ul>
    </nav>

    <article class="import-card">
        <header class="import-header">
            <div class="title-group">
                <h2>📥 Import Massive</h2>
                <small>Mise à jour via CSV</small>
            </div>
            
            <a href="{{ route('books.download-template') }}" class="template-link">
                <span>💾</span>
                <span>Modèle .csv</span>
            </a>
        </header>

        <form action="{{ route('books.import.process') }}" method="POST" enctype="multipart/form-data" style="margin: 0;">
            @csrf
            <div class="drop-zone" id="drop-zone-click" onclick="document.getElementById('csv_file').click()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--primary-blue);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p id="drop-zone-text" style="font-weight: 600; color: #1e293b; margin: 0; font-size: 0.85rem;">Cliquer ou glisser le fichier</p>
                
                <input type="file" name="file" id="csv_file" hidden required onchange="updateFileName(this)">
            </div>

            <button type="submit" class="btn-import">
                Démarrer l'importation
            </button>
        </form>
    </article>
</div>

<script>
    function updateFileName(input) {
        if (input.files.length > 0) {
            const fileName = input.files[0].name;
            const textDisplay = document.getElementById('drop-zone-text');
            const zone = document.getElementById('drop-zone-click');
            
            textDisplay.innerHTML = "✅ " + fileName;
            zone.style.borderColor = "#16a34a";
            zone.style.backgroundColor = "#f0fdf4";
        }
    }
</script>
@endsection