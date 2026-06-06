<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Services\ItemZipService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ItemController extends Controller
{
    public function __construct(private ItemZipService $zipService) {}

    public function index()
    {
        return view('item.create');
    }

    public function edit(Item $item)
    {
        return view('item.create', compact('item'));
    }

    public function create(ItemRequest $request): BinaryFileResponse
    {
        $identifier = $request->input('identifier');

        $texturePath = $request->file('texture')->storeAs(
            'item_textures',
            $identifier . '_' . time() . '.png'
        );

        $creatorIdentifier = Auth::check() ? Auth::user()->identifier : null;
        Item::create([
            'name'                => $request->input('name'),
            'identifier'          => $identifier,
            'creator_identifier'  => $creatorIdentifier,
            'texture_path'        => $texturePath,
        ]);

        if ($creatorIdentifier) {
            session()->flash('success', 'Item créé avec succès — Créateur : ' . $creatorIdentifier);
        } else {
            session()->flash('success', 'Item créé avec succès');
        }

        $zipPath = $this->zipService->generate(
            name:       $request->input('name'),
            identifier: $identifier,
            texture:    $request->file('texture'),
        );

        return response()->download($zipPath, $identifier . '_item_pack.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    public function update(ItemRequest $request, Item $item): BinaryFileResponse
    {
        $identifier = $item->identifier;

        if ($request->hasFile('texture')) {
            Storage::delete($item->texture_path);
            $texturePath = $request->file('texture')->storeAs(
                'item_textures',
                $identifier . '_' . time() . '.png'
            );
        } else {
            $texturePath = $item->texture_path;
        }

        $item->update([
            'name'          => $request->input('name'),
            'texture_path'  => $texturePath,
        ]);

        $zipPath = $this->zipService->generateFromPath(
            name:       $item->name,
            identifier: $identifier,
            texturePath: Storage::path($texturePath),
        );

        return response()->download($zipPath, $identifier . '_item_pack.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    public function history(Request $request)
    {
        $mine      = $request->query('filter') === 'mine' && Auth::check();
        $creatorId = $mine ? Auth::user()->identifier : null;

        $items = Item::latest()
            ->when($creatorId, fn ($q) => $q->where('creator_identifier', $creatorId))
            ->paginate(12);

        return view('item.history', compact('items', 'mine'));
    }

    public function download(Item $item): BinaryFileResponse
    {
        $texturePath = Storage::path($item->texture_path);

        if (!file_exists($texturePath)) {
            abort(404, 'Texture introuvable pour cet item.');
        }

        $zipPath = $this->zipService->generateFromPath(
            name:       $item->name,
            identifier: $item->identifier,
            texturePath: $texturePath,
        );

        return response()->download($zipPath, $item->identifier . '_item_pack.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    public function destroy(Item $item)
    {
        Storage::delete($item->texture_path);
        $item->delete();

        return back()->with('success', 'Item supprimé avec succès.');
    }
}
