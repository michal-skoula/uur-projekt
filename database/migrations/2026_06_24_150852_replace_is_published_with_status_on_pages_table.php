<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('slug');
        });

        DB::table('pages')->where('is_published', true)->update(['status' => 'published']);

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('slug');
        });

        DB::table('pages')->where('status', 'published')->update(['is_published' => true]);

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
