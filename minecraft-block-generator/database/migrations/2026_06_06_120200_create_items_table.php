<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('identifier')->unique();
                $table->string('texture_path');
                $table->string('creator_identifier')->nullable();
                $table->integer('max_stack_size')->default(64)->nullable();
                $table->integer('max_durability')->nullable();
                $table->integer('item_tier')->nullable();
                $table->float('item_multiplier')->nullable();
                $table->integer('damage')->default(0)->nullable();
                $table->boolean('hand_equipped')->default(false)->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('items', function (Blueprint $table) {
                if (!Schema::hasColumn('items', 'max_stack_size')) {
                    $table->integer('max_stack_size')->default(64)->nullable();
                }
                if (!Schema::hasColumn('items', 'max_durability')) {
                    $table->integer('max_durability')->nullable();
                }
                if (!Schema::hasColumn('items', 'item_tier')) {
                    $table->integer('item_tier')->nullable();
                }
                if (!Schema::hasColumn('items', 'item_multiplier')) {
                    $table->float('item_multiplier')->nullable();
                }
                if (!Schema::hasColumn('items', 'damage')) {
                    $table->integer('damage')->default(0)->nullable();
                }
                if (!Schema::hasColumn('items', 'hand_equipped')) {
                    $table->boolean('hand_equipped')->default(false)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
