<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Chaque nouveauté obtient sa propre page publique (comme une fiche produit) :
// on lui donne un slug unique et une description. Le champ "link" (URL libre
// saisie a la main) est retire au profit de cette page generee.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('new_arrivals', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->text('description')->nullable()->after('price');
        });

        Schema::table('new_arrivals', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }

    public function down(): void
    {
        Schema::table('new_arrivals', function (Blueprint $table) {
            $table->dropColumn(['slug', 'description']);
            $table->string('link')->nullable();
        });
    }
};
