<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Connexion ou Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
</head>
<body class="text-gray-100 min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-lg">
        <div class="mb-10">@include('partials.brand-mark')</div>
        <p class="pal-eyebrow mb-3">Espace créateur</p>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-[1.05] mb-5">Commencez<br>votre création !</h1>
        <p class="text-gray-400 mb-10 max-w-md">Connectez-vous ou créez un compte pour générer vos blocs, items et mobs Minecraft Bedrock et retrouver toutes vos créations.</p>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('auth.login') }}" class="bg-green-600 text-white font-bold px-8 py-3.5 rounded">Connexion</a>
            <a href="{{ route('auth.register') }}" class="bg-gray-700 font-bold px-8 py-3.5 rounded">Inscription</a>
        </div>
        <a href="{{ route('block.index') }}" class="inline-block text-sm text-gray-500 hover:text-white mt-10 transition-colors">← Retour à la liste</a>
    </div>
</body>
</html>
