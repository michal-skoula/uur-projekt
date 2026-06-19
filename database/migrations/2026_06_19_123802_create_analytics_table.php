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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('subject');        // subject_type + subject_id (auto-indexed)
            $table->string('url');
            $table->string('visitor_hash', 64)->index();
            $table->string('referrer')->nullable();   // host only, e.g. "google.com"
            $table->string('device_type', 16)->nullable();   // desktop | mobile | tablet
            $table->char('country', 2)->nullable();   // ISO 3166-1 alpha-2, e.g. "CZ"
            $table->timestamps();
            $table->index('created_at');              // for time-series widgets
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
