<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { color: #3182ce; font-size: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Bonne nouvelle ! 📚</div>
        <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
        <p>Votre demande d'emprunt pour le livre <strong>"{{ $book->title }}"</strong> a été validée.</p>
        <p>Vous pouvez venir le récupérer à la bibliothèque dès aujourd'hui.</p>
        <hr>
        <p style="font-size: 12px; color: #718096;">Ceci est un message automatique, merci de ne pas y répondre.</p>
    </div>
</body>
</html>