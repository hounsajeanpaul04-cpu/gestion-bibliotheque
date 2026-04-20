@extends('layouts.app')

@section('content')
<div style="background-color: #f8fafc; padding: 2rem 0; min-height: 80vh;">
    <div style="max-width: 600px; margin: 0 auto; padding: 0 1rem;">
        
        <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            
            <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 1.25rem; color: #1e293b; font-weight: 700;">Mon Profil</h2>
                <span style="background: #e0e7ff; color: #4338ca; padding: 4px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                    Compte Actif
                </span>
            </div>

            <div style="padding: 1.5rem;">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid #f8fafc;">
                        <span style="color: #64748b; font-size: 0.875rem;">Nom complet</span>
                        <span style="color: #0f172a; font-weight: 600; font-size: 0.875rem;">{{ Auth::user()->name }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem;">
                        <span style="color: #64748b; font-size: 0.875rem;">Adresse Email</span>
                        <span style="color: #0f172a; font-weight: 600; font-size: 0.875rem;">{{ Auth::user()->email }}</span>
                    </div>
                </div>

               <div style="margin-top: 2rem; text-align: center;">
                 <a href="{{ route('loans.history') }}" 
                 style="display: inline-block; padding: 12px 24px; background-color: #4f46e5; color: white; text-align: center; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 0.875rem; transition: background-color 0.2s; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'librarian')
                📋 GESTION GLOBALE DES EMPRUNTS
                 @else
                 📋 VOIR MON HISTORIQUE D'EMPRUNTS
                   @endif
                </a>
              </div>
            </div>
        </div>

    </div>
</div>
@endsection