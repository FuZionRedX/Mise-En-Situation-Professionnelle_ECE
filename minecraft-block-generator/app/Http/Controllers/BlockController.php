<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockRequest;
use App\Models\Block;
use App\Services\BlockZipService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BlockController extends Controller
{
    public function __construct(private BlockZipService $zipService) {}

    /**
     * Affiche le formulaire de création de bloc.
     */
    public function index()
    {
        return view('block.create');
    }

    /**
     * Génère les fichiers du pack Minecraft, sauvegarde en base et renvoie l'archive ZIP.
     */
    public function create(BlockRequest $request): BinaryFileResponse
    {
        $identifier = $request->input('identifier');

        // Stocker la texture de façon permanente
        $texturePath = $request->file('texture')->storeAs(
            'textures',
            $identifier . '_' . time() . '.png'
        );

        // Sauvegarder en base
        Block::create([
            'name'         => $request->input('name'),
            'identifier'   => $identifier,
            'solid'        => (bool) $request->input('solid'),
            'destructible' => (bool) $request->input('destructible'),
            'resistance'   => (float) $request->input('resistance'),
            'texture_path' => $texturePath,
        ]);

        // Générer le ZIP
        $zipPath = $this->zipService->generate(
            name:         $request->input('name'),
            identifier:   $identifier,
            solid:        (bool) $request->input('solid'),
            destructible: (bool) $request->input('destructible'),
            resistance:   (float) $request->input('resistance'),
            texture:      $request->file('texture'),
        );

        return response()->download($zipPath, $identifier . '_pack.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Affiche l'historique des blocs générés.
     */
    public function history()
    {
        $blocks = Block::latest()->paginate(12);
        return view('block.history', compact('blocks'));
    }

    /**
     * Re-télécharge un bloc existant depuis l'historique.
     */
    public function download(Block $block): BinaryFileResponse
    {
        $texturePath = Storage::path($block->texture_path);

        if (!file_exists($texturePath)) {
            abort(404, 'Texture introuvable pour ce bloc.');
        }

        $zipPath = $this->zipService->generateFromPath(
            name:         $block->name,
            identifier:   $block->identifier,
            solid:        $block->solid,
            destructible: $block->destructible,
            resistance:   $block->resistance,
            texturePath:  $texturePath,
        );

        return response()->download($zipPath, $block->identifier . '_pack.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Supprime un bloc de l'historique.
     */
    public function destroy(Block $block)
    {
        Storage::delete($block->texture_path);
        $block->delete();

        return redirect()->route('block.history')->with('success', 'Bloc supprimé.');
    }
}
