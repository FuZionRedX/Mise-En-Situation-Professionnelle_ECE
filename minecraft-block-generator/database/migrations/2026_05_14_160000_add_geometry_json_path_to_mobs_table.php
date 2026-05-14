<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobs', function (Blueprint $table) {
            $table->string('geometry_json_path')->nullable()->after('texture_path');
        });
    }

    public function down(): void
    {
        Schema::table('mobs', function (Blueprint $table) {
            $table->dropColumn('geometry_json_path');
        });
    }
};
