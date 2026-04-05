<?php

use App\Http\Controllers\BlockController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', [BlockController::class, 'index'])->name('block.index');
Route::post('/block/create', [BlockController::class, 'create'])->name('block.create');

Route::get('/blocks', [BlockController::class, 'history'])->name('block.history');
Route::get('/block/{block}/download', [BlockController::class, 'download'])->name('block.download');
Route::delete('/block/{block}', [BlockController::class, 'destroy'])->name('block.destroy');

// Sert la texture stockée pour l'affichage dans l'historique
Route::get('/block/{id}/texture', function (int $id) {
    $block = \App\Models\Block::findOrFail($id);
    if (!Storage::exists($block->texture_path)) {
        abort(404);
    }
    return response()->file(Storage::path($block->texture_path), ['Content-Type' => 'image/png']);
})->name('block.texture');
