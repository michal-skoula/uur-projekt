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
        Schema::table('news', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('slug');
        });

        // Posts that were already publicly visible (a past publish date) become
        // Published; everything else (no date or a future date) stays Draft.
        DB::table('news')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->update(['status' => 'published']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
