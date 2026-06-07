<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de Blocs Minecraft</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
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
        @keyframes slide-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-slide-in { animation: slide-in 0.5s ease-out forwards; }

        /* 3D cube preview */
        .cube-scene {
            width: 96px; height: 96px;
            perspective: 260px;
            margin: 0 auto;
        }
        .cube-3d {
            width: 64px; height: 64px;
            position: relative;
            transform-style: preserve-3d;
            transform: rotateX(30deg) rotateY(45deg);
            margin: 16px auto;
            cursor: grab;
            transition: transform 0.3s ease;
        }
        .cube-3d:hover { transform: rotateX(30deg) rotateY(45deg) scale(1.1); }
        .cube-3d.dragging { cursor: grabbing; }
        .cube-face {
            position: absolute;
            width: 64px; height: 64px;
            background-color: #4b5563;
            background-size: cover;
            background-position: center;
            image-rendering: pixelated;
            border: 1px solid rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        .cube-face-top   { transform: rotateX(90deg) translateZ(32px); filter: brightness(1.25); }
        .cube-face-front { transform: translateZ(32px);                 filter: brightness(0.9); }
        .cube-face-right { transform: rotateY(90deg) translateZ(32px);  filter: brightness(0.65); }
        .cube-face-back  { transform: rotateY(180deg) translateZ(32px); filter: brightness(0.65); }
        .cube-face-left  { transform: rotateY(-90deg) translateZ(32px); filter: brightness(0.9); }
        .cube-face-bottom{ transform: rotateX(-90deg) translateZ(32px); filter: brightness(0.5); }

        /* Enhanced form inputs */
        .input-enhanced {
            transition: all 0.3s ease;
        }
        .input-enhanced:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        /* Button enhancements */
        .btn-minecraft {
            position: relative;
            overflow: hidden;
        }
        .btn-minecraft::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        .btn-minecraft:hover::before {
            left: 100%;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen" @if (isset($block)) data-edit-block-id="{{ $block->id }}" @endif>
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 py-4 px-6 sticky top-0 z-40 backdrop-blur-md bg-opacity-95">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-600 rounded-lg grid grid-cols-2 gap-0.5 p-1.5 animate-float shadow-lg">
                    <div class="bg-green-400 rounded-sm"></div>
                    <div class="bg-green-700 rounded-sm"></div>
                    <div class="bg-green-700 rounded-sm"></div>
                    <div class="bg-green-400 rounded-sm"></div>
                </div>
                <div>
                    <h1 class="text-xl font-bold minecraft-font text-green-400 flex items-center gap-2">
                        <span class="text-2xl">🧱</span> Générateur de blocs Minecraft
                    </h1>
                    <p class="text-xs text-gray-400">Bedrock Edition — Créez votre bloc personnalisé</p>
                </div>
            </div>
            <a href="{{ route('block.index') }}"
               class="bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg flex items-center gap-2">
                <span>📜</span> <span id="history-link-text">Liste de blocs</span>
            </a>
        </div>
    </header>

        <!-- Mode Selector Island -->
        <div class="flex justify-center pt-6 pb-2">
            <div class="bg-gray-800 rounded-xl p-1.5 inline-flex gap-1 border border-gray-700 shadow-xl">
                <button id="mode-btn-block" onclick="setMode('block')"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all bg-green-600 text-white shadow-md">
                    🧱 Bloc
                </button>
                <button id="mode-btn-mob" onclick="setMode('mob')"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all text-gray-400 hover:text-white hover:bg-gray-700">
                    🐾 Mob
                </button>
                <button id="mode-btn-item" onclick="setMode('item')"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all text-gray-400 hover:text-white hover:bg-gray-700">
                    🎁 Item
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-900/50 border border-red-500 rounded-lg p-4 mb-4 mt-2">
                <h2 class="font-bold text-red-400 mb-2">Erreurs de validation :</h2>
                <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">

            <!-- Formulaire Bloc -->
            <div id="block-form-col" class="lg:col-span-2">
                <form
                    id="block-form"
                    action="{{ isset($block) ? route('block.update', $block->id) : route('block.create') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    novalidate
                >
                    @csrf

                    <!-- Section : Identité du bloc -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl animate-bounce">🧱</span> Identité du bloc
                        </h2>

                        <div class="space-y-4">
                            <!-- Nom du bloc -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1" for="name">
                                    Nom du bloc <span class="text-red-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', $block->name ?? '') }}"
                                    placeholder="Ex: Pierre volcanique"
                                    maxlength="50"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/30 @error('name') border-red-500 @enderror input-enhanced"
                                >
                                @error('name')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-xs mt-1">1–50 caractères, lettres, chiffres et espaces uniquement.</p>
                            </div>

                            <!-- Identifiant technique -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1" for="identifier">
                                    Identifiant technique <span class="text-red-400">*</span>
                                </label>
                                <div class="flex items-center">
                                    <span class="bg-gray-600 border border-r-0 border-gray-600 rounded-l-lg px-3 py-2 text-gray-400 text-sm">custom:</span>
                                    <input
                                        type="text"
                                        id="identifier"
                                        name="identifier"
                                        value="{{ old('identifier', $block->identifier ?? '') }}"
                                        placeholder="volcanic_rock"
                                        pattern="[a-z0-9_]+"
                                        {{ isset($block) ? 'readonly' : '' }}
                                        class="flex-1 bg-gray-700 border border-gray-600 rounded-r-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('identifier') border-red-500 @enderror {{ isset($block) ? 'bg-gray-600 cursor-not-allowed' : '' }}"
                                    >
                                </div>
                                @error('identifier')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-xs mt-1">{{ isset($block) ? 'Cet identifiant ne peut pas être modifié.' : 'Minuscules et underscores uniquement (ex: <code class="text-green-400">my_block</code>).' }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Section : Texture -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">🎨</span> Texture
                        </h2>

                        <!-- Format selection -->
                        <div class="mb-6 p-4 bg-gray-700/50 rounded-lg border border-gray-600">
                            <p class="text-sm font-medium text-gray-300 mb-3">Type de bloc :</p>
                            <div class="space-y-2">
                                @php $isComplex = isset($block) && $block->geometry === 'net'; @endphp
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-700/50 p-2 rounded">
                                    <input type="radio" name="block_type" value="simple" {{ $isComplex ? '' : 'checked' }} class="w-4 h-4 accent-green-500" id="block-simple">
                                    <span class="text-gray-300">
                                        <span class="font-medium">🧱 Bloc simple</span>
                                        <span class="text-xs text-gray-500 ml-2">(même texture sur les 6 faces — ex: terre, pierre)</span>
                                    </span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-700/50 p-2 rounded">
                                    <input type="radio" name="block_type" value="complex" {{ $isComplex ? 'checked' : '' }} class="w-4 h-4 accent-green-500" id="block-complex">
                                    <span class="text-gray-300">
                                        <span class="font-medium">📦 Bloc complexe</span>
                                        <span class="text-xs text-gray-500 ml-2">(6 faces différentes — ex: coffre, four)</span>
                                    </span>
                                </label>
                            </div>

                            <!-- Sub-options for complex blocks -->
                            <div id="complex-options" class="{{ $isComplex ? '' : 'hidden' }} mt-4 pt-4 border-t border-gray-500 space-y-2">
                                <p class="text-xs font-medium text-gray-400 mb-2">Format du bloc complexe :</p>
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-700/50 p-2 rounded">
                                    <input type="radio" name="complex_format" value="net" checked class="w-4 h-4 accent-green-500" id="format-net">
                                    <span class="text-gray-300">
                                        <span class="text-sm">🗺️ Image réseau</span>
                                        <span class="text-xs text-gray-500 ml-2">(une image avec les 6 faces)</span>
                                    </span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-700/50 p-2 rounded">
                                    <input type="radio" name="complex_format" value="separate" class="w-4 h-4 accent-green-500" id="format-separate">
                                    <span class="text-gray-300">
                                        <span class="text-sm">🎨 6 fichiers séparés</span>
                                        <span class="text-xs text-gray-500 ml-2">(un fichier par face)</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Single file upload (for simple blocks and net format) -->
                        <div id="single-upload-zone">
                            <div
                                id="drop-zone"
                                class="border-2 border-dashed border-gray-600 rounded-xl p-10 text-center cursor-pointer transition-all hover:border-green-500 hover:bg-gray-750 @error('texture') border-red-500 @enderror group"
                                onclick="document.getElementById('texture').click()"
                            >
                                <input
                                    type="file"
                                    id="texture"
                                    name="texture"
                                    accept="image/png"
                                    class="hidden"
                                >
                                <div id="upload-placeholder">
                                    <div class="text-6xl mb-4 group-hover:scale-110 transition-transform">📁</div>
                                    <p class="text-gray-300 font-medium text-lg">Cliquez ou glissez-déposez votre texture</p>
                                    <p class="text-gray-500 text-sm mt-2">PNG uniquement — max 512 Ko</p>
                                    <p class="text-gray-600 text-xs mt-1" id="upload-hint">16×16…256×256</p>
                                </div>
                                <div id="preview-container" class="hidden flex-col items-center gap-3">
                                    <img id="texture-preview" src="" alt="Prévisualisation" class="w-32 h-32 object-contain rounded-lg border-2 border-green-500 shadow-lg animate-pulse-glow" style="image-rendering: pixelated;">
                                    <p id="texture-name" class="text-green-400 text-sm font-medium"></p>
                                    <p class="text-gray-500 text-xs">Cliquez pour changer</p>
                                </div>
                            </div>

                            @error('texture')
                                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                            @enderror

                            <!-- Indicateur de forme détectée -->
                            <div id="geometry-indicator" class="hidden mt-3 flex items-center gap-2 text-sm px-3 py-2 rounded-lg">
                                <span id="geometry-icon"></span>
                                <span id="geometry-label"></span>
                            </div>
                        </div>

                        @if (isset($block) && \Illuminate\Support\Facades\Storage::exists($block->texture_path))
                            <div class="mt-4 p-4 bg-blue-900/30 border border-blue-600 rounded-lg text-blue-300 text-sm">
                                <p class="mb-2">📦 Texture actuelle chargée</p>
                                <p class="text-xs text-blue-400">Téléchargez une nouvelle texture pour la remplacer, ou conservez l'actuelle.</p>
                            </div>
                        @endif

                        <!-- Multiple file uploads (for 6 separate faces) -->
                        <div id="separate-upload-zone" class="hidden space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Top -->
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-400 mb-2">Haut (Top)</label>
                                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-green-500 transition-colors" onclick="document.getElementById('texture-top').click()">
                                        <input type="file" id="texture-top" name="texture_top" accept="image/png" class="hidden face-upload">
                                        <div class="text-2xl mb-2">⬆️</div>
                                        <p class="text-gray-400 text-sm">Cliquez ou déposez</p>
                                        <p class="text-gray-500 text-xs mt-1" id="top-name">Aucun fichier</p>
                                    </div>
                                </div>

                                <!-- Left, Front, Right, Back row -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2">Gauche (Left)</label>
                                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-3 text-center cursor-pointer hover:border-green-500" onclick="document.getElementById('texture-left').click()">
                                        <input type="file" id="texture-left" name="texture_left" accept="image/png" class="hidden face-upload">
                                        <p class="text-xl">⬅️</p>
                                        <p class="text-gray-500 text-xs mt-1" id="left-name">—</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2">Avant (Front)</label>
                                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-3 text-center cursor-pointer hover:border-green-500" onclick="document.getElementById('texture-front').click()">
                                        <input type="file" id="texture-front" name="texture_front" accept="image/png" class="hidden face-upload">
                                        <p class="text-xl">⬇️</p>
                                        <p class="text-gray-500 text-xs mt-1" id="front-name">—</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2">Droite (Right)</label>
                                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-3 text-center cursor-pointer hover:border-green-500" onclick="document.getElementById('texture-right').click()">
                                        <input type="file" id="texture-right" name="texture_right" accept="image/png" class="hidden face-upload">
                                        <p class="text-xl">➡️</p>
                                        <p class="text-gray-500 text-xs mt-1" id="right-name">—</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-2">Arrière (Back)</label>
                                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-3 text-center cursor-pointer hover:border-green-500" onclick="document.getElementById('texture-back').click()">
                                        <input type="file" id="texture-back" name="texture_back" accept="image/png" class="hidden face-upload">
                                        <p class="text-xl">↩️</p>
                                        <p class="text-gray-500 text-xs mt-1" id="back-name">—</p>
                                    </div>
                                </div>

                                <!-- Bottom -->
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-400 mb-2">Bas (Bottom)</label>
                                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-green-500" onclick="document.getElementById('texture-bottom').click()">
                                        <input type="file" id="texture-bottom" name="texture_bottom" accept="image/png" class="hidden face-upload">
                                        <p class="text-2xl mb-2">⬇️</p>
                                        <p class="text-gray-400 text-sm">Cliquez ou déposez</p>
                                        <p class="text-gray-500 text-xs mt-1" id="bottom-name">Aucun fichier</p>
                                    </div>
                                </div>
                            </div>
                            <div id="separate-indicator" class="mt-3 p-3 bg-green-900/30 border border-green-600 rounded-lg text-green-300 text-xs flex items-center gap-2">
                                <span id="separate-status">✓ 0/6 fichiers chargés</span>
                            </div>
                        </div>
                    </section>

                    <!-- Section : Géométrie du bloc -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">📐</span> Géométrie du bloc (Optionnel)
                        </h2>

                        <div class="mb-4 p-4 bg-gray-700/50 rounded-lg border border-gray-600">
                            <p class="text-sm text-gray-300 mb-3">Uploadez un fichier JSON pour définir une géométrie personnalisée :</p>
                            <div
                                id="geometry-drop-zone"
                                class="border-2 border-dashed border-gray-600 rounded-lg p-6 text-center cursor-pointer transition-colors hover:border-green-500"
                                onclick="document.getElementById('geometry-file').click()"
                            >
                                <input
                                    type="file"
                                    id="geometry-file"
                                    name="geometry_file"
                                    accept=".json"
                                    class="hidden"
                                >
                                <div id="geometry-upload-placeholder">
                                    <div class="text-4xl mb-2">📄</div>
                                    <p class="text-gray-300 font-medium">Cliquez ou glissez-déposez votre fichier JSON</p>
                                    <p class="text-gray-500 text-sm mt-1">Format: blocks/exemple_block_geo.json</p>
                                </div>
                                <div id="geometry-preview-container" class="hidden flex-col items-center gap-3">
                                    <div class="text-green-400">✓ Fichier chargé</div>
                                    <p id="geometry-file-name" class="text-green-400 text-sm font-mono"></p>
                                    <div id="geometry-preview-info" class="text-xs text-gray-400 mt-2 bg-gray-800 p-3 rounded max-w-xs text-left">
                                        <p><strong>Identifiant:</strong> <span id="geo-identifier" class="text-green-400">—</span></p>
                                        <p><strong>Collision box:</strong> <span id="geo-collision" class="text-green-400 font-mono text-xs">—</span></p>
                                    </div>
                                    <p class="text-gray-500 text-xs mt-2">Cliquez pour changer</p>
                                </div>
                            </div>
                            <p class="text-gray-500 text-xs mt-3">Laissez vide pour un cube standard.</p>
                        </div>

                        @php
                            $existingGeometryJson = (isset($block) && $block->geometry_json_path)
                                ? \Illuminate\Support\Facades\Storage::get($block->geometry_json_path)
                                : '';
                            $existingGeometryFilename = (isset($block) && $block->geometry_json_path)
                                ? basename($block->geometry_json_path)
                                : '';
                        @endphp
                        <input type="hidden" id="geometry-data" name="geometry_data" value="{{ $existingGeometryJson }}">
                    </section>

                    <!-- Section : Propriétés -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">⚙️</span> Propriétés du bloc
                        </h2>

                        <div class="space-y-5">
                            <!-- Solidité -->
                            <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">🧱</span>
                                    <div>
                                        <p class="font-medium text-gray-200">Solidité</p>
                                        <p class="text-gray-500 text-xs">Le bloc possède une hitbox et bloque les joueurs</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        id="solid-check"
                                        value="1"
                                        class="sr-only peer"
                                        @checked(old('solid', isset($block) ? $block->solid : true))
                                    >
                                    <div class="w-14 h-7 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600 shadow-inner"></div>
                                </label>
                            </div>

                            <!-- Destructible -->
                            <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">⛏️</span>
                                    <div>
                                        <p class="font-medium text-gray-200">Destructible</p>
                                        <p class="text-gray-500 text-xs">Le bloc peut être cassé par les joueurs</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        id="destructible-check"
                                        value="1"
                                        class="sr-only peer"
                                        @checked(old('destructible', isset($block) ? $block->destructible : true))
                                    >
                                    <div class="w-14 h-7 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600 shadow-inner"></div>
                                </label>
                            </div>

                            <!-- Résistance -->
                            <div>
                                <p class="font-medium text-gray-200 mb-1">Résistance aux explosions</p>
                                <p class="text-gray-500 text-xs mb-2">Résistance aux TNT et creepers (0 = fragile, 100 = bedrock)</p>
                                <input
                                    type="number"
                                    name="resistance"
                                    id="resistance"
                                    min="0"
                                    max="100"
                                    step="0.5"
                                    value="{{ old('resistance', isset($block) ? $block->resistance : 3) }}"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                >
                                @error('resistance')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Luminosité -->
                            <div>
                                <p class="font-medium text-gray-200 mb-1 flex items-center gap-2">
                                    <span>💡</span> Luminosité émise
                                </p>
                                <p class="text-gray-500 text-xs mb-2">Niveau de lumière émis par le bloc (0 = aucune, 15 = torche)</p>
                                <input
                                    type="number"
                                    name="light_emission"
                                    id="light-emission"
                                    min="0"
                                    max="15"
                                    step="1"
                                    value="{{ old('light_emission', isset($block) ? $block->light_emission : 0) }}"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
                                >
                                @error('light_emission')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Bouton de génération -->
                    <button
                        type="submit"
                        id="submit-btn"
                        class="w-full bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-bold py-4 px-6 rounded-xl transition-all minecraft-font text-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed btn-minecraft shadow-lg hover:shadow-green-600/30 hover:scale-[1.02] active:scale-[0.98]"
                    >
                        <span id="btn-icon" class="text-2xl">{{ isset($block) ? '💾' : '⚡' }}</span>
                        <span id="btn-text">{{ isset($block) ? 'Enregistrer et régénérer' : 'Générer mon bloc' }}</span>
                    </button>

                </form>
            </div>

            <!-- Formulaire Mob (caché par défaut) -->
            <div id="mob-form-col" class="lg:col-span-2 hidden">
                <form id="mob-form" action="{{ isset($mob) ? route('mob.update', $mob->id) : route('mob.create') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <!-- Identité -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">🐾</span> Identité du mob
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label for="mob-name" class="block text-sm font-medium text-gray-300 mb-1">Nom du mob</label>
                                <input type="text" id="mob-name" name="name" maxlength="50" placeholder="ex: Dragon des Glaces"
                                    value="{{ old('name', $mob->name ?? '') }}"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 input-enhanced">
                                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="mob-identifier" class="block text-sm font-medium text-gray-300 mb-1">Identifiant unique</label>
                                <div class="flex items-center gap-2">
                                    <span class="text-green-400 font-mono text-sm bg-gray-700 px-3 py-2 rounded-lg border border-gray-600">custom:</span>
                                    <input type="text" id="mob-identifier" name="identifier" placeholder="ice_dragon"
                                        value="{{ old('identifier', $mob->identifier ?? '') }}"
                                        {{ isset($mob) ? 'readonly' : '' }}
                                        class="flex-1 bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white font-mono focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 input-enhanced {{ isset($mob) ? 'bg-gray-600 cursor-not-allowed' : '' }}">
                                </div>
                                <p class="text-gray-500 text-xs mt-1">{{ isset($mob) ? 'Cet identifiant ne peut pas être modifié.' : 'Minuscules et underscores uniquement.' }}</p>
                                @error('identifier') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Modèle & Texture -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">🎨</span> Modèle & Texture
                        </h2>

                        <!-- Model type -->
                        <div class="mb-5 p-4 bg-gray-700/50 rounded-lg border border-gray-600">
                            <p class="text-sm font-medium text-gray-300 mb-3">Type de modèle :</p>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="model_type" value="humanoid" {{ old('model_type', $mob->model_type ?? 'humanoid') === 'humanoid' ? 'checked' : '' }} class="sr-only peer" id="mob-model-humanoid">
                                    <div class="peer-checked:border-green-500 peer-checked:bg-green-500/10 border-2 border-gray-600 rounded-lg p-3 text-center transition-all hover:border-gray-500">
                                        <div class="text-2xl mb-1">🧍</div>
                                        <p class="text-xs font-medium text-gray-300 peer-checked:text-green-400">Humanoïde</p>
                                        <p class="text-xs text-gray-500">Zombie, squelette</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="model_type" value="quadruped" {{ old('model_type', $mob->model_type ?? 'humanoid') === 'quadruped' ? 'checked' : '' }} class="sr-only peer" id="mob-model-quadruped">
                                    <div class="peer-checked:border-green-500 peer-checked:bg-green-500/10 border-2 border-gray-600 rounded-lg p-3 text-center transition-all hover:border-gray-500">
                                        <div class="text-2xl mb-1">🐷</div>
                                        <p class="text-xs font-medium text-gray-300">Quadrupède</p>
                                        <p class="text-xs text-gray-500">Cochon, vache</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="model_type" value="creeper" {{ old('model_type', $mob->model_type ?? 'humanoid') === 'creeper' ? 'checked' : '' }} class="sr-only peer" id="mob-model-creeper">
                                    <div class="peer-checked:border-green-500 peer-checked:bg-green-500/10 border-2 border-gray-600 rounded-lg p-3 text-center transition-all hover:border-gray-500">
                                        <div class="text-2xl mb-1">💥</div>
                                        <p class="text-xs font-medium text-gray-300">Creeper</p>
                                        <p class="text-xs text-gray-500">4 pattes, compact</p>
                                    </div>
                                </label>
                            </div>
                            @error('model_type') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
                        </div>

                        <!-- Texture upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Texture / skin PNG</label>
                            <div id="mob-drop-zone"
                                class="border-2 border-dashed border-gray-600 rounded-xl p-8 text-center cursor-pointer transition-all hover:border-green-500 hover:bg-gray-750"
                                onclick="document.getElementById('mob-texture').click()">
                                <input type="file" id="mob-texture" name="texture" accept=".png,image/png" class="hidden">
                                <div id="mob-upload-placeholder">
                                    <div class="text-4xl mb-2">🖼️</div>
                                    <p class="text-gray-300 font-medium">Cliquez ou glissez votre texture PNG</p>
                                    <p class="text-gray-500 text-sm mt-1">64×64 (humanoïde) · 64×32 (quadrupède / creeper)</p>
                                </div>
                                <div id="mob-preview-container" class="hidden flex-col items-center gap-2">
                                    <img id="mob-texture-preview" src="" alt="" class="w-16 h-16 object-contain rounded" style="image-rendering:pixelated">
                                    <p id="mob-texture-name" class="text-green-400 text-sm font-mono"></p>
                                    <p class="text-gray-500 text-xs">Cliquez pour changer</p>
                                </div>
                            </div>
                            @error('texture') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            @if (isset($mob) && \Illuminate\Support\Facades\Storage::exists($mob->texture_path))
                                <div class="mt-3 p-3 bg-blue-900/30 border border-blue-600 rounded-lg text-blue-300 text-sm">
                                    <p class="mb-1">🖼 Texture actuelle conservée</p>
                                    <p class="text-xs text-blue-400">Déposez un nouveau fichier PNG pour la remplacer.</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <!-- Géométrie personnalisée (optionnel) -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-purple-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-purple-400 mb-1 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">📐</span> Géométrie personnalisée
                            <span class="text-xs text-gray-500 font-normal ml-1 bg-gray-700 px-2 py-0.5 rounded-full">optionnel</span>
                        </h2>
                        <p class="text-gray-400 text-xs mb-4">Importez un <code class="bg-gray-700 px-1 rounded">.geo.json</code> Minecraft Bedrock (Blockbench ou resource pack vanilla) pour une forme exacte en aperçu et dans le ZIP.</p>
                        <div id="mob-geo-drop"
                            class="border-2 border-dashed border-gray-600 rounded-xl p-4 text-center cursor-pointer transition-all hover:border-purple-500 hover:bg-gray-750"
                            onclick="document.getElementById('mob-geo-file').click()">
                            <input type="file" id="mob-geo-file" name="geometry_file" accept=".json,application/json" class="hidden">
                            <div id="mob-geo-placeholder">
                                <p class="text-gray-400 text-sm">📂 Cliquez ou déposez un fichier <code>.geo.json</code></p>
                                <p class="text-gray-600 text-xs mt-1">Sans ce fichier, un modèle prédéfini (humanoid / quadruped / creeper) est utilisé.</p>
                            </div>
                            <div id="mob-geo-loaded" class="hidden flex-col items-center gap-1">
                                <p class="text-purple-400 text-sm">✓ <span id="mob-geo-name" class="font-mono"></span></p>
                                <p class="text-gray-500 text-xs">Cliquez pour changer</p>
                            </div>
                        </div>
                        @error('geometry_file') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </section>

                    <!-- Stats -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">⚔️</span> Statistiques
                        </h2>
                        <div class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">❤️ Points de vie</label>
                                    <input type="number" name="health" id="mob-health" value="{{ old('health', $mob->health ?? 20) }}" min="1" max="2048"
                                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                    <p class="text-gray-500 text-xs mt-1">1–2048 (20 = humain)</p>
                                    @error('health') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">🏃 Vitesse</label>
                                    <input type="number" name="speed" id="mob-speed" value="{{ old('speed', $mob->speed ?? '0.25') }}" min="0.1" max="2.0" step="0.05"
                                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                    <p class="text-gray-500 text-xs mt-1">0.1–2.0 (0.25 = humain)</p>
                                    @error('speed') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Behavior type -->
                            <div>
                                <p class="text-sm font-medium text-gray-300 mb-2">🧠 Comportement</p>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="behavior_type" value="passive" {{ old('behavior_type', $mob->behavior_type ?? 'passive') === 'passive' ? 'checked' : '' }} class="sr-only peer" id="mob-behavior-passive">
                                        <div class="peer-checked:border-green-500 peer-checked:bg-green-500/10 border-2 border-gray-600 rounded-lg px-3 py-2 text-center transition-all hover:border-gray-500">
                                            <div class="text-xl">😊</div>
                                            <p class="text-xs font-medium text-gray-300 mt-1">Passif</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="behavior_type" value="neutral" {{ old('behavior_type', $mob->behavior_type ?? 'passive') === 'neutral' ? 'checked' : '' }} class="sr-only peer" id="mob-behavior-neutral">
                                        <div class="peer-checked:border-green-500 peer-checked:bg-green-500/10 border-2 border-gray-600 rounded-lg px-3 py-2 text-center transition-all hover:border-gray-500">
                                            <div class="text-xl">😐</div>
                                            <p class="text-xs font-medium text-gray-300 mt-1">Neutre</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="behavior_type" value="hostile" {{ old('behavior_type', $mob->behavior_type ?? 'passive') === 'hostile' ? 'checked' : '' }} class="sr-only peer" id="mob-behavior-hostile">
                                        <div class="peer-checked:border-green-500 peer-checked:bg-green-500/10 border-2 border-gray-600 rounded-lg px-3 py-2 text-center transition-all hover:border-gray-500">
                                            <div class="text-xl">😠</div>
                                            <p class="text-xs font-medium text-gray-300 mt-1">Hostile</p>
                                        </div>
                                    </label>
                                </div>
                                @error('behavior_type') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Attack damage (visible only if hostile/neutral) -->
                            <div id="mob-attack-row" class="hidden">
                                <label class="block text-sm font-medium text-gray-300 mb-1">⚔️ Dégâts d'attaque</label>
                                <input type="number" name="attack_damage" id="mob-attack-damage" value="{{ old('attack_damage', $mob->attack_damage ?? 3) }}" min="1" max="50"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                @error('attack_damage') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Propriétés & Spawn -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">⚙️</span> Propriétés & Spawn
                        </h2>
                        <div class="space-y-5">
                            <!-- Spawnable / Summonable toggles -->
                            <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">🌍</span>
                                    <div>
                                        <p class="font-medium text-gray-200">Peut apparaître naturellement</p>
                                        <p class="text-gray-500 text-xs">Spawn dans le monde</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="mob-spawnable" value="1" class="sr-only peer" {{ old('is_spawnable', ($mob->is_spawnable ?? true) ? '1' : '0') !== '0' ? 'checked' : '' }}>
                                    <div class="w-14 h-7 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600 shadow-inner"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">✨</span>
                                    <div>
                                        <p class="font-medium text-gray-200">Invocable par commande</p>
                                        <p class="text-gray-500 text-xs">/summon custom:{identifier}</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="mob-summonable" value="1" class="sr-only peer" {{ old('is_summonable', ($mob->is_summonable ?? true) ? '1' : '0') !== '0' ? 'checked' : '' }}>
                                    <div class="w-14 h-7 bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600 shadow-inner"></div>
                                </label>
                            </div>

                            <!-- Collision + Scale -->
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">📏 Largeur collision</label>
                                    <input type="number" name="collision_width" value="{{ old('collision_width', $mob->collision_width ?? '0.6') }}" min="0.1" max="4.0" step="0.1"
                                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                    @error('collision_width') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">📐 Hauteur collision</label>
                                    <input type="number" name="collision_height" value="{{ old('collision_height', $mob->collision_height ?? '1.8') }}" min="0.1" max="4.0" step="0.1"
                                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                    @error('collision_height') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">🔭 Échelle</label>
                                    <input type="number" name="scale" value="{{ old('scale', $mob->scale ?? '1.0') }}" min="0.1" max="4.0" step="0.1"
                                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                    @error('scale') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Apparence spawn egg -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">🥚</span> Œuf d'invocation
                        </h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Couleur principale</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="spawn_egg_primary" id="mob-egg-primary" value="{{ old('spawn_egg_primary', $mob->spawn_egg_primary ?? '#a06040') }}"
                                        class="w-12 h-10 rounded-lg cursor-pointer border border-gray-600 bg-gray-700">
                                    <input type="text" id="mob-egg-primary-text" value="{{ old('spawn_egg_primary', $mob->spawn_egg_primary ?? '#a06040') }}"
                                        class="flex-1 bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white font-mono text-sm focus:outline-none focus:border-green-500">
                                </div>
                                @error('spawn_egg_primary') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Couleur secondaire</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="spawn_egg_secondary" id="mob-egg-secondary" value="{{ old('spawn_egg_secondary', $mob->spawn_egg_secondary ?? '#ffffff') }}"
                                        class="w-12 h-10 rounded-lg cursor-pointer border border-gray-600 bg-gray-700">
                                    <input type="text" id="mob-egg-secondary-text" value="{{ old('spawn_egg_secondary', $mob->spawn_egg_secondary ?? '#ffffff') }}"
                                        class="flex-1 bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white font-mono text-sm focus:outline-none focus:border-green-500">
                                </div>
                                @error('spawn_egg_secondary') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Submit -->
                    <button type="submit" id="mob-submit-btn"
                        class="w-full bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white font-bold py-4 px-6 rounded-xl transition-all minecraft-font text-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-purple-600/30 hover:scale-[1.02] active:scale-[0.98]">
                        <span class="text-2xl">⚡</span>
                        <span>{{ isset($mob) ? 'Mettre à jour mon mob' : 'Générer mon mob' }}</span>
                    </button>
                </form>
            </div>

            <!-- Formulaire Item (caché par défaut) -->
            <div id="item-form-col" class="lg:col-span-2 hidden">
                <form id="item-form" action="{{ route('item.create') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <!-- Identité de l'item -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl animate-bounce">🎁</span> Identité de l'item
                        </h2>

                        <div class="space-y-4">
                            <!-- Nom de l'item -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1" for="item-name">
                                    Nom de l'item <span class="text-red-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="item-name"
                                    name="name"
                                    placeholder="Ex: Épée magique"
                                    maxlength="50"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/30 input-enhanced"
                                >
                                @error('name')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Identifiant technique -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1" for="item-identifier">
                                    Identifiant technique <span class="text-red-400">*</span>
                                </label>
                                <div class="flex items-center">
                                    <span class="bg-gray-600 border border-r-0 border-gray-600 rounded-l-lg px-3 py-2 text-gray-400 text-sm">custom:</span>
                                    <input
                                        type="text"
                                        id="item-identifier"
                                        name="identifier"
                                        placeholder="magic_sword"
                                        pattern="[a-z0-9_]+"
                                        class="flex-1 bg-gray-700 border border-gray-600 rounded-r-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                    >
                                </div>
                                @error('identifier')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Propriétés Item -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">⚙️</span> Propriétés
                        </h2>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Max Stack Size -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1" for="item-max-stack-size">
                                    Taille max de stack
                                </label>
                                <input
                                    type="number"
                                    id="item-max-stack-size"
                                    name="max_stack_size"
                                    value="64"
                                    min="1"
                                    max="64"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/30"
                                >
                            </div>

                            <!-- Max Durability -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1" for="item-max-durability">
                                    Durabilité max
                                </label>
                                <input
                                    type="number"
                                    id="item-max-durability"
                                    name="max_durability"
                                    min="0"
                                    placeholder="Ex: 1750"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/30"
                                >
                            </div>

                            <!-- Item Tier -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1" for="item-tier">
                                    Niveau de tool (tier)
                                </label>
                                <input
                                    type="number"
                                    id="item-tier"
                                    name="item_tier"
                                    min="0"
                                    max="10"
                                    placeholder="Ex: 5"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/30"
                                >
                            </div>

                            <!-- Item Multiplier -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1" for="item-multiplier">
                                    Multiplicateur de vitesse
                                </label>
                                <input
                                    type="number"
                                    id="item-multiplier"
                                    name="item_multiplier"
                                    step="0.1"
                                    min="0"
                                    placeholder="Ex: 14.0"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/30"
                                >
                            </div>

                            <!-- Damage -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1" for="item-damage">
                                    Dégâts
                                </label>
                                <input
                                    type="number"
                                    id="item-damage"
                                    name="damage"
                                    min="0"
                                    max="100"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/30"
                                >
                            </div>

                            <!-- Hand Equipped -->
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        id="item-hand-equipped"
                                        name="hand_equipped"
                                        value="1"
                                        class="w-4 h-4 accent-green-500 rounded cursor-pointer"
                                    >
                                    <span class="text-sm font-medium text-gray-300">Équipable en main</span>
                                </label>
                            </div>
                        </div>
                    </section>

                    <!-- Texture de l'item -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">🎨</span> Texture
                        </h2>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Fichier PNG <span class="text-red-400">*</span>
                            </label>
                            <div class="relative border-2 border-dashed border-gray-600 rounded-lg p-6 text-center hover:border-green-500 transition-colors cursor-pointer" id="item-texture-drop">
                                <input
                                    type="file"
                                    name="texture"
                                    id="item-texture"
                                    accept="image/png"
                                    class="hidden"
                                    required
                                >
                                <div id="item-texture-placeholder">
                                    <label for="item-texture" class="cursor-pointer block">
                                        <p class="text-2xl mb-2">🖼️</p>
                                        <p class="text-gray-300 font-medium">Cliquez ou glissez votre texture</p>
                                        <p class="text-gray-500 text-xs mt-2">PNG uniquement, max 512 Ko</p>
                                    </label>
                                </div>
                                <div id="item-texture-preview" class="hidden text-center">
                                    <img id="item-texture-img" src="" alt="Aperçu" class="w-32 h-32 mx-auto mb-2" style="image-rendering: pixelated;">
                                    <p id="item-texture-filename" class="text-green-400 text-sm font-mono">texture.png</p>
                                    <p class="text-gray-500 text-xs mt-2 cursor-pointer hover:text-gray-400" onclick="document.getElementById('item-texture').click()">Cliquez pour changer</p>
                                </div>
                            </div>
                            @error('texture')
                                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </section>

                    <!-- Bouton de soumission -->
                    <button
                        type="submit"
                        id="item-submit-btn"
                        class="w-full bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-bold py-4 px-6 rounded-xl transition-all minecraft-font text-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed btn-minecraft shadow-lg hover:shadow-green-600/30 hover:scale-[1.02] active:scale-[0.98]"
                    >
                        <span class="text-2xl">⬇️</span>
                        <span>Générer mon item</span>
                    </button>

                </form>
            </div>

            <!-- Panneau de prévisualisation -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all shadow-xl">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-xl">👁️</span> Aperçu
                        </h2>

                        <!-- Cube 3D Three.js -->
                        <div class="mb-4">
                            <canvas id="cube-canvas" style="width: 100%; height: 200px; border: 1px solid #374151; border-radius: 0.5rem;"></canvas>
                            <p id="cube-placeholder-text" class="text-center text-gray-500 text-xs mt-2">Uploadez une texture pour voir l'aperçu 3D</p>
                        <p id="mob-preview-label" class="text-center text-purple-400 text-xs mt-1 hidden">Aperçu du mob</p>
                        </div>

                        <!-- Infos Bloc -->
                        <div id="block-preview-info" class="space-y-3 text-sm bg-gray-700/30 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">📛 Nom</span>
                                <span id="preview-name" class="text-white font-medium truncate ml-2 max-w-32">—</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">🔖 ID</span>
                                <span id="preview-id" class="text-green-400 font-mono text-xs truncate ml-2 max-w-32">—</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">🧱 Solidité</span>
                                <span id="preview-solid" class="text-green-400">Oui</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">⛏️ Détruit.</span>
                                <span id="preview-destructible" class="text-green-400">Oui</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">💪 Résistance</span>
                                <span id="preview-resistance" class="text-white font-bold">3</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">💡 Luminosité</span>
                                <span id="preview-light" class="text-yellow-300 font-bold">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">📐 Forme</span>
                                <span id="preview-geometry" class="text-white">—</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Géométrie personnalisée</span>
                                <span id="preview-custom-geometry" class="text-white">Non</span>
                            </div>
                        </div>

                        <!-- Infos Mob -->
                        <div id="mob-preview-info" class="hidden space-y-3 text-sm bg-gray-700/30 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">📛 Nom</span>
                                <span id="mob-preview-name" class="text-white font-medium truncate ml-2 max-w-32">—</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">🔖 ID</span>
                                <span id="mob-preview-id" class="text-purple-400 font-mono text-xs truncate ml-2 max-w-32">—</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">🧍 Modèle</span>
                                <span id="mob-preview-model" class="text-white">—</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">❤️ Vie</span>
                                <span id="mob-preview-health" class="text-red-400 font-bold">20</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">🏃 Vitesse</span>
                                <span id="mob-preview-speed" class="text-yellow-400 font-bold">0.25</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">🧠 Comportement</span>
                                <span id="mob-preview-behavior" class="text-green-400">Passif</span>
                            </div>
                        </div>

                        <hr class="border-gray-700 my-4">

                        <!-- Structure ZIP Bloc -->
                        <div id="block-zip-structure" class="bg-gray-900/50 p-4 rounded-lg">
                            <p class="text-gray-400 text-xs font-medium mb-2 flex items-center gap-2">
                                <span>📦</span> Structure de l'archive :
                            </p>
                            <pre class="text-xs text-gray-500 leading-5 font-mono overflow-x-auto">generated_pack/
├── behavior_pack/
│   ├── manifest.json
│   └── blocks/
│       └── <span id="zip-id" class="text-green-400">mon_bloc</span>.json
└── resource_pack/
    ├── manifest.json
    ├── terrain_texture.json
    └── textures/blocks/
        └── <span id="zip-id2" class="text-green-400">mon_bloc</span>.png</pre>
                        </div>

                        <!-- Structure ZIP Mob -->
                        <div id="mob-zip-structure" class="hidden bg-gray-900/50 p-4 rounded-lg">
                            <p class="text-gray-400 text-xs font-medium mb-2 flex items-center gap-2">
                                <span>📦</span> Structure de l'archive :
                            </p>
                            <pre class="text-xs text-gray-500 leading-5 font-mono overflow-x-auto"><span id="mob-zip-id" class="text-purple-400">mon_mob</span>_mob_pack/
├── behavior_pack/
│   ├── manifest.json
│   └── entities/
│       └── <span id="mob-zip-id2" class="text-purple-400">mon_mob</span>.json
└── resource_pack/
    ├── manifest.json
    ├── entity/
    ├── models/entity/
    ├── render_controllers/
    ├── animation_controllers/
    └── textures/entity/
        └── <span id="mob-zip-id3" class="text-purple-400">mon_mob</span>.png</pre>
                        </div>

                        <!-- Infos Item -->
                        <div id="item-preview-info" class="hidden space-y-3 text-sm bg-gray-700/30 p-4 rounded-lg">
                            <!-- Image aperçu -->
                            <div class="text-center py-3">
                                <div id="item-preview-container" class="mx-auto w-24 h-24 bg-gray-900/50 rounded flex items-center justify-center border border-gray-600">
                                    <img id="item-preview-texture" src="" alt="Aperçu" class="w-20 h-20" style="image-rendering: pixelated; display: none;">
                                    <span id="item-preview-placeholder" class="text-4xl">🎁</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">📛 Nom</span>
                                <span id="item-preview-name" class="text-white font-medium truncate ml-2 max-w-32">—</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 flex items-center gap-2">🔖 ID</span>
                                <span id="item-preview-id" class="text-green-400 font-mono text-xs truncate ml-2 max-w-32">—</span>
                            </div>
                            <div class="text-center py-2 bg-green-900/30 border border-green-600/50 rounded text-green-400 text-xs">
                                Item générique sans propriétés avancées
                            </div>
                        </div>

                        <!-- Structure ZIP Item -->
                        <div id="item-zip-structure" class="hidden bg-gray-900/50 p-4 rounded-lg">
                            <p class="text-gray-400 text-xs font-medium mb-2 flex items-center gap-2">
                                <span>📦</span> Structure de l'archive :
                            </p>
                            <pre class="text-xs text-gray-500 leading-5 font-mono overflow-x-auto"><span id="item-zip-id" class="text-green-400">mon_item</span>_item_pack/
├── behavior_pack/
│   ├── manifest.json
│   └── items/
│       └── <span id="item-zip-id2" class="text-green-400">mon_item</span>.json
└── resource_pack/
    ├── manifest.json
    ├── items.json
    ├── item_texture.json
    └── textures/items/
        └── <span id="item-zip-id3" class="text-green-400">mon_item</span>.png</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast succès -->
    <div id="success-toast" class="fixed top-6 left-1/2 -translate-x-1/2 z-50 transition-all duration-500 opacity-0 -translate-y-4 pointer-events-none">
        <div class="flex items-center gap-3 bg-gradient-to-r from-green-700 to-green-600 text-white text-sm font-medium px-6 py-4 rounded-xl shadow-lg animate-slide-in border border-green-500/30">
            <span class="text-2xl">✅</span>
            <div>
                <p class="font-bold">Téléchargement réussi !</p>
                <p class="text-green-200 text-xs"><strong id="toast-name"></strong> est prêt</p>
            </div>
            <button onclick="closeToast()" class="text-green-200 hover:text-white text-xl leading-none ml-2">×</button>
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
        // --- Restore toggle states when editing (force CSS peer-checked reflow) ---
        @if(isset($block))
        (function () {
            const solidEl = document.getElementById('solid-check');
            const destructEl = document.getElementById('destructible-check');
            solidEl.checked = {{ $block->solid ? 'true' : 'false' }};
            destructEl.checked = {{ $block->destructible ? 'true' : 'false' }};
            solidEl.dispatchEvent(new Event('change'));
            destructEl.dispatchEvent(new Event('change'));
        })();
        @endif

        // --- Géométrie du bloc (JSON) ---
        const geometryInput = document.getElementById('geometry-file');
        const geometryDropZone = document.getElementById('geometry-drop-zone');
        const geometryUploadPlaceholder = document.getElementById('geometry-upload-placeholder');
        const geometryPreviewContainer = document.getElementById('geometry-preview-container');
        const geometryDataInput = document.getElementById('geometry-data');

        let geometryJsonData = null;

        function parseGeometryFile(file) {
            if (!file || file.type !== 'application/json') {
                alert('Veuillez sélectionner un fichier JSON valide');
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                try {
                    geometryJsonData = JSON.parse(e.target.result);

                    // Handle minecraft:geometry format (model file)
                    const geoArray = geometryJsonData['minecraft:geometry'];
                    const legacyKey = !geoArray?.length && Object.keys(geometryJsonData).find(k => k.startsWith('geometry.'));
                    if (geoArray?.length) {
                        const desc = geoArray[0].description || {};
                        const identifier = desc.identifier || 'unknown';
                        const tw = desc.texture_width || 64;
                        const th = desc.texture_height || 64;
                        const cubeCount = (geoArray[0].bones || [])
                            .reduce((n, b) => n + (b.cubes || []).length, 0);

                        document.getElementById('geometry-file-name').textContent = file.name;
                        document.getElementById('geo-identifier').textContent = identifier;
                        document.getElementById('geo-collision').textContent = `${cubeCount} cubes — texture: ${tw}×${th}`;
                    } else if (legacyKey) {
                        const legacyGeo = geometryJsonData[legacyKey];
                        const tw = legacyGeo.texture_width || 64;
                        const th = legacyGeo.texture_height || 64;
                        const cubeCount = (legacyGeo.bones || [])
                            .reduce((n, b) => n + (b.cubes || []).length, 0);

                        document.getElementById('geometry-file-name').textContent = file.name;
                        document.getElementById('geo-identifier').textContent = legacyKey;
                        document.getElementById('geo-collision').textContent = `${cubeCount} cubes — texture: ${tw}×${th}`;
                    } else if (geometryJsonData['minecraft:block']) {
                        // Handle minecraft:block format (block behavior file)
                        const identifier = geometryJsonData['minecraft:block']?.description?.identifier || 'unknown';
                        const collisionBox = geometryJsonData['minecraft:block']?.components?.['minecraft:collision_box'];

                        document.getElementById('geometry-file-name').textContent = file.name;
                        document.getElementById('geo-identifier').textContent = identifier;

                        if (collisionBox) {
                            const origin = collisionBox.origin ? `[${collisionBox.origin.join(',')}]` : '—';
                            const size = collisionBox.size ? `[${collisionBox.size.join(',')}]` : '—';
                            document.getElementById('geo-collision').textContent = `origin: ${origin}, size: ${size}`;
                        } else {
                            document.getElementById('geo-collision').textContent = 'Standard';
                        }
                    }

                    geometryUploadPlaceholder.classList.add('hidden');
                    geometryPreviewContainer.classList.remove('hidden');
                    geometryPreviewContainer.classList.add('flex');

                    geometryDataInput.value = JSON.stringify(geometryJsonData);

                    // If texture is already loaded, rebuild the 3D preview
                    if (textureInput.files[0]) {
                        showPreview(textureInput.files[0]);
                    }

                    updatePreview();
                    console.log('Géométrie chargée:', geometryJsonData);
                } catch (err) {
                    alert('Erreur : le fichier JSON n\'est pas valide. ' + err.message);
                    console.error('JSON parse error:', err);
                }
            };
            reader.readAsText(file);
        }

        geometryInput.addEventListener('change', e => {
            if (e.target.files[0]) {
                parseGeometryFile(e.target.files[0]);
            }
        });

        geometryDropZone.addEventListener('dragover', e => {
            e.preventDefault();
            geometryDropZone.classList.add('drag-over');
        });

        geometryDropZone.addEventListener('dragleave', () => {
            geometryDropZone.classList.remove('drag-over');
        });

        geometryDropZone.addEventListener('drop', e => {
            e.preventDefault();
            geometryDropZone.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                geometryInput.files = dt.files;
                parseGeometryFile(file);
            }
        });

        function clearGeometry() {
            geometryJsonData = null;
            geometryInput.value = '';
            geometryDataInput.value = '';
            geometryUploadPlaceholder.classList.remove('hidden');
            geometryPreviewContainer.classList.add('hidden');
            geometryPreviewContainer.classList.remove('flex');

            // Rebuild with normal cube preview if texture is loaded
            if (textureInput.files[0]) {
                showPreview(textureInput.files[0]);
            }

            updatePreview();
        }

        // --- Restore geometry JSON state when editing ---
        if (geometryDataInput.value) {
            try {
                geometryJsonData = JSON.parse(geometryDataInput.value);
                const geoArray = geometryJsonData['minecraft:geometry'];
                const blockDef = geometryJsonData['minecraft:block'];
                const legacyKey2 = !geoArray?.length && Object.keys(geometryJsonData).find(k => k.startsWith('geometry.'));

                if (geoArray?.length) {
                    const desc = geoArray[0].description || {};
                    document.getElementById('geo-identifier').textContent = desc.identifier || 'unknown';
                    const tw = desc.texture_width || 64;
                    const th = desc.texture_height || 64;
                    const cubeCount = (geoArray[0].bones || []).reduce((n, b) => n + (b.cubes || []).length, 0);
                    document.getElementById('geo-collision').textContent = `${cubeCount} cubes — texture: ${tw}×${th}`;
                } else if (legacyKey2) {
                    const legacyGeo = geometryJsonData[legacyKey2];
                    const tw = legacyGeo.texture_width || 64;
                    const th = legacyGeo.texture_height || 64;
                    const cubeCount = (legacyGeo.bones || []).reduce((n, b) => n + (b.cubes || []).length, 0);
                    document.getElementById('geo-identifier').textContent = legacyKey2;
                    document.getElementById('geo-collision').textContent = `${cubeCount} cubes — texture: ${tw}×${th}`;
                } else if (blockDef) {
                    document.getElementById('geo-identifier').textContent = blockDef?.description?.identifier || 'unknown';
                    const collisionBox = blockDef?.components?.['minecraft:collision_box'];
                    if (collisionBox) {
                        const origin = collisionBox.origin ? `[${collisionBox.origin.join(',')}]` : '—';
                        const size = collisionBox.size ? `[${collisionBox.size.join(',')}]` : '—';
                        document.getElementById('geo-collision').textContent = `origin: ${origin}, size: ${size}`;
                    } else {
                        document.getElementById('geo-collision').textContent = 'Standard';
                    }
                } else {
                    document.getElementById('geo-identifier').textContent = 'géométrie personnalisée';
                    document.getElementById('geo-collision').textContent = '—';
                }

                document.getElementById('geometry-file-name').textContent = '{{ $existingGeometryFilename }}';
                geometryUploadPlaceholder.classList.add('hidden');
                geometryPreviewContainer.classList.remove('hidden');
                geometryPreviewContainer.classList.add('flex');
            } catch(e) {
                console.warn('Could not restore geometry JSON:', e);
            }
        }

        // --- Type de bloc et format ---
        const blockTypeRadios = document.querySelectorAll('input[name="block_type"]');
        const complexFormatRadios = document.querySelectorAll('input[name="complex_format"]');
        const complexOptions = document.getElementById('complex-options');
        const singleUploadZone = document.getElementById('single-upload-zone');
        const separateUploadZone = document.getElementById('separate-upload-zone');

        function updateUploadInterface() {
            const blockType = document.querySelector('input[name="block_type"]:checked').value;
            const complexFormat = document.querySelector('input[name="complex_format"]:checked')?.value || 'net';

            // Show/hide complex options
            complexOptions.classList.toggle('hidden', blockType === 'simple');

            // Show/hide upload zones
            if (blockType === 'simple' || complexFormat === 'net') {
                singleUploadZone.classList.remove('hidden');
                separateUploadZone.classList.add('hidden');
                document.getElementById('upload-hint').textContent = blockType === 'simple'
                    ? '16×16…256×256'
                    : '64×48, 128×96, 256×192… (ratio 4:3)';
            } else {
                singleUploadZone.classList.add('hidden');
                separateUploadZone.classList.remove('hidden');
            }
        }

        blockTypeRadios.forEach(radio => radio.addEventListener('change', updateUploadInterface));
        complexFormatRadios.forEach(radio => radio.addEventListener('change', updateUploadInterface));
        updateUploadInterface(); // Sync UI to pre-selected state on load

        // --- Prévisualisation de la texture ---
        const textureInput = document.getElementById('texture');
        const dropZone     = document.getElementById('drop-zone');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const previewContainer  = document.getElementById('preview-container');
        const texturePreview    = document.getElementById('texture-preview');
        const textureName       = document.getElementById('texture-name');
        const cubePlaceholderText = document.getElementById('cube-placeholder-text');

        const geometryIndicator = document.getElementById('geometry-indicator');
        const geometryIcon      = document.getElementById('geometry-icon');
        const geometryLabel     = document.getElementById('geometry-label');

        // Standard 4:3 cross net layout — width=4C, height=3C
        //        [top]
        //  [left][front][right][back]
        //        [bottom]
        const NET_FACES = [
            { id: 'cube-face-top',    sx: (C) => C,       sy: (C) => 0       },
            { id: 'cube-face-left',   sx: (C) => 0,       sy: (C) => C       },
            { id: 'cube-face-front',  sx: (C) => C,       sy: (C) => C       },
            { id: 'cube-face-right',  sx: (C) => 2 * C,   sy: (C) => C       },
            { id: 'cube-face-back',   sx: (C) => 3 * C,   sy: (C) => C       },
            { id: 'cube-face-bottom', sx: (C) => C,       sy: (C) => 2 * C   },
        ];

        function extractFace(img, sx, sy, C) {
            const c = document.createElement('canvas');
            c.width = c.height = C;
            const ctx = c.getContext('2d');
            ctx.drawImage(img, sx, sy, C, C, 0, 0, C, C);
            const dataUrl = c.toDataURL('image/png');
            console.log(`Extracted face: sx=${sx}, sy=${sy}, C=${C}, dataUrl length: ${dataUrl.length}`);
            return dataUrl;
        }

        function isNetPattern(data, w, h, C) {
            function alphaAt(col, row) {
                const sx = Math.floor((col + 0.5) * C);
                const sy = Math.floor((row + 0.5) * C);
                if (sx >= w || sy >= h) return 0;
                return data[(sy * w + sx) * 4 + 3];
            }
            const empty  = (c, r) => alphaAt(c, r) < 128;
            const opaque = (c, r) => alphaAt(c, r) >= 128;

            // Check if we have a proper cross pattern
            // Standard 4:3 cross: opaque in cross, transparent in corners
            const corners = [alphaAt(0,0), alphaAt(2,0), alphaAt(3,0), alphaAt(0,2), alphaAt(2,2), alphaAt(3,2)];
            const cross = [alphaAt(1,0), alphaAt(0,1), alphaAt(1,1), alphaAt(2,1), alphaAt(3,1), alphaAt(1,2)];

            const transparentCorners = corners.filter(a => a < 128).length;
            const opaqueCross = cross.filter(a => a >= 128).length;

            // Need at least 4/6 corners transparent and all 6 cross positions opaque
            if (transparentCorners >= 4 && opaqueCross === 6) {
                console.log('Net pattern detected with lenient rules');
                return true;
            }

            // Strict check (original)
            if (!empty(0,0) || !empty(2,0) || !empty(3,0)) return false;
            if (!empty(0,2) || !empty(2,2) || !empty(3,2)) return false;
            if (!opaque(1,0)) return false;
            if (!opaque(0,1) || !opaque(1,1) || !opaque(2,1) || !opaque(3,1)) return false;
            if (!opaque(1,2)) return false;
            return true;
        }

        function extractNetFaces(dataUrl) {
            return new Promise(resolve => {
                const img = new Image();
                img.onload = () => {
                    const w = img.width, h = img.height;
                    console.log('Extracting net faces from:', w, 'x', h);

                    // Determine cell size C from image width
                    let C = Math.floor(w / 4);
                    if (C <= 0) {
                        console.warn('Invalid image width for net extraction');
                        resolve({ faces: null });
                        return;
                    }

                    const faces = {};
                    for (const f of NET_FACES) {
                        faces[f.id] = extractFace(img, f.sx(C), f.sy(C), C);
                    }
                    console.log('Extracted net faces:', Object.keys(faces));
                    resolve({ faces });
                };
                img.src = dataUrl;
            });
        }

        function analyzeTexture(dataUrl) {
            return new Promise(resolve => {
                const img = new Image();
                img.onload = () => {
                    const w = img.width, h = img.height;
                    console.log('Image loaded:', w, 'x', h);
                    const canvas = document.createElement('canvas');
                    canvas.width = w; canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    const data = ctx.getImageData(0, 0, w, h).data;

                    // Net texture: exact 4:3 ratio (e.g. 64×48)
                    if (h > 0 && w % 4 === 0 && h % 3 === 0 && (w / 4) === (h / 3)) {
                        console.log('Net texture detected (exact 4:3 ratio)');
                        const C = w / 4;
                        const faces = {};
                        for (const f of NET_FACES) {
                            faces[f.id] = extractFace(img, f.sx(C), f.sy(C), C);
                        }
                        console.log('Extracted faces:', Object.keys(faces));
                        resolve({ shape: 'net', faces });
                        return;
                    }

                    // Net cross pattern on any canvas (e.g. square 64×64 with transparent corners)
                    const C = Math.floor(w / 4);
                    if (C > 0 && isNetPattern(data, w, h, C)) {
                        console.log('Net texture detected (cross pattern with C=' + C + ')');
                        const faces = {};
                        for (const f of NET_FACES) {
                            faces[f.id] = extractFace(img, f.sx(C), f.sy(C), C);
                        }
                        console.log('Extracted faces:', Object.keys(faces));
                        resolve({ shape: 'net', faces });
                        return;
                    }

                    // Transparency scan: count fully transparent and partially transparent pixels
                    let transparent = 0, partialAlpha = 0;
                    for (let i = 3; i < data.length; i += 4) {
                        const a = data[i]; // 0=transparent, 255=opaque
                        if (a < 200) transparent++;
                        if (a > 5 && a < 250) partialAlpha++; // continuous/partial alpha → blend
                    }
                    const total = w * h;
                    const shape = partialAlpha / total > 0.05 ? 'glass' : (transparent / total) > 0.20 ? 'cross' : 'cube';
                    console.log('Texture shape detected:', shape, '(transparent%:', (transparent/total*100).toFixed(1), ', partial%:', (partialAlpha/total*100).toFixed(1) + ')');
                    if (shape === 'glass') {
                        resolve({ shape: 'glass', faces: null });
                    } else {
                        resolve({ shape, faces: null });
                    }
                };
                img.src = dataUrl;
            });
        }

        // --- Custom Geometry Helpers ---
        function loadImage(url) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = url;
            });
        }

        function extractGeoFace(img, tw, th, uv, uvSize) {
            const [u, v] = uv;
            const [du, dv] = uvSize;
            const scale = img.naturalWidth / tw;
            const pw = Math.max(1, Math.round(Math.abs(du) * scale));
            const ph = Math.max(1, Math.round(Math.abs(dv) * scale));
            const sx = Math.round(Math.min(u, u + du) * scale);
            const sy = Math.round(Math.min(v, v + dv) * scale);

            const c = document.createElement('canvas');
            c.width = pw; c.height = ph;
            const ctx = c.getContext('2d');
            ctx.save();
            if (du < 0) { ctx.translate(pw, 0); ctx.scale(-1, 1); }
            if (dv < 0) { ctx.translate(0, ph); ctx.scale(1, -1); }
            ctx.drawImage(img, sx, sy, pw, ph, 0, 0, pw, ph);
            ctx.restore();
            return c.toDataURL('image/png');
        }

        function replaceBlockMesh(newObj) {
            if (blockMesh) scene.remove(blockMesh);
            blockMesh = newObj;
            scene.add(blockMesh);
        }

        async function buildCustomGeoMesh(geoData, textureUrl) {
            const geo = geoData['minecraft:geometry']?.[0];
            if (!geo) return null;

            const desc = geo.description;
            const tw = desc.texture_width || 16;
            const th = desc.texture_height || 16;
            const S = 1 / 16;

            const img = await loadImage(textureUrl);
            const loader = new THREE.TextureLoader();
            const group = new THREE.Group();
            const FACE_MAP = ['east', 'west', 'up', 'down', 'south', 'north'];

            for (const bone of (geo.bones || [])) {
                for (const cube of (bone.cubes || [])) {
                    const [ox, oy, oz] = cube.origin;
                    const [cw, ch, cd] = cube.size;

                    const materials = FACE_MAP.map(face => {
                        const faceUv = cube.uv?.[face];
                        if (!faceUv) return new THREE.MeshPhongMaterial({ color: 0x888888 });
                        const dataUrl = extractGeoFace(img, tw, th, faceUv.uv, faceUv.uv_size);
                        const tex = loader.load(dataUrl);
                        tex.magFilter = THREE.NearestFilter;
                        tex.minFilter = THREE.NearestFilter;
                        return new THREE.MeshPhongMaterial({ map: tex });
                    });

                    const boxGeo = new THREE.BoxGeometry(cw * S, ch * S, cd * S);
                    const mesh = new THREE.Mesh(boxGeo, materials);
                    mesh.position.set(
                        (ox + cw / 2) * S,
                        (oy + ch / 2) * S,
                        (oz + cd / 2) * S
                    );
                    group.add(mesh);
                }
            }

            // Center the group
            const box = new THREE.Box3().setFromObject(group);
            const center = new THREE.Vector3();
            box.getCenter(center);
            group.position.sub(center);

            return group;
        }

        function showGeometryIndicator(shape) {
            const styles = {
                net:   'bg-yellow-900/40 border border-yellow-600 text-yellow-300',
                cube:  'bg-green-900/40 border border-green-700 text-green-300',
            };
            const icons  = { net: '📦', cube: '🧱' };
            const labels = {
                net:   'Bloc complexe : textures différentes sur chaque face',
                cube:  'Bloc simple : même texture sur les 6 faces',
            };
            geometryIndicator.className = 'mt-3 flex items-center gap-2 text-sm px-3 py-2 rounded-lg ' + styles[shape];
            geometryIcon.textContent  = icons[shape];
            geometryLabel.textContent = labels[shape];
            geometryIndicator.classList.remove('hidden');
        }

        function showPreview(file) {
            if (!file || file.type !== 'image/png') return;
            const reader = new FileReader();
            reader.onload = async e => {
                const dataUrl = e.target.result;
                texturePreview.src = dataUrl;
                if (cubePlaceholderText) cubePlaceholderText.classList.add('hidden');
                uploadPlaceholder.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                previewContainer.classList.add('flex');
                textureName.textContent = file.name;

                // Initialize Three.js if not already done
                if (!scene) initThreeJs();

                // Recreate cube if blockMesh was removed (e.g. after mob mode)
                if (!blockMesh || blockMesh.type === 'Group') {
                    if (blockMesh) scene.remove(blockMesh);
                    const geo = new THREE.BoxGeometry(1, 1, 1);
                    const mat = new THREE.MeshPhongMaterial({ color: 0xcccccc });
                    blockMesh = new THREE.Mesh(geo, mat);
                    blockMesh.castShadow = true;
                    scene.add(blockMesh);
                }

                // Check if we have a custom geometry loaded
                const isCustomGeo = geometryJsonData?.['minecraft:geometry'];
                if (isCustomGeo) {
                    // Build and render custom geometry with UV mapping
                    console.log('Building custom geometry with texture mapping');
                    const group = await buildCustomGeoMesh(geometryJsonData, dataUrl);
                    if (group) {
                        replaceBlockMesh(group);
                        const previewGeometry = document.getElementById('preview-geometry');
                        if (previewGeometry) previewGeometry.textContent = '📐 Géométrie personnalisée';
                    }
                } else {
                    // Standard cube/net preview path
                    const blockType = document.querySelector('input[name="block_type"]:checked').value;
                    const complexFormat = document.querySelector('input[name="complex_format"]:checked')?.value || 'net';
                    const textureFormat = blockType === 'simple' ? 'cube' : complexFormat;

                    console.log('Block type:', blockType, 'Complex format:', complexFormat, 'Texture format:', textureFormat);

                    let faces = null;
                    if (textureFormat === 'net') {
                        const { faces: extractedFaces } = await extractNetFaces(dataUrl);
                        faces = extractedFaces;
                    }

                    applyTexturesToCube(textureFormat, faces, dataUrl);

                    showGeometryIndicator(textureFormat);
                    const previewGeometry = document.getElementById('preview-geometry');
                    if (previewGeometry) {
                        const labels = { net: '📦 Bloc complexe (réseau)', cube: '🧱 Bloc simple' };
                        previewGeometry.textContent = labels[textureFormat] ?? '—';
                    }
                }
            };
            reader.readAsDataURL(file);
        }

        textureInput.addEventListener('change', e => showPreview(e.target.files[0]));

        // Update preview when complex format changes
        complexFormatRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (textureInput.files[0]) {
                    showPreview(textureInput.files[0]);
                }
            });
        });

        // --- Gestion des 6 fichiers séparés ---
        const faceInputs = {
            top: document.getElementById('texture-top'),
            bottom: document.getElementById('texture-bottom'),
            left: document.getElementById('texture-left'),
            right: document.getElementById('texture-right'),
            front: document.getElementById('texture-front'),
            back: document.getElementById('texture-back'),
        };

        const faceNames = {
            top: 'top-name',
            bottom: 'bottom-name',
            left: 'left-name',
            right: 'right-name',
            front: 'front-name',
            back: 'back-name',
        };

        let separateFaces = {};

        function updateSeparateFaceCount() {
            const loaded = Object.values(separateFaces).filter(Boolean).length;
            document.getElementById('separate-status').textContent = `✓ ${loaded}/6 fichiers chargés`;
            if (loaded > 0) {
                if (!scene) initThreeJs();
                applySeparateFaces();
            }
        }

        Object.entries(faceInputs).forEach(([face, input]) => {
            input.addEventListener('change', async e => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = ev => {
                        separateFaces[face] = ev.target.result;
                        document.getElementById(faceNames[face]).textContent = file.name;
                        updateSeparateFaceCount();
                    };
                    reader.readAsDataURL(file);
                } else {
                    delete separateFaces[face];
                    document.getElementById(faceNames[face]).textContent = '—';
                    updateSeparateFaceCount();
                }
            });
        });

        function applySeparateFaces() {
            if (!blockMesh) return;
            const textureLoader = new THREE.TextureLoader();
            const materials = [];

            // Three.js BoxGeometry face order: [right, left, top, bottom, front, back]
            const faceMap = ['right', 'left', 'top', 'bottom', 'front', 'back'];

            for (const faceName of faceMap) {
                if (separateFaces[faceName]) {
                    const texture = textureLoader.load(separateFaces[faceName]);
                    texture.magFilter = THREE.NearestFilter;
                    texture.minFilter = THREE.NearestFilter;
                    materials.push(new THREE.MeshPhongMaterial({ map: texture }));
                } else {
                    materials.push(new THREE.MeshPhongMaterial({ color: 0xcccccc }));
                }
            }

            blockMesh.material = materials;
            console.log('Matériaux appliqués (6 fichiers séparés):', materials.length);
        }

        dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                textureInput.files = dt.files;
                showPreview(file);
            }
        });

        // --- Résistance → aperçu ---
        document.getElementById('resistance').addEventListener('input', function () {
            document.getElementById('preview-resistance').textContent = this.value || '0';
        });

        // --- Luminosité → aperçu ---
        document.getElementById('light-emission').addEventListener('input', function () {
            document.getElementById('preview-light').textContent = this.value;
        });

        // --- Mise à jour du panneau de prévisualisation ---
        function updatePreview() {
            const name       = document.getElementById('name').value || '—';
            const identifier = document.getElementById('identifier').value || '—';
            const solid      = document.querySelector('[name="solid"]:checked')?.value === '1';
            const destructible = document.querySelector('[name="destructible"]:checked')?.value === '1';
            const customGeometry = geometryJsonData ? 'Oui ✓' : 'Non';

            document.getElementById('preview-name').textContent = name;
            document.getElementById('preview-id').textContent   = identifier !== '—' ? 'custom:' + identifier : '—';
            document.getElementById('preview-solid').textContent       = solid ? 'Oui ✓' : 'Non ✗';
            document.getElementById('preview-destructible').textContent = destructible ? 'Oui ✓' : 'Non ✗';
            document.getElementById('preview-custom-geometry').textContent = customGeometry;

            const zipId = identifier !== '—' ? identifier : 'mon_bloc';
            document.getElementById('zip-id').textContent  = zipId;
            document.getElementById('zip-id2').textContent = zipId;
        }

        document.getElementById('name').addEventListener('input', updatePreview);
        document.getElementById('identifier').addEventListener('input', updatePreview);
        document.querySelectorAll('[name="solid"], [name="destructible"]').forEach(el => {
            el.addEventListener('change', updatePreview);
        });

        // Auto-génération de l'identifiant depuis le nom
        document.getElementById('name').addEventListener('input', function () {
            if (document.getElementById('identifier').dataset.manual) return;
            const slug = this.value
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '_')
                .replace(/^_|_$/g, '');
            document.getElementById('identifier').value = slug;
            updatePreview();
        });

        document.getElementById('identifier').addEventListener('input', function () {
            this.dataset.manual = 'true';
            updatePreview();
        });

        // Validation client + soumission via fetch pour afficher le popup
        document.getElementById('block-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            const errors = [];
            const name       = document.getElementById('name').value.trim();
            const identifier = document.getElementById('identifier').value.trim();

            const blockType     = document.querySelector('input[name="block_type"]:checked')?.value || 'simple';
            const complexFormat = document.querySelector('input[name="complex_format"]:checked')?.value || 'net';
            const isSeparate    = blockType === 'complex' && complexFormat === 'separate';

            if (!name) errors.push('Le nom du bloc est requis.');
            if (!identifier || !/^[a-z0-9_]+$/.test(identifier)) errors.push("L'identifiant doit contenir uniquement des minuscules et underscores.");

            if (isSeparate) {
                const faceIds = ['texture-top', 'texture-left', 'texture-front', 'texture-right', 'texture-back', 'texture-bottom'];
                const faceLabels = { 'texture-top': 'haut', 'texture-left': 'gauche', 'texture-front': 'avant', 'texture-right': 'droite', 'texture-back': 'arrière', 'texture-bottom': 'bas' };
                const missing = faceIds.filter(id => !document.getElementById(id).files[0]);
                if (missing.length > 0) errors.push('Faces manquantes : ' + missing.map(id => faceLabels[id]).join(', ') + '.');
            } else {
                const texture = document.getElementById('texture').files[0];
                if (!texture) errors.push('Veuillez sélectionner une texture PNG.');
                else if (texture.type !== 'image/png') errors.push('La texture doit être un fichier PNG.');
                else if (texture.size > 512 * 1024) errors.push('La texture ne doit pas dépasser 512 Ko.');
            }

            if (errors.length > 0) {
                const existingAlert = document.getElementById('client-errors');
                if (existingAlert) existingAlert.remove();
                const alert = document.createElement('div');
                alert.id = 'client-errors';
                alert.className = 'bg-red-900/50 border border-red-500 rounded-lg p-4 mb-6';
                alert.innerHTML = '<h2 class="font-bold text-red-400 mb-2">Erreurs :</h2><ul class="list-disc list-inside text-red-300 text-sm space-y-1">'
                    + errors.map(err => `<li>${err}</li>`).join('') + '</ul>';
                this.insertBefore(alert, this.firstChild);
                alert.scrollIntoView({ behavior: 'smooth' });
                return;
            }

            // Spinner
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            document.getElementById('btn-icon').textContent = '⏳';
            document.getElementById('btn-text').textContent = 'Génération en cours…';

            // Assemble net texture from 6 separate faces before submission
            if (isSeparate) {
                const faceOrder = [
                    { id: 'texture-top',    x: 1, y: 0 },
                    { id: 'texture-left',   x: 0, y: 1 },
                    { id: 'texture-front',  x: 1, y: 1 },
                    { id: 'texture-right',  x: 2, y: 1 },
                    { id: 'texture-back',   x: 3, y: 1 },
                    { id: 'texture-bottom', x: 1, y: 2 },
                ];

                const loadImg = file => new Promise(resolve => {
                    const img = new Image();
                    img.onload = () => resolve(img);
                    img.src = URL.createObjectURL(file);
                });

                const faceImgs = await Promise.all(
                    faceOrder.map(f => loadImg(document.getElementById(f.id).files[0]))
                );

                const C = faceImgs[0].naturalWidth;
                const netCanvas = document.createElement('canvas');
                netCanvas.width  = 4 * C;
                netCanvas.height = 3 * C;
                const ctx = netCanvas.getContext('2d');
                faceOrder.forEach(({ x, y }, i) => ctx.drawImage(faceImgs[i], x * C, y * C, C, C));

                const netBlob = await new Promise(resolve => netCanvas.toBlob(resolve, 'image/png'));
                const netFile = new File([netBlob], 'net_texture.png', { type: 'image/png' });
                const dt = new DataTransfer();
                dt.items.add(netFile);
                document.getElementById('texture').files = dt.files;
            }

            let success = false;
            try {
                const formData = new FormData(this);
                // Ensure solid and destructible are properly set
                formData.set('solid', document.getElementById('solid-check').checked ? '1' : '0');
                formData.set('destructible', document.getElementById('destructible-check').checked ? '1' : '0');
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' },
                });

                if (!response.ok) {
                    const contentType = response.headers.get('content-type') || '';
                    let errorMsg = `Erreur serveur : ${response.status}`;
                    if (contentType.includes('application/json')) {
                        const data = await response.json();
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('\n');
                        } else {
                            errorMsg = data.message || errorMsg;
                        }
                    }
                    throw new Error(errorMsg);
                }

                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('application/zip') && !contentType.includes('application/octet-stream')) {
                    throw new Error('Réponse inattendue du serveur — le bloc n\'a peut-être pas été sauvegardé.');
                }

                const blob = await response.blob();
                const url  = URL.createObjectURL(blob);
                const a    = document.createElement('a');
                a.href     = url;
                a.download = identifier + '_pack.zip';
                document.body.appendChild(a);
                a.click();
                setTimeout(() => { document.body.removeChild(a); URL.revokeObjectURL(url); }, 100);

                success = true;
            } catch (err) {
                console.error('Form submission error:', err);
                alert('Une erreur est survenue : ' + err.message);
            } finally {
                btn.disabled = false;
                document.getElementById('btn-icon').textContent = '⚡';
                document.getElementById('btn-text').textContent = 'Générer mon bloc';
            }

            if (success) {
                document.getElementById('toast-name').textContent = name;
                const toast = document.getElementById('success-toast');
                // Apparition
                toast.classList.remove('opacity-0', '-translate-y-4', 'pointer-events-none');
                toast.classList.add('opacity-100', 'translate-y-0');
                // Disparition après 5s
                setTimeout(() => closeToast(), 5000);
            }
        });

        function closeToast() {
            const toast = document.getElementById('success-toast');
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', '-translate-y-4', 'pointer-events-none');
        }

        // --- Three.js 3D cube preview ---
        const canvas = document.getElementById('cube-canvas');
        let scene, camera, renderer, cube, blockMesh;
        let autoRotate = true;
        let userRotX = 0, userRotY = 0;
        let dragging = false, lastX = 0, lastY = 0;

        function initThreeJs() {
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0x1a2e3a);

            const w = canvas.clientWidth;
            const h = canvas.clientHeight;
            camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 1000);
            camera.position.set(1.5, 1.5, 1.5);
            camera.lookAt(0, 0, 0);

            renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
            renderer.setSize(w, h);
            renderer.setPixelRatio(window.devicePixelRatio);
            renderer.shadowMap.enabled = true;

            // Lighting
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight.position.set(5, 5, 5);
            directionalLight.castShadow = true;
            directionalLight.shadow.mapSize.width = 2048;
            directionalLight.shadow.mapSize.height = 2048;
            scene.add(directionalLight);

            // Create block mesh
            const geometry = new THREE.BoxGeometry(1, 1, 1);
            const material = new THREE.MeshPhongMaterial({ color: 0xcccccc });
            blockMesh = new THREE.Mesh(geometry, material);
            blockMesh.castShadow = true;
            blockMesh.receiveShadow = true;
            scene.add(blockMesh);

            // Mouse events
            canvas.addEventListener('mousedown', onMouseDown);
            canvas.addEventListener('mousemove', onMouseMove);
            canvas.addEventListener('mouseup', onMouseUp);
            canvas.addEventListener('touchstart', onTouchStart, { passive: false });
            canvas.addEventListener('touchmove', onTouchMove, { passive: false });
            canvas.addEventListener('touchend', onTouchEnd, { passive: false });

            // Handle window resize
            window.addEventListener('resize', onWindowResize);

            animate();
        }

        function onMouseDown(e) {
            dragging = true;
            autoRotate = false;
            lastX = e.clientX;
            lastY = e.clientY;
            canvas.style.cursor = 'grabbing';
        }

        function onMouseMove(e) {
            if (!dragging) return;
            const dx = e.clientX - lastX;
            const dy = e.clientY - lastY;
            userRotY += dx * 0.01;
            userRotX -= dy * 0.01;
            userRotX = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, userRotX));
            lastX = e.clientX;
            lastY = e.clientY;
        }

        function onMouseUp() {
            dragging = false;
            canvas.style.cursor = 'grab';
        }

        function onTouchStart(e) {
            if (e.touches.length === 1) {
                dragging = true;
                autoRotate = false;
                lastX = e.touches[0].clientX;
                lastY = e.touches[0].clientY;
                e.preventDefault();
            }
        }

        function onTouchMove(e) {
            if (!dragging || e.touches.length !== 1) return;
            const dx = e.touches[0].clientX - lastX;
            const dy = e.touches[0].clientY - lastY;
            userRotY += dx * 0.01;
            userRotX -= dy * 0.01;
            userRotX = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, userRotX));
            lastX = e.touches[0].clientX;
            lastY = e.touches[0].clientY;
            e.preventDefault();
        }

        function onTouchEnd(e) {
            if (e.touches.length === 0) {
                dragging = false;
            }
        }

        function onWindowResize() {
            if (!renderer) return;
            const w = canvas.clientWidth;
            const h = canvas.clientHeight;
            camera.aspect = w / h;
            camera.updateProjectionMatrix();
            renderer.setSize(w, h);
        }

        function animate() {
            requestAnimationFrame(animate);

            if (autoRotate && !dragging) {
                userRotY += 0.005;
            }

            if (blockMesh) {
                blockMesh.rotation.x = userRotX;
                blockMesh.rotation.y = userRotY;
                blockMesh.rotation.z = 0;
            }

            renderer.render(scene, camera);
        }

        function applyTexturesToCube(shape, faces, dataUrl) {
            if (!blockMesh) return;

            const textureLoader = new THREE.TextureLoader();
            const materials = [];

            console.log('Applying texture - Type:', shape);

            if (shape === 'net' && faces) {
                console.log('Bloc complexe - faces extraites:', Object.keys(faces));
                // BoxGeometry face order: [right, left, top, bottom, front, back]
                const faceOrder = ['cube-face-right', 'cube-face-left', 'cube-face-top', 'cube-face-bottom', 'cube-face-front', 'cube-face-back'];
                for (const faceId of faceOrder) {
                    const faceDataUrl = faces[faceId];
                    if (!faceDataUrl) {
                        console.warn('Face manquante:', faceId);
                        const fallback = textureLoader.load(dataUrl);
                        fallback.magFilter = THREE.NearestFilter;
                        fallback.minFilter = THREE.NearestFilter;
                        materials.push(new THREE.MeshPhongMaterial({ map: fallback }));
                    } else {
                        const texture = textureLoader.load(faceDataUrl);
                        texture.magFilter = THREE.NearestFilter;
                        texture.minFilter = THREE.NearestFilter;
                        materials.push(new THREE.MeshPhongMaterial({ map: texture }));
                    }
                }
            } else {
                console.log('Bloc simple - même texture sur les 6 faces');
                const texture = textureLoader.load(dataUrl);
                texture.magFilter = THREE.NearestFilter;
                texture.minFilter = THREE.NearestFilter;
                const material = new THREE.MeshPhongMaterial({ map: texture });
                for (let i = 0; i < 6; i++) materials.push(material);
            }

            blockMesh.material = materials;
            console.log('Matériaux appliqués:', materials.length);
        }

        // Load existing texture when editing
        const blockIdAttr = document.body.getAttribute('data-edit-block-id');
        if (blockIdAttr) {
            async function loadExistingTexture() {
                const textureUrl = '/block/' + blockIdAttr + '/texture';
                const response = await fetch(textureUrl);
                const blob = await response.blob();
                const file = new File([blob], 'texture.png', { type: 'image/png' });

                const dt = new DataTransfer();
                dt.items.add(file);
                document.getElementById('texture').files = dt.files;

                const reader = new FileReader();
                reader.onload = e => {
                    showPreview(file);
                    texturePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }

            window.addEventListener('load', loadExistingTexture);
        }

        // Init
        updatePreview();
        initThreeJs();

        // ====================================================================
        // MOB MODE
        // ====================================================================

        let currentMode = 'block';

        // Check URL parameter for mode
        const urlParams = new URLSearchParams(window.location.search);
        const initialMode = urlParams.get('mode');
        if (initialMode && ['block', 'mob', 'item'].includes(initialMode)) {
            currentMode = initialMode;
        }

        function setMode(mode) {
            currentMode = mode;
            const blockCol  = document.getElementById('block-form-col');
            const mobCol    = document.getElementById('mob-form-col');
            const itemCol   = document.getElementById('item-form-col');
            const blockInfo = document.getElementById('block-preview-info');
            const mobInfo   = document.getElementById('mob-preview-info');
            const itemInfo  = document.getElementById('item-preview-info');
            const blockZip  = document.getElementById('block-zip-structure');
            const mobZip    = document.getElementById('mob-zip-structure');
            const itemZip   = document.getElementById('item-zip-structure');
            const mobLabel  = document.getElementById('mob-preview-label');
            const btnBlock  = document.getElementById('mode-btn-block');
            const btnMob    = document.getElementById('mode-btn-mob');
            const btnItem   = document.getElementById('mode-btn-item');
            const cubeCanvas = document.getElementById('cube-canvas');
            const cubePlaceholder = document.getElementById('cube-placeholder-text');

            if (mode === 'block') {
                blockCol.classList.remove('hidden');
                mobCol.classList.add('hidden');
                itemCol.classList.add('hidden');
                blockInfo.classList.remove('hidden');
                mobInfo.classList.add('hidden');
                itemInfo.classList.add('hidden');
                blockZip.classList.remove('hidden');
                mobZip.classList.add('hidden');
                itemZip.classList.add('hidden');
                mobLabel.classList.add('hidden');
                btnBlock.className = 'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all bg-green-600 text-white shadow-md';
                btnMob.className   = 'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all text-gray-400 hover:text-white hover:bg-gray-700';
                btnItem.className  = 'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all text-gray-400 hover:text-white hover:bg-gray-700';
                document.getElementById('history-link-text').textContent = 'Liste de blocs';
                if (cubeCanvas) cubeCanvas.style.display = 'block';
                if (cubePlaceholder) cubePlaceholder.style.display = 'block';
                if (blockMesh) { scene.remove(blockMesh); blockMesh = null; }
                if (textureInput && textureInput.files[0]) showPreview(textureInput.files[0]);
            } else if (mode === 'mob') {
                blockCol.classList.add('hidden');
                mobCol.classList.remove('hidden');
                itemCol.classList.add('hidden');
                blockInfo.classList.add('hidden');
                mobInfo.classList.remove('hidden');
                itemInfo.classList.add('hidden');
                blockZip.classList.add('hidden');
                mobZip.classList.remove('hidden');
                itemZip.classList.add('hidden');
                mobLabel.classList.remove('hidden');
                btnMob.className   = 'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all bg-purple-600 text-white shadow-md';
                btnBlock.className = 'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all text-gray-400 hover:text-white hover:bg-gray-700';
                btnItem.className  = 'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all text-gray-400 hover:text-white hover:bg-gray-700';
                document.getElementById('history-link-text').textContent = 'Liste de mobs';
                if (cubeCanvas) cubeCanvas.style.display = 'block';
                if (cubePlaceholder) cubePlaceholder.style.display = 'block';
                if (blockMesh) { scene.remove(blockMesh); blockMesh = null; }
                const mobTex = document.getElementById('mob-texture');
                if (mobTex.files[0]) {
                    const r = new FileReader();
                    r.onload = e => rebuildMobPreview(e.target.result);
                    r.readAsDataURL(mobTex.files[0]);
                }
                updateMobPreview();
            } else if (mode === 'item') {
                blockCol.classList.add('hidden');
                mobCol.classList.add('hidden');
                itemCol.classList.remove('hidden');
                blockInfo.classList.add('hidden');
                mobInfo.classList.add('hidden');
                itemInfo.classList.remove('hidden');
                blockZip.classList.add('hidden');
                mobZip.classList.add('hidden');
                itemZip.classList.remove('hidden');
                mobLabel.classList.add('hidden');
                btnItem.className  = 'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all bg-green-600 text-white shadow-md';
                btnBlock.className = 'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all text-gray-400 hover:text-white hover:bg-gray-700';
                btnMob.className   = 'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all text-gray-400 hover:text-white hover:bg-gray-700';
                document.getElementById('history-link-text').textContent = 'Liste d\'items';
                if (cubeCanvas) cubeCanvas.style.display = 'none';
                if (cubePlaceholder) cubePlaceholder.style.display = 'none';
                if (blockMesh) { scene.remove(blockMesh); blockMesh = null; }
            }
        }

        function getMobModelType() {
            return document.querySelector('input[name="model_type"]:checked')?.value || 'humanoid';
        }

        function updateMobPreview() {
            const name       = document.getElementById('mob-name').value || '—';
            const identifier = document.getElementById('mob-identifier').value || '—';
            const model      = getMobModelType();
            const health     = document.getElementById('mob-health').value || '20';
            const speed      = document.getElementById('mob-speed').value || '0.25';
            const behavior   = document.querySelector('input[name="behavior_type"]:checked')?.value || 'passive';
            const modelLabels    = { humanoid: 'Humanoïde 🧍', quadruped: 'Quadrupède 🐷', creeper: 'Creeper 💥' };
            const behaviorLabels = { passive: 'Passif 😊', neutral: 'Neutre 😐', hostile: 'Hostile 😠' };
            document.getElementById('mob-preview-name').textContent     = name;
            document.getElementById('mob-preview-id').textContent       = identifier !== '—' ? 'custom:' + identifier : '—';
            document.getElementById('mob-preview-model').textContent    = modelLabels[model] || model;
            document.getElementById('mob-preview-health').textContent   = health;
            document.getElementById('mob-preview-speed').textContent    = speed;
            document.getElementById('mob-preview-behavior').textContent = behaviorLabels[behavior] || behavior;
            const zipId = identifier !== '—' ? identifier : 'mon_mob';
            ['mob-zip-id', 'mob-zip-id2', 'mob-zip-id3'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = zipId;
            });
        }

        // --- Mob 3D preview (Three.js) ---
        function buildMobMesh(dataUrl, modelType) {
            if (!scene) initThreeJs();
            if (blockMesh) { scene.remove(blockMesh); blockMesh = null; }

            const img = new Image();
            img.onload = function () {
                const tw = img.naturalWidth;
                const th = img.naturalHeight;
                // Reference texture dimensions for the three model types
                const refTw = 64;
                const refTh = (modelType === 'humanoid') ? 64 : 32;
                const scaleX = tw / refTw;
                const scaleY = th / refTh;

                // Crop a pixel region from the skin and return a Three.js texture
                function cropTex(px, py, pw, ph) {
                    const cw = Math.max(1, Math.round(pw * scaleX));
                    const ch = Math.max(1, Math.round(ph * scaleY));
                    const c = document.createElement('canvas');
                    c.width = cw; c.height = ch;
                    c.getContext('2d').drawImage(img,
                        Math.round(px * scaleX), Math.round(py * scaleY),
                        cw, ch, 0, 0, cw, ch);
                    const t = new THREE.CanvasTexture(c);
                    t.magFilter = t.minFilter = THREE.NearestFilter;
                    t.flipY = false;
                    return t;
                }

                // Minecraft Bedrock box UV → Three.js material array
                // For cube size (w×h×d) at UV origin (u,v):
                //   Up(+y)    : (u+d,       v,   w×d)
                //   Down(-y)  : (u+d+w,     v,   w×d)
                //   West(-x)  : (u,         v+d, d×h)
                //   North(-z) : (u+d,       v+d, w×h)  ← front face (mob's face/eyes)
                //   East(+x)  : (u+d+w,     v+d, d×h)
                //   South(+z) : (u+d+w+d,   v+d, w×h)  ← back face
                // Three.js BoxGeometry indices: [+x=East, -x=West, +y=Up, -y=Down, +z=South, -z=North]
                function boxMat(u, v, w, h, d) {
                    const m = (px, py, pw, ph) =>
                        new THREE.MeshLambertMaterial({map: cropTex(px, py, pw, ph), transparent: true, side: THREE.FrontSide});
                    return [
                        m(u+d+w,     v+d, d, h),  // +x  East
                        m(u,         v+d, d, h),  // -x  West
                        m(u+d,       v,   w, d),  // +y  Up
                        m(u+d+w,     v,   w, d),  // -y  Down
                        m(u+d+w+d,   v+d, w, h),  // +z  South (mob back)
                        m(u+d,       v+d, w, h),  // -z  North (mob front/face)
                    ];
                }

                // Scale factor: 1 Minecraft pixel = 1/16 Three.js unit
                const S = 1 / 16;
                const group = new THREE.Group();

                if (modelType === 'humanoid') {
                    // Positions are cube-center = origin + size/2, scaled by S
                    // Head: origin [-4,24,-4], size [8,8,8], uv [0,0]
                    const head = new THREE.Mesh(new THREE.BoxGeometry(8*S, 8*S, 8*S), boxMat(0, 0, 8, 8, 8));
                    head.position.set(0*S, 28*S, 0*S);
                    group.add(head);

                    // Body: origin [-4,12,-2], size [8,12,4], uv [16,16]
                    const body = new THREE.Mesh(new THREE.BoxGeometry(8*S, 12*S, 4*S), boxMat(16, 16, 8, 12, 4));
                    body.position.set(0*S, 18*S, 0*S);
                    group.add(body);

                    // Right arm: origin [-8,12,-2], size [4,12,4], uv [40,16]
                    const rArm = new THREE.Mesh(new THREE.BoxGeometry(4*S, 12*S, 4*S), boxMat(40, 16, 4, 12, 4));
                    rArm.position.set(-6*S, 18*S, 0*S);
                    group.add(rArm);

                    // Left arm: origin [4,12,-2], size [4,12,4], uv [32,48]
                    const lArm = new THREE.Mesh(new THREE.BoxGeometry(4*S, 12*S, 4*S), boxMat(32, 48, 4, 12, 4));
                    lArm.position.set(6*S, 18*S, 0*S);
                    group.add(lArm);

                    // Right leg: origin [-3.9,0,-2], size [4,12,4], uv [0,16]
                    const rLeg = new THREE.Mesh(new THREE.BoxGeometry(4*S, 12*S, 4*S), boxMat(0, 16, 4, 12, 4));
                    rLeg.position.set(-1.9*S, 6*S, 0*S);
                    group.add(rLeg);

                    // Left leg: origin [-0.1,0,-2], size [4,12,4], uv [16,48]
                    const lLeg = new THREE.Mesh(new THREE.BoxGeometry(4*S, 12*S, 4*S), boxMat(16, 48, 4, 12, 4));
                    lLeg.position.set(1.9*S, 6*S, 0*S);
                    group.add(lLeg);

                    // Model spans y=0..32 → center at y=16
                    group.position.set(0, -16*S, 0);

                } else if (modelType === 'quadruped') {
                    // Body: origin [-5,6,-8], size [10,8,16], uv [0,0]
                    const body = new THREE.Mesh(new THREE.BoxGeometry(10*S, 8*S, 16*S), boxMat(0, 0, 10, 8, 16));
                    body.position.set(0*S, 10*S, 0*S);
                    group.add(body);

                    // Head: origin [-4,8,-14], size [8,8,8], uv [0,16]
                    const head = new THREE.Mesh(new THREE.BoxGeometry(8*S, 8*S, 8*S), boxMat(0, 16, 8, 8, 8));
                    head.position.set(0*S, 12*S, -10*S);
                    group.add(head);

                    // Front-right leg: origin [-5,0,-7], size [4,6,4], uv [16,0]
                    const legFR = new THREE.Mesh(new THREE.BoxGeometry(4*S, 6*S, 4*S), boxMat(16, 0, 4, 6, 4));
                    legFR.position.set(-3*S, 3*S, -5*S);
                    group.add(legFR);

                    // Front-left leg: origin [1,0,-7], size [4,6,4], uv [16,0]
                    const legFL = new THREE.Mesh(new THREE.BoxGeometry(4*S, 6*S, 4*S), boxMat(16, 0, 4, 6, 4));
                    legFL.position.set(3*S, 3*S, -5*S);
                    group.add(legFL);

                    // Back-right leg: origin [-5,0,3], size [4,6,4], uv [16,0]
                    const legBR = new THREE.Mesh(new THREE.BoxGeometry(4*S, 6*S, 4*S), boxMat(16, 0, 4, 6, 4));
                    legBR.position.set(-3*S, 3*S, 5*S);
                    group.add(legBR);

                    // Back-left leg: origin [1,0,3], size [4,6,4], uv [16,0]
                    const legBL = new THREE.Mesh(new THREE.BoxGeometry(4*S, 6*S, 4*S), boxMat(16, 0, 4, 6, 4));
                    legBL.position.set(3*S, 3*S, 5*S);
                    group.add(legBL);

                    // Model spans y=0..16 → center at y=8
                    group.position.set(0, -8*S, 0);

                } else { // creeper — 64×32 skin
                    // Head: origin [-4,18,-4], size [8,8,8], uv [0,0]
                    const head = new THREE.Mesh(new THREE.BoxGeometry(8*S, 8*S, 8*S), boxMat(0, 0, 8, 8, 8));
                    head.position.set(0*S, 22*S, 0*S);
                    group.add(head);

                    // Body: origin [-4,6,-2], size [8,12,4], uv [16,16]
                    const body = new THREE.Mesh(new THREE.BoxGeometry(8*S, 12*S, 4*S), boxMat(16, 16, 8, 12, 4));
                    body.position.set(0*S, 12*S, 0*S);
                    group.add(body);

                    // leg0: origin [-4,0,-4], size [4,6,4], uv [0,16]
                    const leg0 = new THREE.Mesh(new THREE.BoxGeometry(4*S, 6*S, 4*S), boxMat(0, 16, 4, 6, 4));
                    leg0.position.set(-2*S, 3*S, -2*S);
                    group.add(leg0);

                    // leg1: origin [0,0,-4], size [4,6,4], uv [0,16]
                    const leg1 = new THREE.Mesh(new THREE.BoxGeometry(4*S, 6*S, 4*S), boxMat(0, 16, 4, 6, 4));
                    leg1.position.set(2*S, 3*S, -2*S);
                    group.add(leg1);

                    // leg2: origin [-4,0,0], size [4,6,4], uv [0,16]
                    const leg2 = new THREE.Mesh(new THREE.BoxGeometry(4*S, 6*S, 4*S), boxMat(0, 16, 4, 6, 4));
                    leg2.position.set(-2*S, 3*S, 2*S);
                    group.add(leg2);

                    // leg3: origin [0,0,0], size [4,6,4], uv [0,16]
                    const leg3 = new THREE.Mesh(new THREE.BoxGeometry(4*S, 6*S, 4*S), boxMat(0, 16, 4, 6, 4));
                    leg3.position.set(2*S, 3*S, 2*S);
                    group.add(leg3);

                    // Model spans y=0..26 → center at y=13
                    group.position.set(0, -13*S, 0);
                }

                blockMesh = group;
                scene.add(blockMesh);
                // Start slightly rotated so the mob's front face is visible
                userRotX = -0.25;
                userRotY = -0.6;
                autoRotate = true;
                document.getElementById('cube-placeholder-text').style.display = 'none';
            };
            img.src = dataUrl;
        }

        // --- Mob geometry JSON 3D preview (parses Minecraft Bedrock .geo.json) ---
        let mobGeoJsonStr = null;

        function buildMobMeshFromGeoJson(geoJsonStr, textureDataUrl) {
            if (!scene) initThreeJs();
            if (blockMesh) { scene.remove(blockMesh); blockMesh = null; }

            let geoData;
            try { geoData = JSON.parse(geoJsonStr); } catch (e) {
                console.error('Invalid geometry JSON:', e);
                buildMobMesh(textureDataUrl, getMobModelType());
                return;
            }

            // Normalise both format variants into { bones, TW, TH }
            // Modern (1.12+): { "minecraft:geometry": [{ "description": {...}, "bones": [...] }] }
            // Legacy (1.8):   { "geometry.name": { "texture_width": N, "bones": [...] } }
            let geo, TW, TH;

            const modernArr = geoData['minecraft:geometry'];
            if (modernArr && modernArr[0] && modernArr[0].bones) {
                geo = modernArr[0];
                const desc = geo.description || {};
                TW = desc.texture_width  || 64;
                TH = desc.texture_height || 64;
            } else {
                // Try legacy: find the first key starting with "geometry."
                const legacyKey = Object.keys(geoData).find(k => k.startsWith('geometry.'));
                if (legacyKey && geoData[legacyKey].bones) {
                    geo = geoData[legacyKey];
                    TW  = geo.texture_width  || 64;
                    TH  = geo.texture_height || 64;
                } else {
                    buildMobMesh(textureDataUrl, getMobModelType());
                    return;
                }
            }

            const img = new Image();
            img.onload = function () {
                const su = img.naturalWidth  / TW;
                const sv = img.naturalHeight / TH;

                function cropTex(px, py, pw, ph) {
                    const cw = Math.max(1, Math.round(pw * su));
                    const ch = Math.max(1, Math.round(ph * sv));
                    const c  = document.createElement('canvas');
                    c.width = cw; c.height = ch;
                    c.getContext('2d').drawImage(img,
                        Math.round(px * su), Math.round(py * sv), cw, ch, 0, 0, cw, ch);
                    const t = new THREE.CanvasTexture(c);
                    t.magFilter = t.minFilter = THREE.NearestFilter;
                    t.flipY = false;
                    return t;
                }

                // Box UV: cube (w×h×d) at UV origin (u,v) → 6 Three.js materials
                // Three.js index order: [+x=East, -x=West, +y=Up, -y=Down, +z=South, -z=North]
                function boxUvMats(u, v, w, h, d) {
                    const m = (px, py, pw, ph) =>
                        new THREE.MeshLambertMaterial({map: cropTex(px, py, pw, ph), transparent: true, side: THREE.FrontSide});
                    return [
                        m(u+d+w,   v+d, d, h),  // +x  East
                        m(u,       v+d, d, h),  // -x  West
                        m(u+d,     v,   w, d),  // +y  Up
                        m(u+d+w,   v,   w, d),  // -y  Down
                        m(u+d+w+d, v+d, w, h),  // +z  South (back)
                        m(u+d,     v+d, w, h),  // -z  North (front)
                    ];
                }

                // Per-face UV: object keyed by face name
                function perFaceUvMats(uvObj) {
                    const faceIdx = {east:0, west:1, up:2, down:3, south:4, north:5};
                    const grey    = new THREE.MeshLambertMaterial({color: 0x888888, transparent: true});
                    const mats    = [grey, grey, grey, grey, grey, grey];
                    for (const [face, fd] of Object.entries(uvObj)) {
                        const idx = faceIdx[face];
                        if (idx === undefined || !fd) continue;
                        const [fu, fv]   = fd.uv       || [0, 0];
                        const [fw, fh]   = fd.uv_size  || [8, 8];
                        mats[idx] = new THREE.MeshLambertMaterial({
                            map: cropTex(fu, fv, fw, fh), transparent: true, side: THREE.FrontSide
                        });
                    }
                    return mats;
                }

                const S     = 1 / 16;
                const group = new THREE.Group();
                let minY = Infinity, maxY = -Infinity, maxExt = 0;

                for (const bone of geo.bones) {
                    for (const cube of bone.cubes || []) {
                        const [ox, oy, oz] = cube.origin;
                        const [sw, sh, sd] = cube.size;

                        let mats;
                        if (Array.isArray(cube.uv)) {
                            mats = boxUvMats(cube.uv[0], cube.uv[1], sw, sh, sd);
                        } else if (cube.uv && typeof cube.uv === 'object') {
                            mats = perFaceUvMats(cube.uv);
                        } else {
                            mats = new THREE.MeshLambertMaterial({color: 0x888888});
                        }

                        const mesh = new THREE.Mesh(new THREE.BoxGeometry(sw*S, sh*S, sd*S), mats);
                        const cx = (ox + sw/2) * S;
                        const cy = (oy + sh/2) * S;
                        const cz = (oz + sd/2) * S;
                        mesh.position.set(cx, cy, cz);
                        group.add(mesh);

                        minY    = Math.min(minY, oy * S);
                        maxY    = Math.max(maxY, (oy + sh) * S);
                        maxExt  = Math.max(maxExt, Math.abs(cx), Math.abs(cz), sw*S/2, sd*S/2);
                    }
                }

                if (!isFinite(minY)) { minY = 0; maxY = 2; }
                const centerY   = (minY + maxY) / 2;
                group.position.y = -centerY;

                const modelH = maxY - minY;
                const dist   = Math.max(modelH * 1.8, maxExt * 3, 1.5);
                camera.position.set(dist * 0.55, dist * 0.35, dist * 0.8);
                camera.lookAt(0, 0, 0);

                blockMesh = group;
                scene.add(blockMesh);
                userRotX = -0.25;
                userRotY = -0.6;
                autoRotate = true;
                document.getElementById('cube-placeholder-text').style.display = 'none';
            };
            img.src = textureDataUrl;
        }

        // --- Spawn egg color extraction ---
        function extractSpawnEggColors(img) {
            const SIZE = 32;
            const c = document.createElement('canvas');
            c.width = SIZE; c.height = SIZE;
            const ctx = c.getContext('2d');
            ctx.drawImage(img, 0, 0, SIZE, SIZE);
            const px = ctx.getImageData(0, 0, SIZE, SIZE).data;

            const Q = 28; // quantization bucket size
            const buckets = {};
            for (let i = 0; i < px.length; i += 4) {
                if (px[i + 3] < 128) continue;
                const r = px[i], g = px[i+1], b = px[i+2];
                const key = `${Math.round(r/Q)*Q},${Math.round(g/Q)*Q},${Math.round(b/Q)*Q}`;
                if (!buckets[key]) buckets[key] = {r:0,g:0,b:0,n:0};
                buckets[key].r += r; buckets[key].g += g; buckets[key].b += b; buckets[key].n++;
            }

            const sorted = Object.values(buckets).sort((a,b) => b.n - a.n);
            if (!sorted.length) return ['#a06040','#ffffff'];

            const toHex = ({r,g,b,n}) =>
                '#' + [r,g,b].map(v => Math.min(255,Math.round(v/n)).toString(16).padStart(2,'0')).join('');

            const p  = sorted[0];
            const primary = toHex(p);

            // Secondary: first color sufficiently distant from primary (Euclidean RGB ≥ 55)
            let secondary = '#ffffff';
            for (let i = 1; i < sorted.length; i++) {
                const s = sorted[i];
                const d = Math.sqrt(((p.r/p.n)-(s.r/s.n))**2 + ((p.g/p.n)-(s.g/s.n))**2 + ((p.b/p.n)-(s.b/s.n))**2);
                if (d >= 55) { secondary = toHex(s); break; }
            }
            return [primary, secondary];
        }

        function applySpawnEggColors(primary, secondary) {
            document.getElementById('mob-egg-primary').value         = primary;
            document.getElementById('mob-egg-primary-text').value    = primary;
            document.getElementById('mob-egg-secondary').value       = secondary;
            document.getElementById('mob-egg-secondary-text').value  = secondary;
        }

        // --- Mob texture upload ---
        const mobTextureInput = document.getElementById('mob-texture');
        const mobDropZone     = document.getElementById('mob-drop-zone');

        function rebuildMobPreview(textureDataUrl) {
            if (mobGeoJsonStr) {
                buildMobMeshFromGeoJson(mobGeoJsonStr, textureDataUrl);
            } else {
                buildMobMesh(textureDataUrl, getMobModelType());
            }
        }

        function showMobTexturePreview(file) {
            const reader = new FileReader();
            reader.onload = e => {
                const dataUrl = e.target.result;
                document.getElementById('mob-texture-preview').src = dataUrl;
                document.getElementById('mob-texture-name').textContent = file.name;
                document.getElementById('mob-upload-placeholder').classList.add('hidden');
                const pc = document.getElementById('mob-preview-container');
                pc.classList.remove('hidden'); pc.classList.add('flex');
                if (currentMode === 'mob') rebuildMobPreview(dataUrl);

                // Auto-detect spawn egg colors from texture
                const img = new Image();
                img.onload = () => {
                    const [primary, secondary] = extractSpawnEggColors(img);
                    applySpawnEggColors(primary, secondary);
                };
                img.src = dataUrl;
            };
            reader.readAsDataURL(file);
        }

        mobTextureInput.addEventListener('change', e => {
            if (e.target.files[0]) showMobTexturePreview(e.target.files[0]);
        });
        mobDropZone.addEventListener('dragover', e => { e.preventDefault(); mobDropZone.classList.add('border-purple-500'); });
        mobDropZone.addEventListener('dragleave', () => mobDropZone.classList.remove('border-purple-500'));
        mobDropZone.addEventListener('drop', e => {
            e.preventDefault();
            mobDropZone.classList.remove('border-purple-500');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                mobTextureInput.files = dt.files;
                showMobTexturePreview(file);
            }
        });

        // --- Mob form event listeners ---
        document.getElementById('mob-name').addEventListener('input', function () {
            if (!document.getElementById('mob-identifier').dataset.manual) {
                document.getElementById('mob-identifier').value = this.value
                    .toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,'')
                    .replace(/[^a-z0-9]+/g,'_').replace(/^_|_$/g,'');
            }
            updateMobPreview();
        });
        document.getElementById('mob-identifier').addEventListener('input', function () {
            this.dataset.manual = 'true'; updateMobPreview();
        });
        document.getElementById('mob-health').addEventListener('input', updateMobPreview);
        document.getElementById('mob-speed').addEventListener('input', updateMobPreview);

        document.querySelectorAll('input[name="model_type"]').forEach(r => r.addEventListener('change', () => {
            updateMobPreview();
            const mobTex = document.getElementById('mob-texture');
            if (mobTex.files[0] && currentMode === 'mob') {
                const rd = new FileReader();
                rd.onload = e => rebuildMobPreview(e.target.result);
                rd.readAsDataURL(mobTex.files[0]);
            }
        }));

        // --- Geo JSON file input ---
        const mobGeoFileInput = document.getElementById('mob-geo-file');
        const mobGeoDrop      = document.getElementById('mob-geo-drop');

        function loadMobGeoFile(file) {
            const reader = new FileReader();
            reader.onload = ev => {
                mobGeoJsonStr = ev.target.result;
                document.getElementById('mob-geo-placeholder').classList.add('hidden');
                const loaded = document.getElementById('mob-geo-loaded');
                loaded.classList.remove('hidden'); loaded.classList.add('flex');
                document.getElementById('mob-geo-name').textContent = file.name;
                if (currentMode === 'mob') {
                    const texInput = document.getElementById('mob-texture');
                    if (texInput.files[0]) {
                        const tr = new FileReader();
                        tr.onload = te => buildMobMeshFromGeoJson(mobGeoJsonStr, te.target.result);
                        tr.readAsDataURL(texInput.files[0]);
                    }
                }
            };
            reader.readAsText(file);
        }

        mobGeoFileInput.addEventListener('change', e => { if (e.target.files[0]) loadMobGeoFile(e.target.files[0]); });
        mobGeoDrop.addEventListener('dragover',  e => { e.preventDefault(); mobGeoDrop.classList.add('border-purple-500'); });
        mobGeoDrop.addEventListener('dragleave', () => mobGeoDrop.classList.remove('border-purple-500'));
        mobGeoDrop.addEventListener('drop', e => {
            e.preventDefault();
            mobGeoDrop.classList.remove('border-purple-500');
            const file = e.dataTransfer.files[0];
            if (!file) return;
            const dt = new DataTransfer(); dt.items.add(file);
            mobGeoFileInput.files = dt.files;
            loadMobGeoFile(file);
        });

        document.querySelectorAll('input[name="behavior_type"]').forEach(r => r.addEventListener('change', () => {
            const aggressive = ['hostile','neutral'].includes(
                document.querySelector('input[name="behavior_type"]:checked')?.value
            );
            document.getElementById('mob-attack-row').classList.toggle('hidden', !aggressive);
            updateMobPreview();
        }));

        // Spawn egg color sync
        ['primary','secondary'].forEach(side => {
            const colorPicker = document.getElementById('mob-egg-' + side);
            const textField   = document.getElementById('mob-egg-' + side + '-text');
            colorPicker.addEventListener('input', () => { textField.value = colorPicker.value; });
            textField.addEventListener('input', () => {
                if (/^#[0-9a-fA-F]{6}$/.test(textField.value)) colorPicker.value = textField.value;
            });
        });

        // --- Mob form submission ---
        document.getElementById('mob-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const name       = document.getElementById('mob-name').value.trim();
            const identifier = document.getElementById('mob-identifier').value.trim();
            const mobTex     = document.getElementById('mob-texture');
            const errors = [];
            if (!name) errors.push('Le nom du mob est requis.');
            if (!identifier || !/^[a-z0-9_]+$/.test(identifier)) errors.push('L\'identifiant est invalide (minuscules et underscores).');
            if (!mobTex.files[0] && !{{ isset($mob) ? 'true' : 'false' }}) errors.push('Une texture PNG est requise.');
            if (errors.length) { alert(errors.join('\n')); return; }

            const btn = document.getElementById('mob-submit-btn');
            btn.disabled = true;
            btn.querySelector('span:last-child').textContent = 'Génération…';

            let success = false;
            try {
                const formData = new FormData(this);
                formData.set('is_spawnable',  document.getElementById('mob-spawnable').checked  ? '1' : '0');
                formData.set('is_summonable', document.getElementById('mob-summonable').checked ? '1' : '0');

                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData,
                });

                if (!response.ok) {
                    const data = await response.json().catch(() => ({}));
                    if (data.errors) {
                        alert(Object.values(data.errors).flat().join('\n'));
                    } else {
                        throw new Error(data.message || 'Erreur serveur (' + response.status + ')');
                    }
                    return;
                }

                const ct = response.headers.get('content-type') || '';
                if (!ct.includes('application/zip') && !ct.includes('application/octet-stream')) {
                    throw new Error('Réponse inattendue du serveur.');
                }

                const blob = await response.blob();
                const url  = URL.createObjectURL(blob);
                const a    = document.createElement('a');
                a.href = url; a.download = identifier + '_mob_pack.zip';
                document.body.appendChild(a); a.click();
                setTimeout(() => { document.body.removeChild(a); URL.revokeObjectURL(url); }, 100);
                success = true;
            } catch (err) {
                console.error(err);
                alert('Une erreur est survenue : ' + err.message);
            } finally {
                btn.disabled = false;
                btn.querySelector('span:last-child').textContent = btn.dataset.label || 'Générer mon mob';
            }

            if (success) {
                document.getElementById('toast-name').textContent = name;
                const toast = document.getElementById('success-toast');
                toast.classList.remove('opacity-0', '-translate-y-4', 'pointer-events-none');
                toast.classList.add('opacity-100', 'translate-y-0');
                setTimeout(() => closeToast(), 5000);
            }
        });

        @if (isset($mob))
        // ── Mob edit mode boot ──────────────────────────────────────────────
        (function () {
            // Store original label on the button
            const submitBtn = document.getElementById('mob-submit-btn');
            submitBtn.dataset.label = submitBtn.querySelector('span:last-child').textContent;

            setMode('mob');

            // Show attack row if hostile/neutral
            @if (in_array($mob->behavior_type, ['hostile', 'neutral']))
            document.getElementById('mob-attack-row').classList.remove('hidden');
            @endif

            // Load existing texture for 3D preview
            fetch('{{ route('mob.texture', $mob->id) }}')
                .then(r => r.blob())
                .then(blob => {
                    const objectUrl = URL.createObjectURL(blob);
                    document.getElementById('mob-texture-preview').src = objectUrl;
                    document.getElementById('mob-texture-name').textContent = '{{ basename($mob->texture_path) }}';
                    document.getElementById('mob-upload-placeholder').classList.add('hidden');
                    const pc = document.getElementById('mob-preview-container');
                    pc.classList.remove('hidden'); pc.classList.add('flex');
                    @if ($mob->geometry_json_path)
                    rebuildMobPreview(objectUrl);
                    @else
                    buildMobMesh(objectUrl, getMobModelType());
                    @endif
                });

            @if ($mob->geometry_json_path)
            // Pre-load existing geometry JSON
            mobGeoJsonStr = @json(\Illuminate\Support\Facades\Storage::get($mob->geometry_json_path));
            document.getElementById('mob-geo-placeholder').classList.add('hidden');
            const geoLoadedEl = document.getElementById('mob-geo-loaded');
            geoLoadedEl.classList.remove('hidden'); geoLoadedEl.classList.add('flex');
            document.getElementById('mob-geo-name').textContent = '{{ basename($mob->geometry_json_path) }}';
            @endif
        })();
        @endif

        // ====================================================================
        // ITEM MODE - Drag and drop + Preview
        // ====================================================================
        function setupItemForm() {
            const itemTextureDrop = document.getElementById('item-texture-drop');
            const itemTextureInput = document.getElementById('item-texture');
            const itemNameInput = document.getElementById('item-name');
            const itemIdInput = document.getElementById('item-identifier');

            if (!itemTextureDrop || !itemTextureInput) return;

            // Drag over
            itemTextureDrop.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
                itemTextureDrop.classList.add('border-green-500', 'bg-green-500/10');
            });

            // Drag leave
            itemTextureDrop.addEventListener('dragleave', (e) => {
                e.preventDefault();
                e.stopPropagation();
                itemTextureDrop.classList.remove('border-green-500', 'bg-green-500/10');
            });

            // Drop
            itemTextureDrop.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                itemTextureDrop.classList.remove('border-green-500', 'bg-green-500/10');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type === 'image/png') {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        itemTextureInput.files = dataTransfer.files;
                        updateItemPreview(file, itemNameInput, itemIdInput);
                    }
                }
            });

            // File change
            itemTextureInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files[0]) {
                    updateItemPreview(e.target.files[0], itemNameInput, itemIdInput);
                }
            });

            // Input changes
            if (itemNameInput) {
                itemNameInput.addEventListener('input', () => {
                    const previewEl = document.getElementById('item-preview-name');
                    if (previewEl) previewEl.textContent = itemNameInput.value || '—';

                    // Auto-fill identifier from name
                    if (itemIdInput) {
                        const identifier = itemNameInput.value
                            .toLowerCase()
                            .trim()
                            .replace(/\s+/g, '_')
                            .replace(/[^a-z0-9_]/g, '');
                        itemIdInput.value = identifier;

                        // Update preview
                        const previewIdEl = document.getElementById('item-preview-id');
                        if (previewIdEl) previewIdEl.textContent = identifier || '—';

                        // Update ZIP structure
                        document.querySelectorAll('#item-zip-id, #item-zip-id2, #item-zip-id3').forEach(el => {
                            el.textContent = identifier || 'mon_item';
                        });
                    }
                });
            }

            if (itemIdInput) {
                itemIdInput.addEventListener('input', () => {
                    const previewEl = document.getElementById('item-preview-id');
                    if (previewEl) previewEl.textContent = itemIdInput.value || '—';
                    const id = itemIdInput.value || 'mon_item';
                    document.querySelectorAll('#item-zip-id, #item-zip-id2, #item-zip-id3').forEach(el => {
                        el.textContent = id;
                    });
                });
            }
        }

        function updateItemPreview(file, nameInput, idInput) {
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                const dataUrl = e.target.result;

                // Show image preview in form (left)
                const previewContainer = document.getElementById('item-texture-preview');
                const placeholder = document.getElementById('item-texture-placeholder');
                const imgElement = document.getElementById('item-texture-img');
                const filenameEl = document.getElementById('item-texture-filename');

                if (imgElement) imgElement.src = dataUrl;
                if (filenameEl) filenameEl.textContent = file.name;
                if (previewContainer) previewContainer.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');

                // Show image preview in right panel (small)
                const previewTexture = document.getElementById('item-preview-texture');
                const previewPlaceholder = document.getElementById('item-preview-placeholder');
                if (previewTexture) {
                    previewTexture.src = dataUrl;
                    previewTexture.style.display = 'block';
                }
                if (previewPlaceholder) previewPlaceholder.style.display = 'none';

                // Update preview info
                const nameEl = document.getElementById('item-preview-name');
                const idEl = document.getElementById('item-preview-id');
                if (nameEl) nameEl.textContent = nameInput.value || '—';
                if (idEl) idEl.textContent = idInput.value || '—';

                // Update ZIP structure
                const id = idInput.value || 'mon_item';
                document.querySelectorAll('#item-zip-id, #item-zip-id2, #item-zip-id3').forEach(el => {
                    el.textContent = id;
                });
            };
            reader.readAsDataURL(file);
        }

        // Validation pour max stack size
        const itemMaxStackSize = document.getElementById('item-max-stack-size');
        if (itemMaxStackSize) {
            itemMaxStackSize.addEventListener('input', (e) => {
                let value = parseInt(e.target.value);
                if (value > 64) {
                    e.target.value = 64;
                } else if (value < 1 && e.target.value !== '') {
                    e.target.value = 1;
                }
            });

            itemMaxStackSize.addEventListener('blur', (e) => {
                if (e.target.value === '') {
                    e.target.value = 64;
                }
            });
        }

        // Setup when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setMode(currentMode);
                setupItemForm();
            });
        } else {
            setMode(currentMode);
            setupItemForm();
        }
    </script>
</body>
</html>
