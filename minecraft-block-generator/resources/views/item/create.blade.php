<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur d'Items Minecraft</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .minecraft-font { font-family: 'Courier New', monospace; }

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

        .input-enhanced {
            transition: all 0.3s ease;
        }
        .input-enhanced:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
        }

        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

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

        .texture-preview {
            width: 96px;
            height: 96px;
            background-color: #4b5563;
            background-size: cover;
            background-position: center;
            image-rendering: pixelated;
            border: 3px solid #22c55e;
            border-radius: 0.5rem;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen" @if (isset($item)) data-edit-item-id="{{ $item->id }}" @endif>
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 py-4 px-6 sticky top-0 z-40 backdrop-blur-md bg-opacity-95">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center text-2xl animate-float shadow-lg">
                    🎁
                </div>
                <div>
                    <h1 class="text-xl font-bold minecraft-font text-green-400 flex items-center gap-2">
                        <span class="text-2xl">🎁</span> Générateur d'items Minecraft
                    </h1>
                    <p class="text-xs text-gray-400">Bedrock Edition — Créez votre item personnalisé</p>
                </div>
            </div>
            <a href="{{ route('block.index') }}"
               class="bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all hover:scale-105 hover:shadow-lg flex items-center gap-2">
                <span>📜</span> Liste
            </a>
        </div>
    </header>

    <div class="max-w-4xl mx-auto py-8 px-4">
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

        <form
            action="{{ isset($item) ? route('item.update', $item->id) : route('item.create') }}"
            method="POST"
            enctype="multipart/form-data"
            novalidate
        >
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulaire -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Identité -->
                    <section class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl animate-bounce">🎁</span> Identité de l'item
                        </h2>

                        <div class="space-y-4">
                            <!-- Nom -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1" for="name">
                                    Nom de l'item <span class="text-red-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', $item->name ?? '') }}"
                                    placeholder="Ex: Épée magique"
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
                                        value="{{ old('identifier', $item->identifier ?? '') }}"
                                        placeholder="magic_sword"
                                        pattern="[a-z0-9_]+"
                                        {{ isset($item) ? 'readonly' : '' }}
                                        class="flex-1 bg-gray-700 border border-gray-600 rounded-r-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('identifier') border-red-500 @enderror {{ isset($item) ? 'bg-gray-600 cursor-not-allowed' : '' }}"
                                    >
                                </div>
                                @error('identifier')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-xs mt-1">{{ isset($item) ? 'Cet identifiant ne peut pas être modifié.' : 'Minuscules et underscores uniquement (ex: <code class="text-green-400">my_item</code>).' }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Texture -->
                    <section class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover hover:border-green-500/50 transition-all">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font flex items-center gap-2">
                            <span class="text-2xl">🎨</span> Texture
                        </h2>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Fichier PNG {{ !isset($item) ? '<span class="text-red-400">*</span>' : '(optionnel pour modifier)' }}
                            </label>
                            <div class="relative border-2 border-dashed border-gray-600 rounded-lg p-6 text-center hover:border-green-500 transition-colors cursor-pointer" id="texture-drop">
                                <input
                                    type="file"
                                    name="texture"
                                    id="texture"
                                    accept="image/png"
                                    class="hidden"
                                    {{ !isset($item) ? 'required' : '' }}
                                >
                                <label for="texture" class="cursor-pointer block">
                                    <p class="text-2xl mb-2">🖼️</p>
                                    <p class="text-gray-300 font-medium">Cliquez ou glissez votre texture</p>
                                    <p class="text-gray-500 text-xs mt-2">PNG uniquement, max 512 Ko</p>
                                </label>
                            </div>
                            @error('texture')
                                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-3">Recommandé : 16×16, 32×32 ou 64×64 pixels pour un aspect Minecraft authentique.</p>
                        </div>
                    </section>
                </div>

                <!-- Aperçu -->
                <div class="lg:col-span-1">
                    <section class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover sticky top-24">
                        <h2 class="text-lg font-semibold text-green-400 mb-4 minecraft-font text-center">Aperçu</h2>

                        <div id="preview-container" class="mb-6">
                            @if (isset($item))
                                <img src="{{ route('item.texture', $item->id) }}" alt="Aperçu" class="texture-preview">
                            @else
                                <div class="texture-preview opacity-50 flex items-center justify-center text-gray-500">
                                    <span class="text-center">
                                        <p class="text-2xl mb-1">❓</p>
                                        <p class="text-xs">Aperçu de texture</p>
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Bouton de soumission -->
                        <button
                            type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition-all hover:shadow-lg hover:scale-105 btn-minecraft flex items-center justify-center gap-2"
                        >
                            <span class="text-xl">{{ isset($item) ? '💾' : '⬇️' }}</span>
                            {{ isset($item) ? 'Mettre à jour et télécharger' : 'Créer et télécharger' }}
                        </button>
                    </section>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Texture file handling
        const textureInput = document.getElementById('texture');
        const previewContainer = document.getElementById('preview-container');
        const textureDrop = document.getElementById('texture-drop');

        function updatePreview(file) {
            if (file && file.type === 'image/png') {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Aperçu" class="texture-preview">`;
                };
                reader.readAsDataURL(file);
            }
        }

        textureInput.addEventListener('change', (e) => {
            if (e.target.files[0]) {
                updatePreview(e.target.files[0]);
            }
        });

        textureDrop.addEventListener('dragover', (e) => {
            e.preventDefault();
            textureDrop.classList.add('border-green-500', 'bg-green-500/10');
        });

        textureDrop.addEventListener('dragleave', () => {
            textureDrop.classList.remove('border-green-500', 'bg-green-500/10');
        });

        textureDrop.addEventListener('drop', (e) => {
            e.preventDefault();
            textureDrop.classList.remove('border-green-500', 'bg-green-500/10');
            const file = e.dataTransfer.files[0];
            if (file && file.type === 'image/png') {
                textureInput.files = e.dataTransfer.files;
                updatePreview(file);
            }
        });
    </script>
</body>
</html>
