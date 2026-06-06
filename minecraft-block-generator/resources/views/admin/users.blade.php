<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs — Minecraft Block Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.minecraft-font { font-family: 'Courier New', monospace; }</style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">

    <header class="bg-gray-800 border-b border-gray-700 py-4 px-6 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('block.index') }}" class="text-gray-400 hover:text-white transition-colors text-sm">← Retour</a>
                <h1 class="text-xl font-bold minecraft-font text-purple-400 flex items-center gap-2">
                    👥 Gestion des utilisateurs
                </h1>
            </div>
            <span class="text-xs text-gray-500 bg-gray-700 border border-gray-600 rounded-full px-3 py-1">
                Admin : {{ Auth::user()->name }}
            </span>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">

        @if (session('success'))
            <div class="bg-green-900/50 border border-green-500 rounded-lg p-4 mb-6 text-green-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-700 text-gray-400 text-xs uppercase tracking-wide">
                        <th class="text-left px-6 py-4">Identifiant</th>
                        <th class="text-left px-6 py-4">Nom</th>
                        <th class="text-left px-6 py-4">Email</th>
                        <th class="text-left px-6 py-4">Rôle</th>
                        <th class="text-left px-6 py-4">Créé le</th>
                        <th class="text-right px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-750 transition-colors {{ $user->id === Auth::id() ? 'bg-gray-700/30' : '' }}">
                            <td class="px-6 py-4 font-mono text-green-400 text-xs">
                                {{ $user->identifier }}
                                @if($user->id === Auth::id())
                                    <span class="text-gray-500 ml-1">(vous)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-white">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-300 text-xs">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if($user->role === 'admin')
                                    <span class="bg-purple-900/60 text-purple-300 border border-purple-700 text-xs rounded-full px-2 py-0.5 font-semibold">⭐ Admin</span>
                                @else
                                    <span class="bg-gray-700 text-gray-400 border border-gray-600 text-xs rounded-full px-2 py-0.5">👤 User</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all hover:scale-105">
                                        ✏️ Modifier
                                    </a>
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                              onsubmit="return confirm('Supprimer {{ $user->identifier }} ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-gray-700 hover:bg-red-600 text-gray-300 hover:text-white text-xs px-2.5 py-1.5 rounded-lg transition-all hover:scale-105">
                                                🗑
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-600 text-xs px-2.5 py-1.5">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-center">
            {{ $users->links('pagination::tailwind') }}
        </div>

    </main>
</body>
</html>
