<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'receipt_emailed_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->timestamp('receipt_emailed_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'receipt_emailed_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('receipt_emailed_at');
            });
        }
    }
};
