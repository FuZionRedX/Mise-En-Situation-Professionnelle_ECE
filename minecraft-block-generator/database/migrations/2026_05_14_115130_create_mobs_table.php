<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identifier')->unique();
            $table->integer('health')->default(20);
            $table->float('speed')->default(0.25);
            $table->enum('behavior_type', ['passive', 'neutral', 'hostile'])->default('passive');
            $table->integer('attack_damage')->nullable();
            $table->boolean('is_spawnable')->default(true);
            $table->boolean('is_summonable')->default(true);
            $table->float('collision_width')->default(0.6);
            $table->float('collision_height')->default(1.8);
            $table->float('scale')->default(1.0);
            $table->enum('model_type', ['humanoid', 'quadruped', 'creeper'])->default('humanoid');
            $table->string('texture_path');
            $table->string('spawn_egg_primary')->default('#a06040');
            $table->string('spawn_egg_secondary')->default('#ffffff');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobs');
    }
};
