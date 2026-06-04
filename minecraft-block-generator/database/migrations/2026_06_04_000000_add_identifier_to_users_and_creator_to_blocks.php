<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'identifier')) {
                $table->string('identifier')->unique()->after('id');
            }
        });

        Schema::table('blocks', function (Blueprint $table) {
            if (!Schema::hasColumn('blocks', 'creator_identifier')) {
                $table->string('creator_identifier')->nullable()->after('identifier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            if (Schema::hasColumn('blocks', 'creator_identifier')) {
                $table->dropColumn('creator_identifier');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'identifier')) {
                $table->dropColumn('identifier');
            }
        });
    }
};
