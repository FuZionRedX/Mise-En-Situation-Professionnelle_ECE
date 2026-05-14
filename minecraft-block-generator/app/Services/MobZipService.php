<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use ZipArchive;

class MobZipService
{
    public function __construct(private MobJsonService $jsonService) {}

    public function generate(
        string       $name,
        string       $identifier,
        int          $health,
        float        $speed,
        string       $behaviorType,
        ?int         $attackDamage,
        bool         $isSpawnable,
        bool         $isSummonable,
        float        $collisionWidth,
        float        $collisionHeight,
        string       $modelType,
        UploadedFile $texture,
        string       $spawnEggPrimary,
        string       $spawnEggSecondary,
        ?string      $customGeometryJson = null
    ): string {
        return $this->buildZip(
            name: $name,
            identifier: $identifier,
            health: $health,
            speed: $speed,
            behaviorType: $behaviorType,
            attackDamage: $attackDamage,
            isSpawnable: $isSpawnable,
            isSummonable: $isSummonable,
            collisionWidth: $collisionWidth,
            collisionHeight: $collisionHeight,
            modelType: $modelType,
            texturePath: $texture->getRealPath(),
            spawnEggPrimary: $spawnEggPrimary,
            spawnEggSecondary: $spawnEggSecondary,
            customGeometryJson: $customGeometryJson
        );
    }

    public function generateFromPath(
        string  $name,
        string  $identifier,
        int     $health,
        float   $speed,
        string  $behaviorType,
        ?int    $attackDamage,
        bool    $isSpawnable,
        bool    $isSummonable,
        float   $collisionWidth,
        float   $collisionHeight,
        string  $modelType,
        string  $texturePath,
        string  $spawnEggPrimary,
        string  $spawnEggSecondary,
        ?string $customGeometryJson = null
    ): string {
        return $this->buildZip(
            name: $name,
            identifier: $identifier,
            health: $health,
            speed: $speed,
            behaviorType: $behaviorType,
            attackDamage: $attackDamage,
            isSpawnable: $isSpawnable,
            isSummonable: $isSummonable,
            collisionWidth: $collisionWidth,
            collisionHeight: $collisionHeight,
            modelType: $modelType,
            texturePath: $texturePath,
            spawnEggPrimary: $spawnEggPrimary,
            spawnEggSecondary: $spawnEggSecondary,
            customGeometryJson: $customGeometryJson
        );
    }

    private function buildZip(
        string  $name,
        string  $identifier,
        int     $health,
        float   $speed,
        string  $behaviorType,
        ?int    $attackDamage,
        bool    $isSpawnable,
        bool    $isSummonable,
        float   $collisionWidth,
        float   $collisionHeight,
        string  $modelType,
        string  $texturePath,
        string  $spawnEggPrimary,
        string  $spawnEggSecondary,
        ?string $customGeometryJson = null
    ): string {
        $zipPath = tempnam(sys_get_temp_dir(), 'mc_mob_') . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Impossible de créer l\'archive ZIP.');
        }

        $root = $identifier . '_mob_pack/';

        // --- Behavior Pack ---
        $zip->addEmptyDir($root . 'behavior_pack/');
        $zip->addEmptyDir($root . 'behavior_pack/entities/');
        $zip->addFromString(
            $root . 'behavior_pack/manifest.json',
            $this->jsonService->encode($this->jsonService->behaviorManifest($name))
        );
        $zip->addFromString(
            $root . 'behavior_pack/entities/' . $identifier . '.json',
            $this->jsonService->encode(
                $this->jsonService->entityBehavior(
                    $identifier, $health, $speed, $behaviorType, $attackDamage,
                    $isSpawnable, $isSummonable, $collisionWidth, $collisionHeight
                )
            )
        );

        // --- Resource Pack ---
        $zip->addEmptyDir($root . 'resource_pack/');
        $zip->addEmptyDir($root . 'resource_pack/entity/');
        $zip->addEmptyDir($root . 'resource_pack/models/');
        $zip->addEmptyDir($root . 'resource_pack/models/entity/');
        $zip->addEmptyDir($root . 'resource_pack/render_controllers/');
        $zip->addEmptyDir($root . 'resource_pack/animation_controllers/');
        $zip->addEmptyDir($root . 'resource_pack/textures/');
        $zip->addEmptyDir($root . 'resource_pack/textures/entity/');
        $zip->addEmptyDir($root . 'resource_pack/texts/');

        $zip->addFromString(
            $root . 'resource_pack/manifest.json',
            $this->jsonService->encode($this->jsonService->resourceManifest($name))
        );
        $zip->addFromString(
            $root . 'resource_pack/entity/' . $identifier . '.entity.json',
            $this->jsonService->encode(
                $this->jsonService->clientEntity($identifier, $modelType, $spawnEggPrimary, $spawnEggSecondary)
            )
        );
        $geoJson = $customGeometryJson !== null
            ? $customGeometryJson
            : $this->jsonService->encode($this->jsonService->geometryJson($identifier, $modelType));
        $zip->addFromString($root . 'resource_pack/models/entity/' . $identifier . '.geo.json', $geoJson);
        $zip->addFromString(
            $root . 'resource_pack/render_controllers/' . $identifier . '.render_controller.json',
            $this->jsonService->encode($this->jsonService->renderController($identifier))
        );
        $zip->addFromString(
            $root . 'resource_pack/animation_controllers/' . $identifier . '.animation_controllers.json',
            $this->jsonService->encode($this->jsonService->animationController($identifier, $modelType))
        );
        $zip->addFile($texturePath, $root . 'resource_pack/textures/entity/' . $identifier . '.png');
        $zip->addFromString(
            $root . 'resource_pack/texts/en_US.lang',
            $this->jsonService->textsLang($identifier, $name)
        );
        $zip->addFromString(
            $root . 'resource_pack/texts/languages.json',
            $this->jsonService->encode(['en_US'])
        );

        $zip->close();

        return $zipPath;
    }
}
