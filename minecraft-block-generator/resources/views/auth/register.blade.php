<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-xl border border-gray-700 w-full max-w-md">
        <h1 class="text-xl font-bold mb-4">Créer un compte</h1>

        @if ($errors->any())
            <div class="bg-red-900/50 p-3 rounded mb-3">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('auth.register.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-sm text-gray-300">Identifiant (lettres et chiffres, 3-20 car)</label>
                <input type="text" name="identifier" value="{{ old('identifier') }}" class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600 font-mono" placeholder="mon_identifiant" />
                @error('identifier')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm text-gray-300">Mot de passe</label>
                <input type="password" name="password" class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600" />
                @error('password')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-300">Confirmer mot de passe</label>
                <input type="password" name="password_confirmation" class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600" />
            </div>
            <div class="flex gap-3">
                <button class="bg-blue-600 px-4 py-2 rounded text-white">S'inscrire</button>
                <a href="{{ route('auth.choice') }}" class="px-4 py-2 rounded border border-gray-600 text-gray-200">Retour</a>
            </div>
        </form>
    </div>
</body>
</html>
