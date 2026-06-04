<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Compte créé</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-xl border border-gray-700 w-full max-w-md text-center">
        <h1 class="text-2xl font-bold mb-4">Compte créé</h1>
        <p class="mb-4">Voici votre identifiant unique à conserver :</p>
        <div class="bg-gray-900 border border-gray-700 rounded p-3 font-mono text-green-300 mb-4">{{ $identifier }}</div>
        <p class="text-sm text-gray-400 mb-4">Utilisez cet identifiant pour vous reconnecter plus tard.</p>
        <a href="{{ route('block.new') }}" class="bg-green-600 px-4 py-2 rounded text-white">Créer un bloc</a>
    </div>
</body>
</html>
