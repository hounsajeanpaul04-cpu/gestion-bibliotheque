<!DOCTYPE html>
<html>
<head>
    <title>Rappel de prêt</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Bonjour {{ $loan->user->name }},</h2>

    <p>Ceci est un message automatique pour vous informer que le prêt suivant est arrivé à échéance :</p>
    
    <ul>
        <li><strong>Livre :</strong> {{ $loan->book->title }}</li>
        <li><strong>Date de retour prévue :</strong> {{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}</li>
    </ul>

    <p>Merci de rapporter l'ouvrage à la bibliothèque dans les plus brefs délais.</p>

    <p>Cordialement,<br>
    L'équipe de la Bibliothèque</p>
</body>
</html>