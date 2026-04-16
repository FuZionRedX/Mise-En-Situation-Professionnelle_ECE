<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use ZipArchive;

class BlockZipService
{
    public function __construct(private BlockJsonService $jsonService) {}

    public function generate(
        string       $name,
        string       $identifier,
        bool         $solid,
        bool         $destructible,
        float        $resistance,
        UploadedFile $texture,
        string       $geometry = 'cube'
    ): string {
        return $this->buildZip($name, $identifier, $solid, $destructible, $resistance, $texture->getRealPath(), $geometry);
    }

    public function generateFromPath(
        string $name,
        string $identifier,
        bool   $solid,
        bool   $destructible,
        float  $resistance,
        string $texturePath,
        string $geometry = 'cube'
    ): string {
        return $this->buildZip($name, $identifier, $solid, $destructible, $resistance, $texturePath, $geometry);
    }

    private function buildZip(
        string $name,
        string $identifier,
        bool   $solid,
        bool   $destructible,
        float  $resistance,
        string $texturePath,
        string $geometry = 'cube'
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
                $this->jsonService->blockBehavior($identifier, $solid, $destructible, $resistance, $geometry)
            )
        );

        // --- Resource Pack ---
        $zip->addEmptyDir($root . 'resource_pack/');
        $zip->addEmptyDir($root . 'resource_pack/textures/');
        $zip->addEmptyDir($root . 'resource_pack/textures/blocks/');
        $zip->addEmptyDir($root . 'resource_pack/texts/');

        $zip->addFromString(
            $root . 'resource_pack/manifest.json',
            $this->jsonService->encode($this->jsonService->resourceManifest($name))
        );

        $zip->addFromString(
            $root . 'resource_pack/terrain_texture.json',
            $this->jsonService->encode($this->jsonService->terrainTexture($identifier, $geometry))
        );

        $zip->addFromString(
            $root . 'resource_pack/blocks.json',
            $this->jsonService->encode($this->jsonService->blocksJson($identifier))
        );

        $zip->addFromString(
            $root . 'resource_pack/texts/languages.json',
            $this->jsonService->encode($this->jsonService->languagesJson())
        );

        $zip->addFromString(
            $root . 'resource_pack/texts/en_US.lang',
            $this->jsonService->textsLang($identifier, $name)
        );

        // Textures: split net into 6 faces, or add single texture
        if ($geometry === 'net') {
            $facePaths = $this->splitNetTexture($texturePath, $identifier);
            foreach ($facePaths as $face => $facePath) {
                $zip->addFile($facePath, $root . "resource_pack/textures/blocks/{$identifier}_{$face}.png");
            }
            // Clean up temp face files after ZIP is closed
            register_shutdown_function(static function () use ($facePaths) {
                foreach ($facePaths as $path) {
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            });
        } else {
            $zip->addFile($texturePath, $root . 'resource_pack/textures/blocks/' . $identifier . '.png');
        }

        // Geometry file for cross/plant shapes
        if ($geometry !== 'cube' && $geometry !== 'net') {
            $zip->addEmptyDir($root . 'resource_pack/models/');
            $zip->addEmptyDir($root . 'resource_pack/models/blocks/');
            $zip->addFromString(
                $root . 'resource_pack/models/blocks/' . $identifier . '.geo.json',
                $this->jsonService->encode($this->jsonService->geometryJson($identifier, $geometry))
            );
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * Découpe une texture en réseau 4:3 (croix dépliée) en 6 faces PNG temporaires.
     *
     * Layout standard (width=4C, height=3C):
     *        [top]
     *  [left][front][right][back]
     *        [bottom]
     */
    private function splitNetTexture(string $texturePath, string $identifier): array
    {
        $img = imagecreatefrompng($texturePath);
        $w   = imagesx($img);
        $C   = intval($w / 4);

        $regions = [
            'top'    => [$C,       0],
            'left'   => [0,        $C],
            'front'  => [$C,       $C],
            'right'  => [2 * $C,   $C],
            'back'   => [3 * $C,   $C],
            'bottom' => [$C,       2 * $C],
        ];

        $paths = [];
        foreach ($regions as $face => [$sx, $sy]) {
            $faceImg = imagecreatetruecolor($C, $C);
            imagealphablending($faceImg, false);
            imagesavealpha($faceImg, true);
            imagecopy($faceImg, $img, 0, 0, $sx, $sy, $C, $C);

            $facePath = tempnam(sys_get_temp_dir(), "mc_{$identifier}_{$face}_") . '.png';
            imagepng($faceImg, $facePath);
            imagedestroy($faceImg);
            $paths[$face] = $facePath;
        }

        imagedestroy($img);
        return $paths;
    }
}
