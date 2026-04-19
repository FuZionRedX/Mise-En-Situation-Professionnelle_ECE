<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique — Minecraft Block Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.minecraft-font { font-family: 'Courier New', monospace; }</style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">

    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 py-4 px-6">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-600 rounded grid grid-cols-2 gap-0.5 p-1">
                    <div class="bg-green-400 rounded-sm"></div>
                    <div class="bg-green-700 rounded-sm"></div>
                    <div class="bg-green-700 rounded-sm"></div>
                    <div class="bg-green-400 rounded-sm"></div>
                </div>
                <div>
                    <h1 class="text-xl font-bold minecraft-font text-green-400">Minecraft Block Generator</h1>
                    <p class="text-xs text-gray-400">Historique des blocs générés</p>
                </div>
            </div>
            <a href="{{ route('block.new') }}"
               class="bg-green-600 hover:bg-green-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                + Nouveau bloc
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
                <div class="text-6xl mb-4">📦</div>
                <p class="text-gray-400 text-lg">Aucun bloc généré pour l'instant.</p>
                <a href="{{ route('block.new') }}" class="mt-4 inline-block text-green-400 hover:underline">
                    Créer mon premier bloc →
                </a>
            </div>
        @else
            <p class="text-gray-400 text-sm mb-6">{{ $blocks->total() }} bloc(s) généré(s)</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($blocks as $block)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden hover:border-green-600 transition-colors">

                        <!-- Texture -->
                        <div class="bg-gray-900 h-36 flex items-center justify-center">
                            @if (Storage::exists($block->texture_path))
                                <img
                                    src="{{ route('block.texture', $block->id) }}"
                                    alt="Texture {{ $block->name }}"
                                    class="w-24 h-24 object-contain"
                                    style="image-rendering: pixelated;"
                                >
                            @else
                                <div class="text-4xl opacity-30">🧱</div>
                            @endif
                        </div>

                        <!-- Infos -->
                        <div class="p-4">
                            <h2 class="font-bold text-white text-lg truncate">{{ $block->name }}</h2>
                            <p class="text-green-400 text-xs font-mono mb-3">custom:{{ $block->identifier }}</p>

                            <div class="grid grid-cols-3 gap-2 text-xs mb-4">
                                <div class="bg-gray-700 rounded p-2 text-center">
                                    <div class="text-gray-400 mb-0.5">Solidité</div>
                                    <div class="{{ $block->solid ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $block->solid ? 'Oui' : 'Non' }}
                                    </div>
                                </div>
                                <div class="bg-gray-700 rounded p-2 text-center">
                                    <div class="text-gray-400 mb-0.5">Détruit.</div>
                                    <div class="{{ $block->destructible ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $block->destructible ? 'Oui' : 'Non' }}
                                    </div>
                                </div>
                                <div class="bg-gray-700 rounded p-2 text-center">
                                    <div class="text-gray-400 mb-0.5">Résist.</div>
                                    <div class="text-white font-bold">{{ $block->resistance }}</div>
                                </div>
                            </div>

                            <p class="text-gray-600 text-xs mb-4">
                                Créé le {{ $block->created_at->format('d/m/Y à H:i') }}
                            </p>

                            <div class="flex gap-2">
                                <a href="{{ route('block.download', $block->id) }}"
                                   class="flex-1 bg-green-600 hover:bg-green-500 text-white text-center text-sm font-semibold py-2 rounded-lg transition-colors">
                                    ⬇ Télécharger
                                </a>
                                <form action="{{ route('block.destroy', $block->id) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce bloc ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-gray-700 hover:bg-red-700 text-gray-300 hover:text-white px-3 py-2 rounded-lg transition-colors text-sm">
                                        🗑
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $blocks->links('pagination::tailwind') }}
            </div>
        @endif
    </main>

    <footer class="text-center text-gray-600 text-xs py-6">
        Minecraft Block Generator — Bedrock Edition
    </footer>

</body>
</html>
