<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use ZipArchive;

class BlockZipService
{
    public function __construct(private BlockJsonService $jsonService) {}

    /**
     * Génère le ZIP depuis un upload (première création).
     */
    public function generate(
        string       $name,
        string       $identifier,
        bool         $solid,
        bool         $destructible,
        float        $resistance,
        UploadedFile $texture
    ): string {
        return $this->buildZip($name, $identifier, $solid, $destructible, $resistance, $texture->getRealPath());
    }

    /**
     * Régénère le ZIP depuis une texture déjà stockée (re-téléchargement historique).
     */
    public function generateFromPath(
        string $name,
        string $identifier,
        bool   $solid,
        bool   $destructible,
        float  $resistance,
        string $texturePath
    ): string {
        return $this->buildZip($name, $identifier, $solid, $destructible, $resistance, $texturePath);
    }

    private function buildZip(
        string $name,
        string $identifier,
        bool   $solid,
        bool   $destructible,
        float  $resistance,
        string $texturePath
    ): string {
        $zipPath = tempnam(sys_get_temp_dir(), 'mc_block_') . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Impossible de créer l\'archive ZIP.');
        }

        $root = 'generated_pack/';

        // --- Behavior Pack ---
        $zip->addEmptyDir($root . 'behavior_pack/');
        $zip->addEmptyDir($root . 'behavior_pack/blocks/');

        $zip->addFromString(
            $root . 'behavior_pack/manifest.json',
            $this->jsonService->encode($this->jsonService->behaviorManifest($name))
        );

        $zip->addFromString(
            $root . 'behavior_pack/blocks/' . $identifier . '.json',
            $this->jsonService->encode(
                $this->jsonService->blockBehavior($identifier, $solid, $destructible, $resistance)
            )
        );

        // --- Resource Pack ---
        $zip->addEmptyDir($root . 'resource_pack/');
        $zip->addEmptyDir($root . 'resource_pack/textures/');
        $zip->addEmptyDir($root . 'resource_pack/textures/blocks/');

        $zip->addFromString(
            $root . 'resource_pack/manifest.json',
            $this->jsonService->encode($this->jsonService->resourceManifest($name))
        );

        $zip->addFromString(
            $root . 'resource_pack/terrain_texture.json',
            $this->jsonService->encode($this->jsonService->terrainTexture($identifier))
        );

        $zip->addFile($texturePath, $root . 'resource_pack/textures/blocks/' . $identifier . '.png');

        $zip->close();

        return $zipPath;
    }
}
