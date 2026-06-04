<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Connexion ou Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-xl border border-gray-700 w-full max-w-md text-center">
        <h1 class="text-2xl font-bold mb-4">Veuillez vous connecter ou créer un compte</h1>
        <div class="space-y-3">
            <a href="{{ route('auth.login') }}" class="block bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded">Connexion</a>
            <a href="{{ route('auth.register') }}" class="block bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded">Inscription</a>
            <a href="{{ route('block.index') }}" class="inline-block text-sm text-gray-400 mt-3">Retour à la liste</a>
        </div>
    </div>
</body>
</html>
