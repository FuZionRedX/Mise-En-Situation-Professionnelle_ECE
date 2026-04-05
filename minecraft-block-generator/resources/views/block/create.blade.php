<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de Blocs Minecraft</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .minecraft-font { font-family: 'Courier New', monospace; }
        .drag-over { border-color: #22c55e !important; background-color: #f0fdf4 !important; }
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
            <a href="{{ route('block.history') }}"
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
                                <p class="text-gray-500 text-sm mt-1">PNG uniquement — dimensions carrées — max 512 Ko</p>
                                <p class="text-gray-600 text-xs mt-1">16×16 · 32×32 · 64×64 · 128×128 pixels</p>
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

                        <!-- Cube 3D isométrique simulé -->
                        <div class="flex justify-center mb-4">
                            <div class="relative w-32 h-32" id="cube-preview">
                                <!-- Face top -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-20 h-20 rounded-sm shadow-lg border-2 border-gray-600 overflow-hidden" id="cube-face" style="image-rendering: pixelated;">
                                        <div class="w-full h-full bg-gray-600 flex items-center justify-center text-4xl" id="cube-placeholder">?</div>
                                        <img id="cube-texture" src="" alt="" class="w-full h-full object-cover hidden" style="image-rendering: pixelated;">
                                    </div>
                                </div>
                            </div>
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
        // --- Prévisualisation de la texture ---
        const textureInput = document.getElementById('texture');
        const dropZone     = document.getElementById('drop-zone');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const previewContainer  = document.getElementById('preview-container');
        const texturePreview    = document.getElementById('texture-preview');
        const textureName       = document.getElementById('texture-name');
        const cubeTexture       = document.getElementById('cube-texture');
        const cubePlaceholder   = document.getElementById('cube-placeholder');

        function showPreview(file) {
            if (!file || file.type !== 'image/png') return;
            const reader = new FileReader();
            reader.onload = e => {
                texturePreview.src = e.target.result;
                cubeTexture.src    = e.target.result;
                uploadPlaceholder.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                previewContainer.classList.add('flex');
                cubeTexture.classList.remove('hidden');
                cubePlaceholder.classList.add('hidden');
                textureName.textContent = file.name;
            };
            reader.readAsDataURL(file);
        }

        textureInput.addEventListener('change', e => showPreview(e.target.files[0]));

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

        // Init
        updatePreview();
    </script>
</body>
</html>
