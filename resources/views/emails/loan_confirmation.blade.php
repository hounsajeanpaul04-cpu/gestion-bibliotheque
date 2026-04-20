<h2>Bonjour {{ $loan->user->name }}</h2>

<p>Votre emprunt a été confirmé :</p>

<ul>
    <li>Livre : {{ $loan->book->title }}</li>
    <li>Date de retour : {{ $loan->due_date }}</li>
</ul>

<p>Merci d'utiliser notre bibliothèque 📚</p>