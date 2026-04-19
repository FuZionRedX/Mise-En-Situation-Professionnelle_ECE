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

        $geometry = $this->detectGeometry($request->file('texture')->getRealPath());

        // Sauvegarder en base
        Block::create([
            'name'         => $request->input('name'),
            'identifier'   => $identifier,
            'solid'        => (bool) $request->input('solid'),
            'destructible' => (bool) $request->input('destructible'),
            'resistance'   => (float) $request->input('resistance'),
            'texture_path' => $texturePath,
            'geometry'     => $geometry,
        ]);

        // Générer le ZIP
        $zipPath = $this->zipService->generate(
            name:         $request->input('name'),
            identifier:   $identifier,
            solid:        (bool) $request->input('solid'),
            destructible: (bool) $request->input('destructible'),
            resistance:   (float) $request->input('resistance'),
            texture:      $request->file('texture'),
            geometry:     $geometry,
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
            geometry:     $block->geometry ?? 'cube',
        );

        return response()->download($zipPath, $block->identifier . '_pack.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Détecte automatiquement la forme du bloc depuis la texture :
     * - Ratio 4:3 exact (ex: 64×48, 32×24) → réseau de faces "net" (cube déplié)
     * - Pixels transparents hors format net → croix / plante
     * - Entièrement opaque → cube plein
     */
    private function detectGeometry(string $imagePath): string
    {
        $img = @imagecreatefrompng($imagePath);
        if (!$img) {
            return 'cube';
        }

        $w = imagesx($img);
        $h = imagesy($img);

        // Net texture: exact 4:3 ratio (e.g. 64×48)
        if ($h > 0 && $w % 4 === 0 && $h % 3 === 0 && ($w / 4) === ($h / 3)) {
            imagedestroy($img);
            return 'net';
        }

        // Net cross pattern on any canvas (e.g. square 64×64 with transparent corners)
        $C = intval($w / 4);
        if ($C > 0 && $this->isNetPattern($img, $w, $h, $C)) {
            imagedestroy($img);
            return 'net';
        }

        // Scan pixels: count fully transparent and partially transparent (blend)
        $transparent  = 0; // alpha > 10 (nearly transparent)
        $partialAlpha = 0; // 5 < alpha < 122 (continuous/partial transparency → blend)
        $total = $w * $h;
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $alpha = (imagecolorat($img, $x, $y) >> 24) & 0x7F; // GD: 0=opaque, 127=transparent
                if ($alpha > 10) {
                    $transparent++;
                }
                if ($alpha > 5 && $alpha < 122) {
                    $partialAlpha++;
                }
            }
        }

        imagedestroy($img);

        // Continuous (partial) transparency > 5% → glass-like block, use blend render_method
        if (($partialAlpha / $total) > 0.05) {
            return 'glass';
        }

        // Binary transparency > 20% → cross/plant shape, use alpha_test render_method
        return ($transparent / $total) > 0.20 ? 'cross' : 'cube';
    }

    private function isNetPattern($img, int $w, int $h, int $C): bool
    {
        $emptyAt = function (int $col, int $row) use ($img, $w, $h, $C): bool {
            $sx = intval(($col + 0.5) * $C);
            $sy = intval(($row + 0.5) * $C);
            if ($sx >= $w || $sy >= $h) return true;
            return (imagecolorat($img, $sx, $sy) >> 24 & 0x7F) > 32;
        };
        $opaqueAt = function (int $col, int $row) use ($img, $w, $h, $C): bool {
            $sx = intval(($col + 0.5) * $C);
            $sy = intval(($row + 0.5) * $C);
            if ($sx >= $w || $sy >= $h) return false;
            return (imagecolorat($img, $sx, $sy) >> 24 & 0x7F) <= 32;
        };

        // 6 corner cells must be transparent
        if (!$emptyAt(0, 0) || !$emptyAt(2, 0) || !$emptyAt(3, 0)) return false;
        if (!$emptyAt(0, 2) || !$emptyAt(2, 2) || !$emptyAt(3, 2)) return false;
        // 6 face cells must be opaque
        if (!$opaqueAt(1, 0)) return false;
        if (!$opaqueAt(0, 1) || !$opaqueAt(1, 1) || !$opaqueAt(2, 1) || !$opaqueAt(3, 1)) return false;
        if (!$opaqueAt(1, 2)) return false;

        return true;
    }

    /**
     * Supprime un bloc de l'historique.
     */
    public function destroy(Block $block)
    {
        Storage::delete($block->texture_path);
        $block->delete();

        return redirect()->route('block.index')->with('success', 'Bloc supprimé.');
    }
}
