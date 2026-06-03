<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->json('content')->default('[]');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('parent_id', name: 'fk_pages_parent_id')
                ->references('id')
                ->on('pages')
                ->nullOnDelete();

            $table->index(['parent_id'], name: 'idx_pages_parent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
