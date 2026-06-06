<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use ZipArchive;

class ItemZipService
{
    public function __construct(private ItemJsonService $jsonService) {}

    public function generate(
        string       $name,
        string       $identifier,
        UploadedFile $texture
    ): string {
        return $this->buildZip($name, $identifier, $texture->getRealPath());
    }

    public function generateFromPath(
        string $name,
        string $identifier,
        string $texturePath
    ): string {
        return $this->buildZip($name, $identifier, $texturePath);
    }

    private function buildZip(
        string $name,
        string $identifier,
        string $texturePath
    ): string {
        $zipPath = tempnam(sys_get_temp_dir(), 'mc_items_') . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Impossible de créer l\'archive ZIP.');
        }

        $root = 'custom_items_pack/';

        // Behavior pack structure
        $zip->addEmptyDir($root . 'behavior_pack/');
        $zip->addEmptyDir($root . 'behavior_pack/items/');
        $zip->addFromString(
            $root . 'behavior_pack/manifest.json',
            $this->jsonService->encode($this->jsonService->behaviorManifest($name))
        );

        // Item behavior JSON
        $zip->addFromString(
            $root . 'behavior_pack/items/' . $identifier . '.json',
            $this->jsonService->encode($this->jsonService->itemBehavior($identifier))
        );

        // Resource pack structure
        $zip->addEmptyDir($root . 'resource_pack/');
        $zip->addEmptyDir($root . 'resource_pack/textures/');
        $zip->addEmptyDir($root . 'resource_pack/textures/items/');
        $zip->addEmptyDir($root . 'resource_pack/texts/');
        $zip->addFromString(
            $root . 'resource_pack/manifest.json',
            $this->jsonService->encode($this->jsonService->resourceManifest($name))
        );

        // Item texture file
        $zip->addFile($texturePath, $root . 'resource_pack/textures/items/' . $identifier . '.png');

        // Item texture JSON
        $zip->addFromString(
            $root . 'resource_pack/textures/item_texture.json',
            $this->jsonService->encode($this->jsonService->itemTexture($identifier))
        );

        // Items JSON
        $zip->addFromString(
            $root . 'resource_pack/items.json',
            $this->jsonService->encode($this->jsonService->itemsJson($identifier))
        );

        // Language files
        $zip->addFromString(
            $root . 'resource_pack/texts/languages.json',
            $this->jsonService->encode($this->jsonService->languagesJson())
        );

        $zip->addFromString(
            $root . 'resource_pack/texts/en_US.lang',
            $this->jsonService->textsLang($identifier, $name)
        );

        $zip->close();

        return $zipPath;
    }
}
