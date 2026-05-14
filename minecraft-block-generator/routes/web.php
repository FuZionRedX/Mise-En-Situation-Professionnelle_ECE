<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\MobController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', [BlockController::class, 'history'])->name('block.index');
Route::get('/block/new', [BlockController::class, 'index'])->name('block.new');
Route::post('/block/create', [BlockController::class, 'create'])->name('block.create');
Route::get('/block/{block}/edit', [BlockController::class, 'edit'])->name('block.edit');
Route::post('/block/{block}/update', [BlockController::class, 'update'])->name('block.update');
Route::post('/blocks/download-selected', [BlockController::class, 'downloadSelected'])->name('block.download-selected');
Route::get('/blocks/textures', [BlockController::class, 'downloadAllTextures'])->name('block.download-textures');
Route::get('/block/{block}/download', [BlockController::class, 'download'])->name('block.download');
Route::delete('/block/{block}', [BlockController::class, 'destroy'])->name('block.destroy');

// Mob routes
Route::post('/mob/create', [MobController::class, 'create'])->name('mob.create');
Route::get('/mob/{mob}/edit', [MobController::class, 'edit'])->name('mob.edit');
Route::post('/mob/{mob}/update', [MobController::class, 'update'])->name('mob.update');
Route::get('/mob/{mob}/download', [MobController::class, 'download'])->name('mob.download');
Route::delete('/mob/{mob}', [MobController::class, 'destroy'])->name('mob.destroy');

// Sert la texture stockée pour l'affichage dans l'historique
Route::get('/block/{id}/texture', function (int $id) {
    $block = \App\Models\Block::findOrFail($id);
    if (!Storage::exists($block->texture_path)) {
        abort(404);
    }
    return response()->file(Storage::path($block->texture_path), ['Content-Type' => 'image/png']);
})->name('block.texture');

Route::get('/mob/{id}/texture', function (int $id) {
    $mob = \App\Models\Mob::findOrFail($id);
    if (!Storage::exists($mob->texture_path)) {
        abort(404);
    }
    return response()->file(Storage::path($mob->texture_path), ['Content-Type' => 'image/png']);
})->name('mob.texture');
