<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Compte créé</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
</head>
<body class="text-gray-100 min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-md">
        <div class="mb-8">@include('partials.brand-mark')</div>
        <p class="pal-eyebrow mb-2">Bienvenue</p>
        <h1 class="text-4xl font-extrabold text-white mb-8">Compte créé !</h1>

        <div class="bg-gray-800 border border-green-600/70 rounded-lg p-7" style="box-shadow: 0 0 40px rgba(255,106,0,.18)">
            <p class="text-gray-300 mb-3">Voici votre identifiant unique à conserver :</p>
            <div class="bg-gray-900 border border-gray-700 rounded px-4 py-3 font-mono text-lg text-green-400 mb-3">{{ $identifier }}</div>
            <p class="text-sm text-gray-500 mb-7">Utilisez cet identifiant pour vous reconnecter plus tard.</p>
            <a href="{{ route('block.new') }}" class="inline-block bg-green-600 text-white font-bold px-7 py-3 rounded">Créer un bloc</a>
        </div>
    </div>
</body>
</html>
