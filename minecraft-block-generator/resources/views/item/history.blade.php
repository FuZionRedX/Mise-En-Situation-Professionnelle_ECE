<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste d'Items — Minecraft Item Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .minecraft-font { font-family: 'Courier New', monospace; }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }

        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">

    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 py-4 px-6 sticky top-0 z-40 backdrop-blur-md bg-opacity-95">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center text-2xl animate-float shadow-lg">
                    🎁
                </div>
                <div>
                    <h1 class="text-xl font-bold minecraft-font text-green-400 flex items-center gap-2">
                        <span class="text-2xl">🎁</span> Minecraft Item Generator
                    </h1>
                    <p class="text-xs text-gray-400">Items générés</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.users') }}"
                           class="bg-purple-700 hover:bg-purple-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg flex items-center gap-2">
                            👥 Utilisateurs
                        </a>
                    @endif
                    <button onclick="document.getElementById('logout-modal').classList.remove('hidden')"
                            class="bg-red-600 hover:bg-red-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg">
                        Déconnexion
                    </button>
                @endauth

                <a href="{{ route('item.new') }}"
                   class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg flex items-center gap-2">
                    <span>+</span> Nouvel item
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">

        @if (session('success'))
            <div class="bg-green-900/50 border border-green-500 rounded-lg p-4 mb-6 text-green-300">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter -->
        @auth
        <div class="flex justify-center mb-8">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-1.5 inline-flex gap-1 shadow-lg">
                <a href="{{ route('item.index') }}"
                   class="px-5 py-2 text-sm font-semibold rounded-lg transition-all {{ !$mine ? 'bg-green-600 text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                    Tous les items
                </a>
                <a href="?filter=mine"
                   class="px-5 py-2 text-sm font-semibold rounded-lg transition-all {{ $mine ? 'bg-green-600 text-white shadow-md' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                    👤 Mes créations
                </a>
            </div>
        </div>
        @endauth

        <!-- Items Section -->
        <div class="flex items-center gap-3 mb-6">
            <span class="text-2xl">🎁</span>
            <h2 class="text-lg font-bold text-white minecraft-font">Items</h2>
            <span class="text-xs text-gray-500 bg-gray-800 border border-gray-700 rounded-full px-2 py-0.5">{{ $items->total() }}</span>
        </div>

        @if ($items->isEmpty())
            <div class="text-center py-12 bg-gray-800/40 rounded-xl border border-gray-700 mb-12">
                <div class="text-6xl mb-4 animate-float">📦</div>
                <p class="text-gray-400 text-base mb-1">Aucun item généré pour l'instant.</p>
                <p class="text-gray-600 text-sm mb-4">Commencez par créer votre premier item personnalisé !</p>
                <a href="{{ route('item.new') }}" class="inline-block bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-semibold px-5 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg text-sm">
                    ➕ Créer mon premier item →
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach ($items as $item)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden hover:border-green-600 transition-all card-hover"
                         data-id="{{ $item->id }}">

                        <!-- Texture -->
                        <div class="bg-gray-900 h-40 flex items-center justify-center relative overflow-hidden">
                            @if (Storage::exists($item->texture_path))
                                <img
                                    src="{{ route('item.texture', $item->id) }}"
                                    alt="Texture {{ $item->name }}"
                                    class="w-24 h-24 object-contain"
                                    style="image-rendering: pixelated;"
                                >
                            @else
                                <div class="text-6xl opacity-30">🎁</div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
                        </div>

                        <!-- Infos -->
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-1">
                                <h2 class="font-bold text-white text-lg truncate">{{ $item->name }}</h2>
                            </div>
                            <p class="text-gray-400 text-xs mb-3">
                                <code class="text-green-400">custom:{{ $item->identifier }}</code>
                            </p>
                            <p class="text-gray-500 text-xs mb-4">
                                {{ $item->created_at->translatedFormat('d M Y à H:i') }}
                                @if($item->creator_identifier)
                                    <br><span class="text-gray-600">par {{ $item->creator_identifier }}</span>
                                @endif
                            </p>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('item.download', $item->id) }}"
                                   class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition-all text-sm text-center">
                                    ⬇️ Télécharger
                                </a>
                                @auth
                                    @if(Auth::user()->identifier === $item->creator_identifier || Auth::user()->isAdmin())
                                        <a href="{{ route('item.edit', $item->id) }}"
                                           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-3 rounded-lg transition-all text-sm">
                                            ✏️
                                        </a>
                                        <button onclick="confirmDelete({{ $item->id }}, '{{ $item->name }}')"
                                                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-3 rounded-lg transition-all text-sm">
                                            🗑️
                                        </button>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $items->links('pagination::tailwind') }}
            </div>
        @endif
    </main>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 max-w-sm">
            <h2 class="text-lg font-bold text-white mb-2">Supprimer l'item ?</h2>
            <p class="text-gray-400 mb-6">
                Êtes-vous sûr de vouloir supprimer <span id="delete-name" class="font-semibold text-green-400"></span> ?
                Cette action est irréversible.
            </p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('delete-modal').classList.add('hidden')"
                        class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 rounded-lg transition-all">
                    Annuler
                </button>
                <button id="confirm-delete-btn"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-all">
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 max-w-sm">
            <h2 class="text-lg font-bold text-white mb-2">Déconnexion</h2>
            <p class="text-gray-400 mb-6">Êtes-vous sûr de vouloir vous déconnecter ?</p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('logout-modal').classList.add('hidden')"
                        class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 rounded-lg transition-all">
                    Annuler
                </button>
                <form method="POST" action="{{ route('auth.logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-all">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(itemId, itemName) {
            document.getElementById('delete-name').textContent = itemName;
            document.getElementById('delete-modal').classList.remove('hidden');

            document.getElementById('confirm-delete-btn').onclick = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("item.destroy", ":id") }}'.replace(':id', itemId);
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            };
        }
    </script>
</body>
</html>
