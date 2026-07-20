<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Aligne le schema de new_arrivals sur celui de products (memes champs a
// remplir), tout en restant une table totalement independante et dediee a
// l'affichage "Nouveautes".
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('new_arrivals', function (Blueprint $table) {
            $table->renameColumn('image_path', 'main_image');
        });

        Schema::table('new_arrivals', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique()->after('slug');
            $table->text('short_description')->nullable()->after('description');
            $table->unsignedBigInteger('price_promo')->nullable()->after('price');
            $table->unsignedInteger('stock')->default(0)->after('price_promo');
            $table->boolean('in_stock')->default(true)->after('stock');
            $table->boolean('is_custom')->default(false)->after('in_stock');
            $table->json('images')->nullable()->after('main_image');
            $table->string('material')->nullable()->after('images');
            $table->string('color')->nullable()->after('material');
            $table->integer('delivery_days')->default(7)->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('new_arrivals', function (Blueprint $table) {
            $table->dropColumn([
                'sku', 'short_description', 'price_promo', 'stock', 'in_stock',
                'is_custom', 'images', 'material', 'color', 'delivery_days',
            ]);
        });

        Schema::table('new_arrivals', function (Blueprint $table) {
            $table->renameColumn('main_image', 'image_path');
        });
    }
};
