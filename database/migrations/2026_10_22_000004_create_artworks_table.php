<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->foreignId('artist_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('genre_id')->constrained('genres')->restrictOnDelete();
            $table->decimal('price', 8, 2);
            $table->string('file_type', 60)->nullable();
            $table->string('resolution', 60)->nullable();
            $table->string('file_size', 30)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('Published'); // Published, Pending, Draft
            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedInteger('reviews')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
