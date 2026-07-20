<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Le flag Product::is_new est remplace par la table new_arrivals,
// independante des produits (voir 2026_07_20_000001_create_new_arrivals_table).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_new');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_new')->default(false)->after('is_featured');
        });
    }
};
