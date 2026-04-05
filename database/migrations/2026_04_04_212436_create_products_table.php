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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->unsignedBigInteger('price'); // prix en DA (centimes ou entier)
            $table->unsignedBigInteger('price_promo')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('in_stock')->default(true);
            $table->boolean('is_custom')->default(false); // sur commande
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('images')->nullable(); // tableau de chemins
            $table->string('main_image')->nullable();
            $table->json('dimensions')->nullable(); // {largeur, hauteur, profondeur}
            $table->string('material')->nullable();
            $table->string('color')->nullable();
            $table->integer('delivery_days')->default(7); // délai livraison estimé
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
