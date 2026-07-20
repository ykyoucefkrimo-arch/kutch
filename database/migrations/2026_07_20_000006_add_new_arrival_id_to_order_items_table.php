<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Une ligne de commande peut desormais referencer soit un vrai produit
// (product_id), soit une entree "nouveauté" independante (new_arrival_id).
// Exactement l'un des deux est rempli (regle geree cote application).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('new_arrival_id')->nullable()->after('product_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('new_arrival_id');
        });
    }
};
