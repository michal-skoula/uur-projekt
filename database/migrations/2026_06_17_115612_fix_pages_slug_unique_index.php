<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate NULL slugs (homepage) to empty string so the standard unique
        // index enforces uniqueness across all slugs including the homepage.
        DB::table('pages')->whereNull('slug')->update(['slug' => '']);

        Schema::table('pages', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->default('')->change();
        });
    }

    public function down(): void
    {
        // Make nullable first, then restore NULLs — order matters in SQL.
        Schema::table('pages', function (Blueprint $table) {
            $table->string('slug')->nullable()->default(null)->change();
        });

        DB::table('pages')->where('slug', '')->update(['slug' => null]);
    }
};
