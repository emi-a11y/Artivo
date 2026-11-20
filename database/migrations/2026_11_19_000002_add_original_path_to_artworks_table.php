<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            // Full-quality file on the PRIVATE disk (storage/app/artworks/originals/...), never web-accessible.
            // image_path keeps pointing at the small public preview, so every page works unchanged.
            $table->string('original_path')->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn('original_path');
        });
    }
};
