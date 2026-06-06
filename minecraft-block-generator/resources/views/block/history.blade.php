<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste Texture — Minecraft Block Generator</title>
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
                    <p class="text-xs text-gray-400">Blocs &amp; Mobs générés</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('block.download-textures') }}"
                   class="bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg flex items-center gap-2">
                    📦 Exporter tous les blocs
                </a>
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

                <a href="{{ route('block.new') }}"
                   class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg flex items-center gap-2">
                    <span>+</span> Nouveau
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">

        @if (session('success'))
            <div id="success-notification" class="bg-green-900/50 border border-green-500 rounded-lg p-4 mb-6 text-green-300 flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="document.getElementById('success-notification').classList.add('hidden')" class="text-green-300 hover:text-green-200 transition-colors">
                    ✕
                </button>
            </div>
        @endif

        <!-- View switcher island -->
        <div class="flex justify-center mb-8">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-1.5 inline-flex gap-1 shadow-lg">
                <button id="view-all"    onclick="setView('all')"    class="view-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all">Tout</button>
                <button id="view-blocks" onclick="setView('blocks')" class="view-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all">🧱 Blocs</button>
                <button id="view-mobs"   onclick="setView('mobs')"   class="view-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all">🐾 Mobs</button>
                <button id="view-items"  onclick="setView('items')"  class="view-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all">🎁 Items</button>
                @auth
                <button id="view-mine"   onclick="setView('mine')"   class="view-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all">👤 Mes créations</button>
                @endauth
            </div>
        </div>

        {{-- ===== BLOCKS SECTION ===== --}}
        <div id="section-blocks">
        <div class="flex items-center gap-3 mb-6">
            <span class="text-2xl">🧱</span>
            <h2 class="text-lg font-bold text-white minecraft-font">Blocs</h2>
            <span id="blocks-counter" class="text-xs text-gray-500 bg-gray-800 border border-gray-700 rounded-full px-2 py-0.5">{{ $blocks->total() }}</span>
        </div>

        @if ($blocks->isEmpty())
            <div class="text-center py-24 bg-gray-800/40 rounded-xl border border-gray-700 mb-12" id="blocks-empty-box">
                <div class="text-6xl mb-4 animate-float">🧱</div>
                <p class="text-gray-400 text-base mb-1">Aucun bloc généré pour l'instant.</p>
                <p class="text-gray-600 text-sm mb-6">Commencez par créer votre premier bloc personnalisé !</p>
                <a href="{{ route('block.new') }}?mode=block" class="inline-block bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-semibold px-5 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg text-sm">
                    ➕ Créer mon premier bloc →
                </a>
            </div>
        @else
            <div class="text-center py-24 bg-gray-800/40 rounded-xl border border-gray-700 mb-12 hidden" id="blocks-empty-box">
                <div class="text-6xl mb-4 animate-float">🧱</div>
                <p class="text-gray-400 text-base mb-1">Aucun bloc généré pour l'instant.</p>
                <p class="text-gray-600 text-sm mb-6">Commencez par créer votre premier bloc personnalisé !</p>
                <a href="{{ route('block.new') }}?mode=block" class="inline-block bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-semibold px-5 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg text-sm">
                    ➕ Créer mon premier bloc →
                </a>
            </div>
            <div id="select-all-container" class="hidden flex items-center justify-end mb-4">
                <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer hover:text-gray-200 transition-colors select-none">
                    <input type="checkbox" id="select-all" class="w-4 h-4 accent-green-500 cursor-pointer">
                    Tout sélectionner
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach ($blocks as $block)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden hover:border-green-600 transition-all card-hover block-card"
                         data-id="{{ $block->id }}"
                         data-creator="{{ $block->creator_identifier }}">

                        <!-- Texture -->
                        <div class="bg-gray-900 h-40 flex items-center justify-center relative overflow-hidden">
                            <label class="absolute top-2 left-2 z-10 cursor-pointer">
                                <input type="checkbox" class="block-checkbox w-5 h-5 accent-green-500 cursor-pointer rounded"
                                       value="{{ $block->id }}">
                            </label>
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
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
                        </div>

                        <!-- Infos -->
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-1">
                                <h2 class="font-bold text-white text-lg truncate">{{ $block->name }}</h2>
                                <span class="text-xs bg-green-900/50 text-green-400 border border-green-700 rounded-full px-2 py-0.5 ml-2 whitespace-nowrap">🧱 Bloc</span>
                            </div>
                            <p class="text-green-400 text-xs font-mono mb-2">custom:{{ $block->identifier }}</p>
                            <p class="text-gray-400 text-xs mb-4">Créé par : <span class="text-green-300 font-mono">{{ $block->creator->name ?? '—' }}</span></p>

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

                            <div class="flex gap-2 items-stretch">
                                @auth
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('block.edit', $block->id) }}"
                                           class="flex items-center justify-center bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-all hover:scale-[1.02] hover:shadow-lg whitespace-nowrap">
                                            ✏️ Modifier
                                        </a>
                                    @endif
                                @endauth
                                <a href="{{ route('block.download', $block->id) }}"
                                   class="flex-1 flex items-center justify-center bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white text-xs font-bold py-2 px-2 rounded-lg transition-all hover:scale-[1.02] hover:shadow-lg whitespace-nowrap">
                                    ⬇ Télécharger
                                </a>
                                @auth
                                    @if(Auth::user()->isAdmin() || Auth::user()->isOwnerOf($block->creator_identifier))
                                        <button onclick="confirmDeleteBlock({{ $block->id }}, '{{ $block->name }}')"
                                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-all text-xs">
                                            🗑️
                                        </button>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex justify-center">
                {{ $blocks->links('pagination::tailwind') }}
            </div>
        @endif
        </div>{{-- #section-blocks --}}

        {{-- ===== MOBS SECTION ===== --}}
        <div id="section-mobs">
        <div class="flex items-center gap-3 mt-14 mb-6">
            <span class="text-2xl">🐾</span>
            <h2 class="text-lg font-bold text-white minecraft-font">Mobs</h2>
            <span id="mobs-counter" class="text-xs text-gray-500 bg-gray-800 border border-gray-700 rounded-full px-2 py-0.5">{{ $mobs->total() }}</span>
        </div>

        @if ($mobs->isEmpty())
            <div class="text-center py-24 bg-gray-800/40 rounded-xl border border-gray-700 mb-12" id="mobs-empty-box">
                <div class="text-6xl mb-4 animate-float">🐾</div>
                <p class="text-gray-400 text-base mb-1">Aucun mob généré pour l'instant.</p>
                <p class="text-gray-600 text-sm mb-6">Créez votre premier mob personnalisé !</p>
                <a href="{{ route('block.new') }}?mode=mob" class="inline-block bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white font-semibold px-5 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg text-sm">
                    ➕ Créer mon premier mob →
                </a>
            </div>
        @else
            <div class="text-center py-24 bg-gray-800/40 rounded-xl border border-gray-700 mb-12 hidden" id="mobs-empty-box">
                <div class="text-6xl mb-4 animate-float">🐾</div>
                <p class="text-gray-400 text-base mb-1">Aucun mob généré pour l'instant.</p>
                <p class="text-gray-600 text-sm mb-6">Créez votre premier mob personnalisé !</p>
                <a href="{{ route('block.new') }}?mode=mob" class="inline-block bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white font-semibold px-5 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg text-sm">
                    ➕ Créer mon premier mob →
                </a>
            </div>
            @php
                $behaviorLabels = ['passive' => 'Passif', 'neutral' => 'Neutre', 'hostile' => 'Hostile'];
                $behaviorColors = ['passive' => 'text-green-400', 'neutral' => 'text-yellow-400', 'hostile' => 'text-red-400'];
                $modelIcons     = ['humanoid' => '🚶', 'quadruped' => '🐄', 'creeper' => '💚'];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach ($mobs as $mob)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden hover:border-purple-600 transition-all card-hover mob-card"
                         data-creator="{{ $mob->creator_identifier }}">

                        <!-- Texture thumbnail -->
                        <div class="bg-gray-900 h-40 flex items-center justify-center relative overflow-hidden">
                            @if (Storage::exists($mob->texture_path))
                                <img
                                    src="{{ route('mob.texture', $mob->id) }}"
                                    alt="Skin {{ $mob->name }}"
                                    class="h-32 object-contain"
                                    style="image-rendering: pixelated;"
                                >
                            @else
                                <div class="text-6xl opacity-30">🐾</div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
                            <!-- Model type badge -->
                            <div class="absolute top-2 right-2 text-xs bg-gray-900/80 border border-gray-600 rounded-full px-2 py-0.5">
                                {{ $modelIcons[$mob->model_type] ?? '🐾' }} {{ ucfirst($mob->model_type) }}
                            </div>
                        </div>

                        <!-- Infos -->
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-1">
                                <h2 class="font-bold text-white text-lg truncate">{{ $mob->name }}</h2>
                                <span class="text-xs bg-purple-900/50 text-purple-300 border border-purple-700 rounded-full px-2 py-0.5 ml-2 whitespace-nowrap">🐾 Mob</span>
                            </div>
                            <p class="text-purple-400 text-xs font-mono mb-2">custom:{{ $mob->identifier }}</p>
                            <p class="text-gray-400 text-xs mb-4">Créé par : <span class="text-purple-300 font-mono">{{ $mob->creator->name ?? '—' }}</span></p>

                            <div class="grid grid-cols-3 gap-2 text-xs mb-4">
                                <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                                    <div class="text-gray-400 mb-1">❤️ Santé</div>
                                    <div class="text-white font-bold">{{ $mob->health }}</div>
                                </div>
                                <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                                    <div class="text-gray-400 mb-1">⚡ Vitesse</div>
                                    <div class="text-white font-bold">{{ $mob->speed }}</div>
                                </div>
                                <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                                    <div class="text-gray-400 mb-1">🗡 Comport.</div>
                                    <div class="{{ $behaviorColors[$mob->behavior_type] ?? 'text-gray-300' }} font-semibold">
                                        {{ $behaviorLabels[$mob->behavior_type] ?? $mob->behavior_type }}
                                    </div>
                                </div>
                            </div>

                            <!-- Spawn egg colors -->
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xs text-gray-500">Œuf :</span>
                                <span class="w-4 h-4 rounded-sm border border-gray-600 inline-block" style="background:{{ $mob->spawn_egg_primary }}"></span>
                                <span class="w-4 h-4 rounded-sm border border-gray-600 inline-block" style="background:{{ $mob->spawn_egg_secondary }}"></span>
                                <span class="text-xs text-gray-500 font-mono">{{ $mob->spawn_egg_primary }} / {{ $mob->spawn_egg_secondary }}</span>
                            </div>

                            <p class="text-gray-600 text-xs mb-4 flex items-center gap-2">
                                <span>📅</span> Créé le {{ $mob->created_at->format('d/m/Y à H:i') }}
                            </p>

                            <div class="flex gap-2 items-stretch">
                                @auth
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('mob.edit', $mob->id) }}"
                                           class="flex items-center justify-center bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-all hover:scale-[1.02] hover:shadow-lg whitespace-nowrap">
                                            ✏️ Modifier
                                        </a>
                                    @endif
                                @endauth
                                <a href="{{ route('mob.download', $mob->id) }}"
                                   class="flex-1 flex items-center justify-center bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white text-xs font-bold py-2 px-2 rounded-lg transition-all hover:scale-[1.02] hover:shadow-lg whitespace-nowrap">
                                    ⬇ Télécharger
                                </a>
                                @auth
                                    @if(Auth::user()->isAdmin() || Auth::user()->isOwnerOf($mob->creator_identifier))
                                        <button onclick="confirmDeleteMob({{ $mob->id }}, '{{ $mob->name }}')"
                                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-all text-xs">
                                            🗑️
                                        </button>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex justify-center">
                {{ $mobs->links('pagination::tailwind') }}
            </div>
        @endif
        </div>{{-- #section-mobs --}}

        {{-- ===== ITEMS SECTION ===== --}}
        <div id="section-items" class="hidden">
        <div class="flex items-center gap-3 mt-14 mb-6">
            <span class="text-2xl">🎁</span>
            <h2 class="text-lg font-bold text-white minecraft-font">Items</h2>
            <span id="items-counter" class="text-xs text-gray-500 bg-gray-800 border border-gray-700 rounded-full px-2 py-0.5">{{ isset($items) ? $items->total() : 0 }}</span>
        </div>

        @if (!isset($items) || $items->isEmpty())
            <div class="text-center py-24 bg-gray-800/40 rounded-xl border border-gray-700 mb-12" id="items-empty-box">
                <div class="text-6xl mb-4 animate-float">🎁</div>
                <p class="text-gray-400 text-base mb-1">Aucun item généré pour l'instant.</p>
                <p class="text-gray-600 text-sm mb-6">Créez votre premier item personnalisé !</p>
                <a href="{{ route('block.new') }}?mode=item" class="inline-block bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-semibold px-5 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg text-sm">
                    ➕ Créer mon premier item →
                </a>
            </div>
        @else
            <div class="text-center py-24 bg-gray-800/40 rounded-xl border border-gray-700 mb-12 hidden" id="items-empty-box">
                <div class="text-6xl mb-4 animate-float">🎁</div>
                <p class="text-gray-400 text-base mb-1">Aucun item généré pour l'instant.</p>
                <p class="text-gray-600 text-sm mb-6">Créez votre premier item personnalisé !</p>
                <a href="{{ route('block.new') }}?mode=item" class="inline-block bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-semibold px-5 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg text-sm">
                    ➕ Créer mon premier item →
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach ($items as $item)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden hover:border-green-600 transition-all card-hover item-card"
                         data-creator="{{ $item->creator_identifier }}">

                        <!-- Texture thumbnail -->
                        <div class="bg-gray-900 h-40 flex items-center justify-center relative overflow-hidden">
                            @if (Storage::exists($item->texture_path))
                                <img
                                    src="{{ route('item.texture', $item->id) }}"
                                    alt="Texture {{ $item->name }}"
                                    class="w-28 h-28 object-contain"
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
                                <span class="text-xs bg-green-900/50 text-green-400 border border-green-700 rounded-full px-2 py-0.5 ml-2 whitespace-nowrap">🎁 Item</span>
                            </div>
                            <p class="text-green-400 text-xs font-mono mb-2">custom:{{ $item->identifier }}</p>
                            <p class="text-gray-400 text-xs mb-4">Créé par : <span class="text-green-300 font-mono">{{ $item->creator->name ?? '—' }}</span></p>

                            <p class="text-gray-600 text-xs mb-4 flex items-center gap-2">
                                <span>📅</span> Créé le {{ $item->created_at->format('d/m/Y à H:i') }}
                            </p>

                            <!-- Actions -->
                            <div class="flex gap-2 items-stretch">
                                @auth
                                    @if(Auth::user()->isAdmin() || Auth::user()->identifier === $item->creator_identifier)
                                        <a href="{{ route('item.edit', $item->id) }}"
                                           class="flex items-center justify-center bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-all hover:scale-[1.02] hover:shadow-lg whitespace-nowrap">
                                            ✏️ Modifier
                                        </a>
                                    @endif
                                @endauth
                                <a href="{{ route('item.download', $item->id) }}"
                                   class="flex-1 flex items-center justify-center bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white text-xs font-bold py-2 px-2 rounded-lg transition-all hover:scale-[1.02] hover:shadow-lg whitespace-nowrap">
                                    ⬇ Télécharger
                                </a>
                                @auth
                                    @if(Auth::user()->isAdmin() || Auth::user()->identifier === $item->creator_identifier)
                                        <button onclick="confirmDeleteItem({{ $item->id }}, '{{ $item->name }}')"
                                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-all text-xs">
                                            🗑️
                                        </button>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex justify-center">
                {{ $items->links('pagination::tailwind') }}
            </div>
        @endif
        </div>{{-- #section-items --}}
    </main>

    <!-- Selection action bar -->
    <div id="selection-bar"
         class="fixed bottom-0 left-0 right-0 z-50 bg-gray-800 border-t border-gray-700 shadow-2xl
                transform translate-y-full transition-transform duration-300 ease-in-out">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span id="selection-count" class="text-green-400 font-semibold text-sm"></span>
                <button onclick="clearSelection()"
                        class="text-xs text-gray-400 hover:text-gray-200 transition-colors underline">
                    Tout désélectionner
                </button>
            </div>
            <form id="download-selected-form" action="{{ route('block.download-selected') }}" method="POST">
                @csrf
                <div id="selected-ids-container"></div>
                <button type="submit"
                        class="bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-all hover:scale-105 hover:shadow-lg flex items-center gap-2">
                    ⬇ Télécharger la sélection
                </button>
            </form>
        </div>
    </div>

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

    <script>
        const selectionBar      = document.getElementById('selection-bar');
        const selectionCount    = document.getElementById('selection-count');
        const idsContainer      = document.getElementById('selected-ids-container');
        const selectAllCheckbox = document.getElementById('select-all');
        const checkboxes        = document.querySelectorAll('.block-checkbox');

        function updateBar() {
            const checked = [...document.querySelectorAll('.block-checkbox:checked')];
            const n = checked.length;

            if (n > 0) {
                selectionCount.textContent = n + ' bloc' + (n > 1 ? 's' : '') + ' sélectionné' + (n > 1 ? 's' : '');
                selectionBar.classList.remove('translate-y-full');
            } else {
                selectionBar.classList.add('translate-y-full');
            }

            // Update hidden inputs for form POST
            idsContainer.innerHTML = '';
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = 'ids[]';
                input.value = cb.value;
                idsContainer.appendChild(input);
            });

            // Update card border highlight
            document.querySelectorAll('.block-card').forEach(card => {
                const cb = card.querySelector('.block-checkbox');
                if (cb && cb.checked) {
                    card.classList.add('border-green-500');
                    card.classList.remove('border-gray-700');
                } else {
                    card.classList.remove('border-green-500');
                    card.classList.add('border-gray-700');
                }
            });

            // Sync select-all state
            if (selectAllCheckbox) {
                selectAllCheckbox.checked       = n > 0 && n === checkboxes.length;
                selectAllCheckbox.indeterminate  = n > 0 && n < checkboxes.length;
            }
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateBar));

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBar();
            });
        }

        function clearSelection() {
            checkboxes.forEach(cb => cb.checked = false);
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            updateBar();
        }

        // --- View switcher ---
        const currentUserIdentifier = @auth '{{ Auth::user()->identifier }}' @else null @endauth;

        function setView(v) {
            const allCards = document.querySelectorAll('.block-card, .mob-card, .item-card');

            // Reset individual card visibility
            allCards.forEach(c => c.classList.remove('hidden'));

            if (v === 'mine') {
                // Show all sections, but hide cards not owned by current user
                document.getElementById('section-blocks').classList.remove('hidden');
                document.getElementById('section-mobs').classList.remove('hidden');
                document.getElementById('section-items').classList.remove('hidden');
                allCards.forEach(card => {
                    if (card.dataset.creator !== currentUserIdentifier) {
                        card.classList.add('hidden');
                    }
                });
            } else if (v === 'all') {
                document.getElementById('section-blocks').classList.remove('hidden');
                document.getElementById('section-mobs').classList.remove('hidden');
                document.getElementById('section-items').classList.remove('hidden');
            } else if (v === 'blocks') {
                document.getElementById('section-blocks').classList.remove('hidden');
                document.getElementById('section-mobs').classList.add('hidden');
                document.getElementById('section-items').classList.add('hidden');
            } else if (v === 'mobs') {
                document.getElementById('section-blocks').classList.add('hidden');
                document.getElementById('section-mobs').classList.remove('hidden');
                document.getElementById('section-items').classList.add('hidden');
            } else if (v === 'items') {
                document.getElementById('section-blocks').classList.add('hidden');
                document.getElementById('section-mobs').classList.add('hidden');
                document.getElementById('section-items').classList.remove('hidden');
            }

            // Show/hide empty boxes and counters based on visible cards
            const blockSection = document.getElementById('section-blocks');
            const mobSection = document.getElementById('section-mobs');
            const itemSection = document.getElementById('section-items');

            const blockCards = blockSection.classList.contains('hidden') ? [] : document.querySelectorAll('.block-card:not(.hidden)');
            const mobCards = mobSection.classList.contains('hidden') ? [] : document.querySelectorAll('.mob-card:not(.hidden)');
            const itemCards = itemSection.classList.contains('hidden') ? [] : document.querySelectorAll('.item-card:not(.hidden)');
            const totalCards = blockCards.length + mobCards.length + itemCards.length;

            document.getElementById('blocks-empty-box').classList.toggle('hidden', blockCards.length > 0);
            document.getElementById('mobs-empty-box').classList.toggle('hidden', mobCards.length > 0);
            document.getElementById('items-empty-box').classList.toggle('hidden', itemCards.length > 0);

            document.getElementById('blocks-counter').classList.toggle('hidden', blockCards.length === 0);
            document.getElementById('mobs-counter').classList.toggle('hidden', mobCards.length === 0);
            document.getElementById('items-counter').classList.toggle('hidden', itemCards.length === 0);

            // Show/hide select all button only if there are at least 2 cards total
            document.getElementById('select-all-container').classList.toggle('hidden', totalCards < 2);

            document.querySelectorAll('.view-btn').forEach(b => {
                const active = b.id === 'view-' + v;
                b.className = 'view-btn px-5 py-2 text-sm font-semibold rounded-lg transition-all '
                    + (active ? 'bg-green-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700');
            });
            localStorage.setItem('mcgen_history_view', v);
        }
        setView(localStorage.getItem('mcgen_history_view') || 'all');

        // Delete confirmation modal for blocks
        function confirmDeleteBlock(blockId, blockName) {
            document.getElementById('delete-block-name').textContent = blockName;
            document.getElementById('delete-block-modal').classList.remove('hidden');

            document.getElementById('confirm-delete-block-btn').onclick = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("block.destroy", ":id") }}'.replace(':id', blockId);
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            };
        }

        // Delete confirmation modal for mobs
        function confirmDeleteMob(mobId, mobName) {
            document.getElementById('delete-mob-name').textContent = mobName;
            document.getElementById('delete-mob-modal').classList.remove('hidden');

            document.getElementById('confirm-delete-mob-btn').onclick = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("mob.destroy", ":id") }}'.replace(':id', mobId);
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            };
        }

        // Delete confirmation modal for items
        function confirmDeleteItem(itemId, itemName) {
            document.getElementById('delete-item-name').textContent = itemName;
            document.getElementById('delete-item-modal').classList.remove('hidden');

            document.getElementById('confirm-delete-item-btn').onclick = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("item.destroy", ":id") }}'.replace(':id', itemId);
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            };
        }
    </script>

    <!-- Delete confirmation modal for blocks -->
    <div id="delete-block-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 max-w-sm">
            <h2 class="text-lg font-bold text-white mb-2">Supprimer le bloc ?</h2>
            <p class="text-gray-400 mb-6">
                Êtes-vous sûr de vouloir supprimer <span id="delete-block-name" class="font-semibold text-green-400"></span> ?
                Cette action est irréversible.
            </p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('delete-block-modal').classList.add('hidden')"
                        class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 rounded-lg transition-all">
                    Annuler
                </button>
                <button id="confirm-delete-block-btn"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-all">
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Delete confirmation modal for mobs -->
    <div id="delete-mob-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 max-w-sm">
            <h2 class="text-lg font-bold text-white mb-2">Supprimer le mob ?</h2>
            <p class="text-gray-400 mb-6">
                Êtes-vous sûr de vouloir supprimer <span id="delete-mob-name" class="font-semibold text-green-400"></span> ?
                Cette action est irréversible.
            </p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('delete-mob-modal').classList.add('hidden')"
                        class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 rounded-lg transition-all">
                    Annuler
                </button>
                <button id="confirm-delete-mob-btn"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-all">
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Delete confirmation modal for items -->
    <div id="delete-item-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 max-w-sm">
            <h2 class="text-lg font-bold text-white mb-2">Supprimer l'item ?</h2>
            <p class="text-gray-400 mb-6">
                Êtes-vous sûr de vouloir supprimer <span id="delete-item-name" class="font-semibold text-green-400"></span> ?
                Cette action est irréversible.
            </p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('delete-item-modal').classList.add('hidden')"
                        class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 rounded-lg transition-all">
                    Annuler
                </button>
                <button id="confirm-delete-item-btn"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-all">
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Logout confirmation modal -->
    <div id="logout-modal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
         onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-8 w-full max-w-sm mx-4 text-center">
            <div class="text-5xl mb-4">🚪</div>
            <h2 class="text-xl font-bold text-white minecraft-font mb-2">Déconnexion</h2>
            <p class="text-gray-400 text-sm mb-6">Voulez-vous vraiment vous déconnecter&nbsp;?</p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('logout-modal').classList.add('hidden')"
                        class="flex-1 bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white font-semibold py-2.5 rounded-xl transition-all">
                    Annuler
                </button>
                <form action="{{ route('auth.logout') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-500 text-white font-semibold py-2.5 rounded-xl transition-all">
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
