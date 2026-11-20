<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ways to pay: the admin uploads one QR image per method (GCash, Maya, bank...).
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->unique();
            $table->string('account_name', 80)->nullable();
            $table->string('account_number', 60)->nullable();
            $table->string('qr_path');                      // storage/app/public/payment-qr/...
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // What the buyer sends after paying, saved on the order itself.
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('status')->constrained('payment_methods')->nullOnDelete();
            $table->string('payment_reference', 60)->nullable()->unique()->after('payment_method_id');
            $table->string('payment_proof_path')->nullable()->after('payment_reference');   // receipt screenshot
            $table->timestamp('payment_submitted_at')->nullable()->after('payment_proof_path');
            $table->string('payment_note', 200)->nullable()->after('payment_submitted_at');  // admin's reason when rejected
            $table->timestamp('paid_at')->nullable()->after('payment_note');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropUnique(['payment_reference']);
            $table->dropColumn(['payment_reference', 'payment_proof_path', 'payment_submitted_at', 'payment_note', 'paid_at']);
        });
        Schema::dropIfExists('payment_methods');
    }
};
