<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier {{ $user->identifier }} — Minecraft Block Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.minecraft-font { font-family: 'Courier New', monospace; }</style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">

    <header class="bg-gray-800 border-b border-gray-700 py-4 px-6 sticky top-0 z-40">
        <div class="max-w-xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users') }}" class="text-gray-400 hover:text-white transition-colors text-sm">← Utilisateurs</a>
                <h1 class="text-xl font-bold minecraft-font text-purple-400">
                    ✏️ Modifier {{ $user->identifier }}
                </h1>
            </div>
        </div>
    </header>

    <main class="max-w-xl mx-auto px-4 py-8">

        @if ($errors->any())
            <div class="bg-red-900/50 border border-red-500 rounded-lg p-4 mb-6 text-red-300 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">

            <div class="mb-5 p-3 bg-gray-700/50 rounded-lg text-sm text-gray-400">
                Identifiant : <span class="text-green-400 font-mono">{{ $user->identifier }}</span>
                <span class="text-gray-600 ml-2">(non modifiable)</span>
            </div>

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Nom</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="50"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Rôle</label>
                    <select name="role"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                        <option value="user"  {{ old('role', $user->role) === 'user'  ? 'selected' : '' }}>👤 User</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>⭐ Admin</option>
                    </select>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white font-bold py-3 px-6 rounded-xl transition-all hover:scale-[1.02] minecraft-font">
                    ✅ Enregistrer
                </button>
            </form>
        </div>

    </main>
</body>
</html>
