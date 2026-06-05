<?php

namespace App\Http\Controllers;

use App\Http\Requests\MobRequest;
use App\Models\Mob;
use App\Services\MobZipService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MobController extends Controller
{
    public function __construct(private MobZipService $zipService) {}

    public function create(MobRequest $request): BinaryFileResponse
    {
        $identifier = $request->input('identifier');

        $texturePath = $request->file('texture')->storeAs(
            'mob_textures',
            $identifier . '_' . time() . '.png'
        );

        $geometryJsonPath = $this->storeMobGeometryJson($request, $identifier);

        Mob::create([
            'name'                => $request->input('name'),
            'identifier'          => $identifier,
            'creator_identifier'  => Auth::check() ? Auth::user()->identifier : null,
            'health'              => (int)   $request->input('health'),
            'speed'               => (float) $request->input('speed'),
            'behavior_type'       => $request->input('behavior_type'),
            'attack_damage'       => $request->input('attack_damage') ? (int) $request->input('attack_damage') : null,
            'is_spawnable'        => (bool)  $request->input('is_spawnable'),
            'is_summonable'       => (bool)  $request->input('is_summonable'),
            'collision_width'     => (float) $request->input('collision_width'),
            'collision_height'    => (float) $request->input('collision_height'),
            'scale'               => (float) $request->input('scale'),
            'model_type'          => $request->input('model_type'),
            'texture_path'        => $texturePath,
            'geometry_json_path'  => $geometryJsonPath,
            'spawn_egg_primary'   => $request->input('spawn_egg_primary'),
            'spawn_egg_secondary' => $request->input('spawn_egg_secondary'),
        ]);

        $customGeometryJson = $geometryJsonPath ? Storage::get($geometryJsonPath) : null;

        $zipPath = $this->zipService->generate(
            name:               $request->input('name'),
            identifier:         $identifier,
            health:             (int)   $request->input('health'),
            speed:              (float) $request->input('speed'),
            behaviorType:       $request->input('behavior_type'),
            attackDamage:       $request->input('attack_damage') ? (int) $request->input('attack_damage') : null,
            isSpawnable:        (bool)  $request->input('is_spawnable'),
            isSummonable:       (bool)  $request->input('is_summonable'),
            collisionWidth:     (float) $request->input('collision_width'),
            collisionHeight:    (float) $request->input('collision_height'),
            modelType:          $request->input('model_type'),
            texture:            $request->file('texture'),
            spawnEggPrimary:    $request->input('spawn_egg_primary'),
            spawnEggSecondary:  $request->input('spawn_egg_secondary'),
            customGeometryJson: $customGeometryJson,
        );

        return response()->download($zipPath, $identifier . '_mob_pack.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    public function edit(Mob $mob)
    {
        return view('block.create', compact('mob'));
    }

    public function update(MobRequest $request, Mob $mob): BinaryFileResponse
    {
        $identifier = $mob->identifier;

        if ($request->hasFile('texture')) {
            Storage::delete($mob->texture_path);
            $texturePath = $request->file('texture')->storeAs(
                'mob_textures',
                $identifier . '_' . time() . '.png'
            );
        } else {
            $texturePath = $mob->texture_path;
        }

        $newGeometryJsonPath = $this->storeMobGeometryJson($request, $identifier);
        if ($newGeometryJsonPath && $mob->geometry_json_path) {
            Storage::delete($mob->geometry_json_path);
        }
        $geometryJsonPath = $newGeometryJsonPath ?? $mob->geometry_json_path;

        $mob->update([
            'name'                => $request->input('name'),
            'health'              => (int)   $request->input('health'),
            'speed'               => (float) $request->input('speed'),
            'behavior_type'       => $request->input('behavior_type'),
            'attack_damage'       => $request->input('attack_damage') ? (int) $request->input('attack_damage') : null,
            'is_spawnable'        => (bool)  $request->input('is_spawnable'),
            'is_summonable'       => (bool)  $request->input('is_summonable'),
            'collision_width'     => (float) $request->input('collision_width'),
            'collision_height'    => (float) $request->input('collision_height'),
            'scale'               => (float) $request->input('scale'),
            'model_type'          => $request->input('model_type'),
            'texture_path'        => $texturePath,
            'geometry_json_path'  => $geometryJsonPath,
            'spawn_egg_primary'   => $request->input('spawn_egg_primary'),
            'spawn_egg_secondary' => $request->input('spawn_egg_secondary'),
        ]);

        $customGeometryJson = $geometryJsonPath ? Storage::get($geometryJsonPath) : null;

        if ($request->hasFile('texture')) {
            $zipPath = $this->zipService->generate(
                name:               $request->input('name'),
                identifier:         $identifier,
                health:             (int)   $request->input('health'),
                speed:              (float) $request->input('speed'),
                behaviorType:       $request->input('behavior_type'),
                attackDamage:       $request->input('attack_damage') ? (int) $request->input('attack_damage') : null,
                isSpawnable:        (bool)  $request->input('is_spawnable'),
                isSummonable:       (bool)  $request->input('is_summonable'),
                collisionWidth:     (float) $request->input('collision_width'),
                collisionHeight:    (float) $request->input('collision_height'),
                modelType:          $request->input('model_type'),
                texture:            $request->file('texture'),
                spawnEggPrimary:    $request->input('spawn_egg_primary'),
                spawnEggSecondary:  $request->input('spawn_egg_secondary'),
                customGeometryJson: $customGeometryJson,
            );
        } else {
            $zipPath = $this->zipService->generateFromPath(
                name:               $request->input('name'),
                identifier:         $identifier,
                health:             (int)   $request->input('health'),
                speed:              (float) $request->input('speed'),
                behaviorType:       $request->input('behavior_type'),
                attackDamage:       $request->input('attack_damage') ? (int) $request->input('attack_damage') : null,
                isSpawnable:        (bool)  $request->input('is_spawnable'),
                isSummonable:       (bool)  $request->input('is_summonable'),
                collisionWidth:     (float) $request->input('collision_width'),
                collisionHeight:    (float) $request->input('collision_height'),
                modelType:          $request->input('model_type'),
                texturePath:        Storage::path($texturePath),
                spawnEggPrimary:    $request->input('spawn_egg_primary'),
                spawnEggSecondary:  $request->input('spawn_egg_secondary'),
                customGeometryJson: $customGeometryJson,
            );
        }

        return response()->download($zipPath, $identifier . '_mob_pack.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    public function download(Mob $mob): BinaryFileResponse
    {
        $texturePath = Storage::path($mob->texture_path);

        if (!file_exists($texturePath)) {
            abort(404, 'Texture introuvable pour ce mob.');
        }

        $customGeometryJson = $mob->geometry_json_path ? Storage::get($mob->geometry_json_path) : null;

        $zipPath = $this->zipService->generateFromPath(
            name:               $mob->name,
            identifier:         $mob->identifier,
            health:             $mob->health,
            speed:              $mob->speed,
            behaviorType:       $mob->behavior_type,
            attackDamage:       $mob->attack_damage,
            isSpawnable:        $mob->is_spawnable,
            isSummonable:       $mob->is_summonable,
            collisionWidth:     $mob->collision_width,
            collisionHeight:    $mob->collision_height,
            modelType:          $mob->model_type,
            texturePath:        $texturePath,
            spawnEggPrimary:    $mob->spawn_egg_primary,
            spawnEggSecondary:  $mob->spawn_egg_secondary,
            customGeometryJson: $customGeometryJson,
        );

        return response()->download($zipPath, $mob->identifier . '_mob_pack.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    public function destroy(Mob $mob)
    {
        Storage::delete($mob->texture_path);
        if ($mob->geometry_json_path) {
            Storage::delete($mob->geometry_json_path);
        }
        $mob->delete();

        return redirect()->route('block.index')->with('success', 'Mob supprimé.');
    }

    private function storeMobGeometryJson(\Illuminate\Http\Request $request, string $identifier): ?string
    {
        if (!$request->hasFile('geometry_file')) {
            return null;
        }

        $json = file_get_contents($request->file('geometry_file')->getRealPath());
        $filename = $identifier . '_' . time() . '.json';
        Storage::put('mob_geometry/' . $filename, $json);

        return 'mob_geometry/' . $filename;
    }
}
