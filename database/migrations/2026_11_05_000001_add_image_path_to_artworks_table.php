<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            // Path on the "public" disk, e.g. artworks/abc123.jpg
            $table->string('image_path')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
