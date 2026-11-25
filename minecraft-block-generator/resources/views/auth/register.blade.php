<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
</head>
<body class="text-gray-100 min-h-screen flex items-center justify-center px-6 py-10">
    <div class="w-full max-w-md">
        <div class="mb-8">@include('partials.brand-mark')</div>
        <p class="pal-eyebrow mb-2">Rejoignez l'atelier</p>
        <h1 class="text-4xl font-extrabold text-white mb-8">Créer un compte</h1>

        <div class="bg-gray-800 border border-gray-700 rounded-lg p-7">
            @if ($errors->any())
                <div class="bg-red-900/40 border border-red-700 text-red-300 text-sm p-3 rounded mb-5">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('auth.register.post') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-300 mb-1.5">Identifiant <span class="text-gray-500 font-normal">(lettres et chiffres, 3-20 car.)</span></label>
                    <input type="text" name="identifier" value="{{ old('identifier') }}" class="w-full px-4 py-3 rounded border font-mono" placeholder="mon_identifiant" />
                    @error('identifier')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-300 mb-1.5">Mot de passe</label>
                    <input type="password" name="password" class="w-full px-4 py-3 rounded border" />
                    @error('password')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-300 mb-1.5">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-3 rounded border" />
                </div>
                <div class="flex flex-wrap gap-3">
                    <button class="bg-green-600 text-white font-bold px-7 py-3 rounded">S'inscrire</button>
                    <a href="{{ route('auth.choice') }}" class="bg-gray-700 font-bold px-7 py-3 rounded">Retour</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
