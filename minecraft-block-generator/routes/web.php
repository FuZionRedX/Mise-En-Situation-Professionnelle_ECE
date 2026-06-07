<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MobController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Public routes
Route::get('/', [BlockController::class, 'history'])->name('block.index');

Route::get('/block/new', function () {
    if (Auth::check()) {
        return app(BlockController::class)->index();
    }
    return redirect()->route('auth.choice');
})->name('block.new');

Route::get('/items', [ItemController::class, 'history'])->name('item.index');

Route::get('/item/new', function () {
    if (Auth::check()) {
        return app(ItemController::class)->index();
    }
    return redirect()->route('auth.choice');
})->name('item.new');

// Download routes (public — anyone can download)
Route::get('/block/{block}/download', [BlockController::class, 'download'])->name('block.download');
Route::get('/item/{item}/download', [ItemController::class, 'download'])->name('item.download');
Route::get('/mob/{mob}/download', [MobController::class, 'download'])->name('mob.download');
Route::post('/blocks/download-selected', [BlockController::class, 'downloadSelected'])->name('block.download-selected');
Route::get('/blocks/textures', [BlockController::class, 'downloadAllTextures'])->name('block.download-textures');

// Texture serving (public)
Route::get('/block/{id}/texture', function (int $id) {
    $block = \App\Models\Block::findOrFail($id);
    if (!Storage::exists($block->texture_path)) {
        abort(404);
    }
    return response()->file(Storage::path($block->texture_path), ['Content-Type' => 'image/png']);
})->name('block.texture');

Route::get('/item/{id}/texture', function (int $id) {
    $item = \App\Models\Item::findOrFail($id);
    if (!Storage::exists($item->texture_path)) {
        abort(404);
    }
    return response()->file(Storage::path($item->texture_path), ['Content-Type' => 'image/png']);
})->name('item.texture');

Route::get('/mob/{id}/texture', function (int $id) {
    $mob = \App\Models\Mob::findOrFail($id);
    if (!Storage::exists($mob->texture_path)) {
        abort(404);
    }
    return response()->file(Storage::path($mob->texture_path), ['Content-Type' => 'image/png']);
})->name('mob.texture');

// Auth routes
Route::get('/auth/choice', [\App\Http\Controllers\AuthController::class, 'choice'])->name('auth.choice');
Route::get('/auth/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('auth.login.post');
Route::get('/auth/register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/auth/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('auth.register.post');
Route::post('/auth/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('auth.logout');

// Authenticated users: create + delete own
Route::middleware('auth')->group(function () {
    Route::post('/block/create', [BlockController::class, 'create'])->name('block.create');
    Route::post('/item/create', [ItemController::class, 'create'])->name('item.create');
    Route::post('/mob/create', [MobController::class, 'create'])->name('mob.create');
    Route::delete('/block/{block}', [BlockController::class, 'destroy'])->name('block.destroy');
    Route::delete('/item/{item}', [ItemController::class, 'destroy'])->name('item.destroy');
    Route::delete('/mob/{mob}', [MobController::class, 'destroy'])->name('mob.destroy');
});

// Edit/update: owner or admin
Route::middleware(['auth', \App\Http\Middleware\EnsureOwnerOrAdmin::class])->group(function () {
    Route::get('/block/{block}/edit', [BlockController::class, 'edit'])->name('block.edit');
    Route::post('/block/{block}/update', [BlockController::class, 'update'])->name('block.update');
    Route::get('/item/{item}/edit', [ItemController::class, 'edit'])->name('item.edit');
    Route::post('/item/{item}/update', [ItemController::class, 'update'])->name('item.update');
    Route::get('/mob/{mob}/edit', [MobController::class, 'edit'])->name('mob.edit');
    Route::post('/mob/{mob}/update', [MobController::class, 'update'])->name('mob.update');
});

// Admin only: user management
Route::middleware(['auth', \App\Http\Middleware\EnsureAdmin::class])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});
