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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // EB-2026-XXXXX
            // Informations client
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_phone2')->nullable();
            $table->string('customer_email')->nullable();
            // Adresse livraison
            $table->foreignId('wilaya_id')->constrained();
            $table->foreignId('commune_id')->constrained();
            $table->text('address')->nullable();
            // Statut & paiement
            $table->enum('status', [
                'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'
            ])->default('pending');
            $table->enum('payment_method', ['cash_on_delivery'])->default('cash_on_delivery');
            // Montants en DA
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->unsignedBigInteger('total');
            // Notes
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            // Anti-fraude
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->unsignedTinyInteger('fraud_score')->default(0); // 0-100
            $table->boolean('is_flagged')->default(false);
            // Honeypot
            $table->boolean('honeypot_triggered')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
