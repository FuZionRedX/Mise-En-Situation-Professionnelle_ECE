# 🎁 Feature: Item Generator — Documentation

## Overview
New generic item creation page allowing users to generate Minecraft Bedrock Edition items with custom textures. Unlike blocks which have geometry and properties (solid, destructible, resistance), items are simpler and focus on texture-based creation for any item type (torches, weapons, panels, tools, etc.).

## Architecture

### Database
- **Table**: `items` (auto-created or already exists)
- **Columns**: 
  - `id` (bigint PK)
  - `name` (string) — display name
  - `identifier` (string) — technical ID (`[a-z0-9_]+`)
  - `texture_path` (string) — path to stored PNG
  - `creator_identifier` (string|null) — user who created it
  - `created_at`, `updated_at` (timestamps)

### Models
- `App\Models\Item` — simple model with fillable properties

### Services

#### `ItemJsonService`
Generates JSON files for behavior + resource packs:
- `behaviorManifest()` — behavior pack manifest.json
- `resourceManifest()` — resource pack manifest.json
- `itemBehavior(identifier)` — item behavior JSON (format_version 1.16.100)
- `itemTexture(identifier)` — item texture mapping JSON
- `itemsJson(identifier)` — items.json registry
- `languagesJson()` — language list
- `textsLang(identifier, name)` — en_US.lang file
- `encode(array)` — pretty-print JSON with Unicode support

#### `ItemZipService`
Assembles ZIP file structure:
```
custom_items_pack/
├── behavior_pack/
│   ├── manifest.json
│   └── items/
│       └── {identifier}.json
└── resource_pack/
    ├── manifest.json
    ├── textures/
    │   └── items/
    │       └── {identifier}.png
    ├── items.json
    └── texts/
        ├── languages.json
        └── en_US.lang
```

### Controllers
`ItemController` with actions:
- `index()` → displays creation form
- `create(ItemRequest)` → generates ZIP + saves to DB
- `edit(Item)` → edit form
- `update(ItemRequest, Item)` → updates + regenerates ZIP
- `download(Item)` → re-downloads ZIP from stored texture
- `history(Request)` → paginated list (12/page), filterable by creator
- `destroy(Item)` → deletes texture + DB record

### Validation
`ItemRequest` rules:
- `name`: required, string, 1–50 chars, alphanumeric + spaces only
- `identifier`: required, unique, lowercase + underscores only
- `texture`: PNG only, max 512KB, required for create (optional for update)

### Routes

| Method | URI | Name | Auth | Action |
|--------|-----|------|------|--------|
| GET | `/items` | `item.index` | Public | History + list (paginated) |
| GET | `/item/new` | `item.new` | Auth required | Show creation form |
| POST | `/item/create` | `item.create` | Auth required | Create item + download |
| GET | `/item/{item}/edit` | `item.edit` | Admin | Show edit form |
| POST | `/item/{item}/update` | `item.update` | Admin | Update item + download |
| GET | `/item/{item}/download` | `item.download` | Public | Re-download ZIP |
| DELETE | `/item/{item}` | `item.destroy` | Owner/Admin | Delete item |
| GET | `/item/{id}/texture` | `item.texture` | Public | Serve PNG inline |

### Views

#### `resources/views/item/create.blade.php`
- Standalone HTML document (no `@extends` layout)
- Form with fields: name, identifier, texture upload
- Texture preview (pixelated Minecraft style, 96×96px)
- Drag-and-drop texture input
- Responsive layout (mobile + desktop)
- Styled with Tailwind CSS (via CDN)

#### `resources/views/item/history.blade.php`
- Public browseable list of all items (paginated, 12/page)
- Filter by creator (if authenticated)
- Card layout showing: texture preview, name, identifier, date, creator
- Actions: Download, Edit (if owner/admin), Delete (if owner/admin)
- Links to create new item

## Generated Minecraft Pack Structure

For item `custom:my_item`:

### Behavior Pack (`behavior_pack/items/my_item.json`)
```json
{
  "format_version": "1.16.100",
  "minecraft:item": {
    "description": {
      "identifier": "custom:my_item",
      "category": "equipment"
    },
    "components": {
      "minecraft:max_stack_size": 64,
      "minecraft:display_name": {
        "value": "§rmy_item"
      }
    }
  }
}
```

### Resource Pack
- `textures/items/{identifier}.png` — the uploaded texture
- `textures/item_texture.json` — maps texture to identifier
- `items.json` — registers item in resource pack
- `texts/en_US.lang` — language strings

## Key Features
✅ Generic item creation (works for any item type)  
✅ Texture preview before download  
✅ Persistent texture storage for re-download  
✅ Paginated history  
✅ User authentication + ownership tracking  
✅ Admin editing capabilities  
✅ Simple, clean UI matching existing block generator style  

## What's NOT Supported (by design)
❌ Per-type properties (lumosity for torches, damage for weapons, etc.)  
❌ Custom geometry (3D models)  
❌ Multiple textures  
❌ Enchantability, durability limits per-item  

These can be added as extensions if needed.

## Testing Checklist
- [ ] Create item with valid texture
- [ ] Verify ZIP structure and JSON content
- [ ] Download ZIP and test in Minecraft Bedrock
- [ ] Edit existing item
- [ ] Delete item
- [ ] Verify pagination on history page
- [ ] Filter by "My creations" (authenticated)
- [ ] Test texture drag-and-drop
- [ ] Test mobile responsive layout
- [ ] Verify texture preview renders correctly

## Future Extensions
- [ ] Custom item categories (equipment, food, weapon, tool)
- [ ] Per-category default max_stack_size
- [ ] Enchantability toggle
- [ ] Durability configuration
- [ ] Custom geometry upload (.geo.json)
- [ ] Multiple items per pack (bulk creation)
- [ ] Item loot table integration
