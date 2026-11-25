<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class PackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** Lit chaque fichier du ZIP renvoyé : [chemin => contenu]. */
    private function zipEntries($response): array
    {
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($response->baseResponse->getFile()->getPathname()));
        $entries = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entries[$zip->getNameIndex($i)] = $zip->getFromIndex($i);
        }
        $zip->close();

        return $entries;
    }

    public function test_uploaded_model_goes_to_resource_pack_and_block_is_still_defined(): void
    {
        $model = json_encode(['format_version' => '1.12.0', 'minecraft:geometry' => [
            ['description' => ['identifier' => 'geometry.chest'], 'bones' => []],
        ]]);

        $response = $this->actingAs(User::factory()->create())->post('/block/create', [
            'name' => 'Coffre', 'identifier' => 'coffre', 'solid' => 1, 'destructible' => 1,
            'resistance' => 5, 'light_emission' => 0,
            'texture' => UploadedFile::fake()->image('coffre.png', 16, 16),
            'geometry_data' => $model,
        ]);

        $response->assertOk();
        $entries = $this->zipEntries($response);
        $this->assertSame($model, $entries['generated_pack/resource_pack/models/blocks/coffre.geo.json']);
        $block = json_decode($entries['generated_pack/behavior_pack/blocks/coffre.json'], true);
        $this->assertSame('custom:coffre', $block['minecraft:block']['description']['identifier']);
        $this->assertSame('geometry.chest', $block['minecraft:block']['components']['minecraft:geometry']['identifier']);
    }

    public function test_invalid_geometry_json_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())->post('/block/create', [
            'name' => 'Bad', 'identifier' => 'bad', 'solid' => 1, 'destructible' => 1,
            'resistance' => 5, 'light_emission' => 0,
            'texture' => UploadedFile::fake()->image('bad.png', 16, 16),
            'geometry_data' => '{not json',
        ])->assertSessionHasErrors('geometry_file');

        $this->assertDatabaseCount('blocks', 0);
    }

    public function test_user_cannot_delete_someone_elses_item(): void
    {
        $owner = User::factory()->create();
        $item = Item::create(['name' => 'Axe', 'identifier' => 'axe', 'creator_identifier' => $owner->identifier, 'texture_path' => 'item_textures/axe.png']);

        $this->actingAs(User::factory()->create())->delete("/item/{$item->id}")->assertForbidden();
        $this->assertDatabaseHas('items', ['id' => $item->id]);
    }

    public function test_item_pack_has_icon(): void
    {
        $response = $this->actingAs(User::factory()->create())->post('/item/create', [
            'name' => 'Axe', 'identifier' => 'axe',
            'texture' => UploadedFile::fake()->image('axe.png', 16, 16),
        ]);

        $response->assertOk();
        $item = json_decode($this->zipEntries($response)['custom_items_pack/behavior_pack/items/axe.json'], true);
        $this->assertSame('axe', $item['minecraft:item']['components']['minecraft:icon']['texture']);
    }
}
