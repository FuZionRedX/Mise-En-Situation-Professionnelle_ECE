<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\MobController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', [BlockController::class, 'history'])->name('block.index');

// Nouveau : si l'utilisateur n'est pas connecté, on redirige vers le choix connexion/inscription
Route::get('/block/new', function () {
    if (auth()->check()) {
        return app(BlockController::class)->index();
    }
    return redirect()->route('auth.choice');
})->name('block.new');

// Auth light routes
Route::get('/auth/choice', [\App\Http\Controllers\AuthController::class, 'choice'])->name('auth.choice');
Route::get('/auth/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('auth.login.post');
Route::get('/auth/register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/auth/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('auth.register.post');
Route::post('/auth/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('auth.logout');
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
