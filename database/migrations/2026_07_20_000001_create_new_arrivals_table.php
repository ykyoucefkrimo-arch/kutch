<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Entrees "Nouveaute" independantes des produits reels : l'admin saisit un
// nom, une categorie (optionnelle) et une image propres a l'entree, sans
// creer de fiche produit.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_arrivals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image_path')->nullable();
            $table->string('link')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_arrivals');
    }
};
