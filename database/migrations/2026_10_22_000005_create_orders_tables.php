<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('subtotal', 8, 2);
            $table->decimal('service_fee', 8, 2)->default(0);
            $table->decimal('total', 8, 2);
            $table->string('status', 20)->default('Pending'); // Pending, Paid, Refunded
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('artwork_id')->nullable()->constrained('artworks')->nullOnDelete();
            $table->string('title', 120);   // copied at purchase time so old orders survive an artwork delete
            $table->decimal('price', 8, 2);
            $table->string('license', 40);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
