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
        .drag-over { border-color: #22c55e !important; background-color: #f0fdf4 !important; }

        /* 3D cube preview */
        #cube-canvas {
            width: 100%;
            height: 280px;
            display: block;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #1a2e3a 0%, #2d3d47 100%);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">

    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 py-4 px-6">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="w-10 h-10 bg-green-600 rounded grid grid-cols-2 gap-0.5 p-1">
                <div class="bg-green-400 rounded-sm"></div>
                <div class="bg-green-700 rounded-sm"></div>
                <div class="bg-green-700 rounded-sm"></div>
                <div class="bg-green-400 rounded-sm"></div>
            </div>
            <div>
                <h1 class="text-xl font-bold minecraft-font text-green-400">Minecraft Block Generator</h1>
                <p class="text-xs text-gray-400">Bedrock Edition — Créez votre bloc personnalisé</p>
            </div>
            <a href="{{ route('block.index') }}"
               class="bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                📋 Historique
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">

        <!-- Erreurs de validation -->
        @if ($errors->any())
            <div class="bg-red-900/50 border border-red-500 rounded-lg p-4 mb-6">
                <h2 class="font-bold text-red-400 mb-2">Erreurs de validation :</h2>
                <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Formulaire -->
            <div class="lg:col-span-2">
                <form
                    id="block-form"
                    action="{{ route('block.create') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    novalidate
                >
                    @csrf

                    <!-- Section : Identité du bloc -->
                    <!-- Section : Identité du bloc -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">🧱</span> Identité du bloc
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
                                    value="{{ old('name') }}"
                                    placeholder="Ex: Pierre volcanique"
                                    maxlength="50"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('name') border-red-500 @enderror"
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
                                        value="{{ old('identifier') }}"
                                        placeholder="volcanic_rock"
                                        pattern="[a-z0-9_]+"
                                        class="flex-1 bg-gray-700 border border-gray-600 rounded-r-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('identifier') border-red-500 @enderror"
                                    >
                                </div>
                                @error('identifier')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-xs mt-1">Minuscules et underscores uniquement (ex: <code class="text-green-400">my_block</code>).</p>
                            </div>
                        </div>
                    </section>

                    <!-- Section : Texture -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">🎨</span> Texture
                        </h2>

                        <!-- Format selection -->
                        <div class="mb-6 p-4 bg-gray-700/50 rounded-lg border border-gray-600">
                            <p class="text-sm font-medium text-gray-300 mb-3">Type de bloc :</p>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-700/50 p-2 rounded">
                                    <input type="radio" name="block_type" value="simple" checked class="w-4 h-4 accent-green-500" id="block-simple">
                                    <span class="text-gray-300">
                                        <span class="font-medium">🧱 Bloc simple</span>
                                        <span class="text-xs text-gray-500 ml-2">(même texture sur les 6 faces — ex: terre, pierre)</span>
                                    </span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-700/50 p-2 rounded">
                                    <input type="radio" name="block_type" value="complex" class="w-4 h-4 accent-green-500" id="block-complex">
                                    <span class="text-gray-300">
                                        <span class="font-medium">📦 Bloc complexe</span>
                                        <span class="text-xs text-gray-500 ml-2">(6 faces différentes — ex: coffre, four)</span>
                                    </span>
                                </label>
                            </div>

                            <!-- Sub-options for complex blocks -->
                            <div id="complex-options" class="hidden mt-4 pt-4 border-t border-gray-500 space-y-2">
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
                                class="border-2 border-dashed border-gray-600 rounded-xl p-8 text-center cursor-pointer transition-colors hover:border-green-500 @error('texture') border-red-500 @enderror"
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
                                    <div class="text-5xl mb-3">📁</div>
                                    <p class="text-gray-300 font-medium">Cliquez ou glissez-déposez votre texture</p>
                                    <p class="text-gray-500 text-sm mt-1">PNG uniquement — max 512 Ko</p>
                                    <p class="text-gray-600 text-xs mt-1" id="upload-hint">16×16…256×256</p>
                                </div>
                                <div id="preview-container" class="hidden flex-col items-center gap-3">
                                    <img id="texture-preview" src="" alt="Prévisualisation" class="w-32 h-32 object-contain rounded-lg border-2 border-green-500" style="image-rendering: pixelated;">
                                    <p id="texture-name" class="text-green-400 text-sm"></p>
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

                    <!-- Section : Propriétés -->
                    <section class="bg-gray-800 rounded-xl p-6 mb-6 border border-gray-700">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">⚙️</span> Propriétés du bloc
                        </h2>

                        <div class="space-y-5">
                            <!-- Solidité -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-200">Solidité</p>
                                    <p class="text-gray-500 text-xs">Le bloc possède une hitbox et bloque les joueurs</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="solid" value="0">
                                    <input
                                        type="checkbox"
                                        name="solid"
                                        value="1"
                                        class="sr-only peer"
                                        {{ old('solid', '1') == '1' ? 'checked' : '' }}
                                    >
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                </label>
                            </div>

                            <!-- Destructible -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-200">Destructible</p>
                                    <p class="text-gray-500 text-xs">Le bloc peut être cassé par les joueurs</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="destructible" value="0">
                                    <input
                                        type="checkbox"
                                        name="destructible"
                                        value="1"
                                        class="sr-only peer"
                                        {{ old('destructible', '1') == '1' ? 'checked' : '' }}
                                    >
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                </label>
                            </div>

                            <!-- Résistance -->
                            <div>
                                <div class="flex justify-between mb-2">
                                    <div>
                                        <p class="font-medium text-gray-200">Résistance aux explosions</p>
                                        <p class="text-gray-500 text-xs">Résistance aux TNT et creepers</p>
                                    </div>
                                    <span id="resistance-value" class="text-green-400 font-bold text-lg">{{ old('resistance', 3) }}</span>
                                </div>
                                <input
                                    type="range"
                                    name="resistance"
                                    id="resistance"
                                    min="0"
                                    max="100"
                                    step="0.5"
                                    value="{{ old('resistance', 3) }}"
                                    class="w-full h-2 bg-gray-600 rounded-lg appearance-none cursor-pointer accent-green-500"
                                >
                                <div class="flex justify-between text-gray-600 text-xs mt-1">
                                    <span>0 (fragile)</span>
                                    <span>50 (pierre)</span>
                                    <span>100 (bedrock)</span>
                                </div>
                                @error('resistance')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Bouton de génération -->
                    <button
                        type="submit"
                        id="submit-btn"
                        class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-4 px-6 rounded-xl transition-colors minecraft-font text-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span id="btn-icon">⚡</span>
                        <span id="btn-text">Générer mon bloc</span>
                    </button>

                </form>
            </div>

            <!-- Panneau de prévisualisation -->
            <div class="lg:col-span-1">
                <div class="sticky top-6">
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font">Aperçu</h2>

                        <!-- Cube 3D Three.js -->
                        <div class="mb-4">
                            <canvas id="cube-canvas"></canvas>
                            <p id="cube-placeholder-text" class="text-center text-gray-500 text-xs mt-2">Uploadez une texture pour voir l'aperçu 3D</p>
                        </div>

                        <!-- Infos -->
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Nom</span>
                                <span id="preview-name" class="text-white font-medium truncate ml-2 max-w-32">—</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Identifiant</span>
                                <span id="preview-id" class="text-green-400 font-mono text-xs truncate ml-2 max-w-32">—</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Solidité</span>
                                <span id="preview-solid" class="text-white">Oui</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Destructible</span>
                                <span id="preview-destructible" class="text-white">Oui</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Résistance</span>
                                <span id="preview-resistance" class="text-white">3</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Forme</span>
                                <span id="preview-geometry" class="text-white">—</span>
                            </div>
                        </div>

                        <hr class="border-gray-700 my-4">

                        <!-- Structure ZIP attendue -->
                        <div>
                            <p class="text-gray-400 text-xs font-medium mb-2">Structure de l'archive :</p>
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
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast succès -->
    <div id="success-toast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 transition-all duration-500 opacity-0 -translate-y-4 pointer-events-none">
        <div class="flex items-center gap-3 bg-green-700 text-white text-sm font-medium px-5 py-3 rounded-xl shadow-lg">
            <span>✅ Téléchargement de <strong id="toast-name"></strong> réussi !</span>
            <button onclick="closeToast()" class="text-green-200 hover:text-white text-lg leading-none">&times;</button>
        </div>
    </div>

    <footer class="text-center text-gray-600 text-xs py-6">
        Minecraft Block Generator — Bedrock Edition &bull; Laravel {{ app()->version() }}
    </footer>


    <script>
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

                // Get user-selected block type and complex format
                const blockType = document.querySelector('input[name="block_type"]:checked').value;
                const complexFormat = document.querySelector('input[name="complex_format"]:checked')?.value || 'net';
                const textureFormat = blockType === 'simple' ? 'cube' : complexFormat;

                console.log('Block type:', blockType, 'Complex format:', complexFormat, 'Texture format:', textureFormat);

                let faces = null;
                if (textureFormat === 'net') {
                    // Extract the 6 faces from the net texture
                    const { faces: extractedFaces } = await extractNetFaces(dataUrl);
                    faces = extractedFaces;
                }

                // Apply textures to Three.js cube
                if (!blockMesh) initThreeJs();
                applyTexturesToCube(textureFormat, faces, dataUrl);

                showGeometryIndicator(textureFormat);
                const previewGeometry = document.getElementById('preview-geometry');
                if (previewGeometry) {
                    const labels = { net: '📦 Bloc complexe (réseau)', cube: '🧱 Bloc simple' };
                    previewGeometry.textContent = labels[textureFormat] ?? '—';
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
                if (!blockMesh) initThreeJs();
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

        // --- Résistance slider ---
        const resistanceSlider = document.getElementById('resistance');
        const resistanceValue  = document.getElementById('resistance-value');
        const previewResistance = document.getElementById('preview-resistance');

        resistanceSlider.addEventListener('input', () => {
            resistanceValue.textContent  = resistanceSlider.value;
            previewResistance.textContent = resistanceSlider.value;
        });

        // --- Mise à jour du panneau de prévisualisation ---
        function updatePreview() {
            const name       = document.getElementById('name').value || '—';
            const identifier = document.getElementById('identifier').value || '—';
            const solid      = document.querySelector('[name="solid"]:checked')?.value === '1';
            const destructible = document.querySelector('[name="destructible"]:checked')?.value === '1';

            document.getElementById('preview-name').textContent = name;
            document.getElementById('preview-id').textContent   = identifier !== '—' ? 'custom:' + identifier : '—';
            document.getElementById('preview-solid').textContent       = solid ? 'Oui ✓' : 'Non ✗';
            document.getElementById('preview-destructible').textContent = destructible ? 'Oui ✓' : 'Non ✗';

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
            const texture    = document.getElementById('texture').files[0];

            if (!name) errors.push('Le nom du bloc est requis.');
            if (!identifier || !/^[a-z0-9_]+$/.test(identifier)) errors.push("L'identifiant doit contenir uniquement des minuscules et underscores.");
            if (!texture) errors.push('Veuillez sélectionner une texture PNG.');
            else if (texture.type !== 'image/png') errors.push('La texture doit être un fichier PNG.');
            else if (texture.size > 512 * 1024) errors.push('La texture ne doit pas dépasser 512 Ko.');

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

            let success = false;
            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, { method: 'POST', body: formData });

                if (!response.ok) throw new Error('Erreur serveur : ' + response.status);

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

            blockMesh.rotation.x = userRotX;
            blockMesh.rotation.y = userRotY;
            blockMesh.rotation.z = 0;

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

        // Init
        updatePreview();
    </script>
</body>
</html>
