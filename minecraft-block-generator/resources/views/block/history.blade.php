<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique — Minecraft Block Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .minecraft-font { font-family: 'Courier New', monospace; }
        
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(34, 197, 94, 0.3); }
            50% { box-shadow: 0 0 40px rgba(34, 197, 94, 0.6); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
        
        /* Card hover */
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
                <div class="w-12 h-12 bg-green-600 rounded-lg grid grid-cols-2 gap-0.5 p-1.5 animate-float shadow-lg">
                    <div class="bg-green-400 rounded-sm"></div>
                    <div class="bg-green-700 rounded-sm"></div>
                    <div class="bg-green-700 rounded-sm"></div>
                    <div class="bg-green-400 rounded-sm"></div>
                </div>
                <div>
                    <h1 class="text-xl font-bold minecraft-font text-green-400 flex items-center gap-2">
                        <span class="text-2xl">⛏️</span> Minecraft Block Generator
                    </h1>
                    <p class="text-xs text-gray-400">Historique des blocs générés</p>
                </div>
            </div>
            <a href="{{ route('block.new') }}"
               class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg flex items-center gap-2">
                <span>+</span> Nouveau bloc
            </a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">

        @if (session('success'))
            <div class="bg-green-900/50 border border-green-500 rounded-lg p-4 mb-6 text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if ($blocks->isEmpty())
            <div class="text-center py-20">
                <div class="text-8xl mb-6 animate-float">📦</div>
                <p class="text-gray-400 text-xl mb-2">Aucun bloc généré pour l'instant.</p>
                <p class="text-gray-600 text-sm mb-6">Commencez par créer votre premier bloc personnalisé !</p>
                <a href="{{ route('block.new') }}" class="inline-block bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-semibold px-6 py-3 rounded-lg transition-all hover:scale-105 hover:shadow-lg">
                    ➕ Créer mon premier bloc →
                </a>
            </div>
        @else
            <p class="text-gray-400 text-sm mb-6 flex items-center gap-2">
                <span>📊</span> {{ $blocks->total() }} bloc(s) généré(s)
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($blocks as $block)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden hover:border-green-600 transition-all card-hover">

                        <!-- Texture -->
                        <div class="bg-gray-900 h-40 flex items-center justify-center relative overflow-hidden">
                            @if (Storage::exists($block->texture_path))
                                <img
                                    src="{{ route('block.texture', $block->id) }}"
                                    alt="Texture {{ $block->name }}"
                                    class="w-28 h-28 object-contain"
                                    style="image-rendering: pixelated;"
                                >
                            @else
                                <div class="text-6xl opacity-30">🧱</div>
                            @endif
                            <!-- Overlay gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
                        </div>

                        <!-- Infos -->
                        <div class="p-5">
                            <h2 class="font-bold text-white text-lg truncate mb-1">{{ $block->name }}</h2>
                            <p class="text-green-400 text-xs font-mono mb-4">custom:{{ $block->identifier }}</p>

                            <div class="grid grid-cols-3 gap-2 text-xs mb-4">
                                <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                                    <div class="text-gray-400 mb-1">🧱 Solidité</div>
                                    <div class="{{ $block->solid ? 'text-green-400 font-semibold' : 'text-red-400' }}">
                                        {{ $block->solid ? 'Oui' : 'Non' }}
                                    </div>
                                </div>
                                <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                                    <div class="text-gray-400 mb-1">⛏️ Détruit.</div>
                                    <div class="{{ $block->destructible ? 'text-green-400 font-semibold' : 'text-red-400' }}">
                                        {{ $block->destructible ? 'Oui' : 'Non' }}
                                    </div>
                                </div>
                                <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                                    <div class="text-gray-400 mb-1">💪 Résist.</div>
                                    <div class="text-white font-bold">{{ $block->resistance }}</div>
                                </div>
                            </div>

                            <p class="text-gray-600 text-xs mb-4 flex items-center gap-2">
                                <span>📅</span> Créé le {{ $block->created_at->format('d/m/Y à H:i') }}
                            </p>

                            <div class="flex gap-2">
                                <a href="{{ route('block.edit', $block->id) }}"
                                   class="flex-1 bg-blue-600 hover:bg-blue-500 text-white text-center text-sm font-semibold py-2.5 rounded-lg transition-all hover:scale-[1.02] hover:shadow-lg">
                                    ✏️ Modifier
                                </a>
                                <a href="{{ route('block.download', $block->id) }}"
                                   class="flex-1 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white text-center text-sm font-semibold py-2.5 rounded-lg transition-all hover:scale-[1.02] hover:shadow-lg">
                                    ⬇ Télécharger
                                </a>
                                <form action="{{ route('block.destroy', $block->id) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce bloc ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-gray-700 hover:bg-red-600 text-gray-300 hover:text-white px-4 py-2.5 rounded-lg transition-all hover:scale-105 text-sm">
                                        🗑
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10 flex justify-center">
                {{ $blocks->links('pagination::tailwind') }}
            </div>
        @endif
    </main>

    <footer class="text-center text-gray-500 text-xs py-8 mt-8 border-t border-gray-800">
        <div class="flex items-center justify-center gap-2 mb-2">
            <span>⛏️</span>
            <span>Minecraft Block Generator</span>
            <span>—</span>
            <span>Bedrock Edition</span>
            <span>•</span>
            <span>Laravel {{ app()->version() }}</span>
        </div>
        <p class="text-gray-600">Créé avec ❤️ pour la communauté Minecraft</p>
    </footer>

</body>
</html>
